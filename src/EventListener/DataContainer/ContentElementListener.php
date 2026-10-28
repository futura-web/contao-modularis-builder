<?php

declare(strict_types=1);

namespace Futuraweb\ContaoModularisBuilder\EventListener\DataContainer;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Input;
use Contao\StringUtil;
use Contao\Validator;
use Doctrine\DBAL\Connection;
use Futuraweb\ContaoModularisBuilder\Snippet\FieldDefinitionFactory;
use Futuraweb\ContaoModularisBuilder\Snippet\Placeholder;
use Futuraweb\ContaoModularisBuilder\Snippet\SnippetParser;

/**
 * Registriert für das Inhaltselement "modularis_element" die Felder der gewählten
 * Vorlage dynamisch im DCA und speichert deren Werte gesammelt als JSON in
 * tl_content.modularisData.
 */
class ContentElementListener
{
    public const TYPE = 'modularis_element';

    /** @var array<int, array<string, string>> Geladene JSON-Daten je Datensatz-ID */
    private array $data = [];

    /** @var array<int, array<string, string>> Gesammelte neue Werte je Datensatz-ID */
    private array $pending = [];

    /** @var array<int, bool> Vorlage wurde in diesem Request gewechselt */
    private array $templateSwitched = [];

    public function __construct(
        private readonly Connection $connection,
        private readonly SnippetParser $parser,
        private readonly FieldDefinitionFactory $fieldFactory,
    ) {
    }

    #[AsCallback('tl_content', 'config.onload')]
    public function registerDynamicFields(DataContainer|null $dc = null): void
    {
        if (!$dc || !$dc->id || 'edit' !== Input::get('act')) {
            return;
        }

        $id = (int) $dc->id;

        $row = $this->connection->fetchAssociative(
            'SELECT type, modularisTemplate FROM tl_content WHERE id = ?',
            [$id],
        );

        if (!$row || self::TYPE !== $row['type']) {
            return;
        }

        $templateId = (int) $row['modularisTemplate'];

        // Bei submitOnChange die gerade gewählte Vorlage bevorzugen
        if ('tl_content' === Input::post('FORM_SUBMIT') && null !== Input::post('modularisTemplate')) {
            $posted = (int) Input::post('modularisTemplate');

            if ($posted !== $templateId) {
                $this->templateSwitched[$id] = true;
                $templateId = $posted;
            }
        }

        if ($templateId < 1) {
            return;
        }

        $template = $this->connection->fetchAssociative(
            'SELECT html, imgSize FROM tl_modularis_template WHERE id = ?',
            [$templateId],
        );

        if (!$template) {
            return;
        }

        $placeholders = $this->parser->parse((string) $template['html']);

        if ([] === $placeholders) {
            return;
        }

        $fieldNames = [];

        foreach ($placeholders as $placeholder) {
            foreach ($this->fieldFactory->create($placeholder) as $suffix => $definition) {
                $fieldName = $placeholder->getFieldName().$suffix;
                $dataKey = $placeholder->name.$suffix;
                $kind = $this->fieldKind($placeholder, $suffix);
                $default = $this->defaultValue($placeholder, $suffix, $template['imgSize']);

                $definition['load_callback'] = [fn ($value, DataContainer $dc) => $this->loadValue($dataKey, $kind, $default, $dc)];
                $definition['save_callback'] = [fn ($value, DataContainer $dc) => $this->saveValue($dataKey, $kind, $value, $dc)];

                $GLOBALS['TL_DCA']['tl_content']['fields'][$fieldName] = $definition;
                $fieldNames[] = $fieldName;
            }
        }

        PaletteManipulator::create()
            ->addLegend('snippet_fields_legend', 'snippet_legend', PaletteManipulator::POSITION_AFTER)
            ->addField($fieldNames, 'snippet_fields_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette(self::TYPE, 'tl_content')
        ;
    }

    #[AsCallback('tl_content', 'config.onsubmit')]
    public function persistSnippetData(DataContainer $dc): void
    {
        $id = (int) $dc->id;

        if (!isset($this->pending[$id]) || !empty($this->templateSwitched[$id])) {
            return;
        }

        $data = array_merge($this->loadData($id), $this->pending[$id]);
        unset($this->pending[$id]);

        $this->connection->update(
            'tl_content',
            ['modularisData' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
            ['id' => $id],
        );

        $this->data[$id] = $data;
    }

    private function fieldKind(Placeholder $placeholder, string $suffix): string
    {
        if ('' === $suffix) {
            return match ($placeholder->type) {
                'image' => 'image',
                'checkbox' => 'checkbox',
                default => 'text',
            };
        }

        return '_fullsize' === $suffix ? 'checkbox' : 'text';
    }

    /**
     * Vorbelegung für neue oder vor diesem Update gespeicherte Datensätze: eigene
     * Angabe im Platzhalter, sonst die Bildgröße der Vorlage.
     */
    private function defaultValue(Placeholder $placeholder, string $suffix, mixed $templateImgSize): mixed
    {
        return match ($suffix) {
            '_size' => $placeholder->imageSize ?? (StringUtil::deserialize($templateImgSize) ?: ''),
            '_fullsize' => $placeholder->lightbox ? '1' : '',
            default => '',
        };
    }

    private function loadValue(string $dataKey, string $kind, mixed $default, DataContainer $dc): mixed
    {
        $value = $this->loadData((int) $dc->id)[$dataKey] ?? $default;

        if ('image' === $kind) {
            return \is_string($value) && Validator::isStringUuid($value) ? StringUtil::uuidToBin($value) : '';
        }

        return $value;
    }

    private function saveValue(string $dataKey, string $kind, mixed $value, DataContainer $dc): mixed
    {
        $normalized = match ($kind) {
            'image' => Validator::isBinaryUuid($value) ? StringUtil::binToUuid($value) : (Validator::isStringUuid((string) $value) ? (string) $value : ''),
            'checkbox' => $value ? '1' : '',
            default => \is_array($value) ? serialize($value) : (string) $value,
        };

        $this->pending[(int) $dc->id][$dataKey] = $normalized;

        // Den ursprünglich geladenen Wert zurückgeben, damit DC_Table keine
        // (nicht existierende) Spalte aktualisiert. Gespeichert wird im onsubmit.
        return $dc->value;
    }

    /**
     * @return array<string, string>
     */
    private function loadData(int $id): array
    {
        if (!isset($this->data[$id])) {
            $json = $this->connection->fetchOne('SELECT modularisData FROM tl_content WHERE id = ?', [$id]);
            $decoded = json_decode((string) $json, true);

            $this->data[$id] = \is_array($decoded) ? $decoded : [];
        }

        return $this->data[$id];
    }
}

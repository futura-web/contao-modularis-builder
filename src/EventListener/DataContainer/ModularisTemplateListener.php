<?php

declare(strict_types=1);

namespace Futuraweb\ContaoModularisBuilder\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Slug\Slug;
use Contao\Controller;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Futuraweb\ContaoModularisBuilder\Snippet\Placeholder;
use Futuraweb\ContaoModularisBuilder\Snippet\SnippetParser;

class ModularisTemplateListener
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Slug $slug,
        private readonly SnippetParser $parser,
    ) {
    }

    #[AsCallback('tl_modularis_template', 'fields.alias.save')]
    public function generateAlias(mixed $value, DataContainer $dc): string
    {
        $value = (string) $value;

        $aliasExists = fn (string $alias): bool => false !== $this->connection->fetchOne(
            'SELECT id FROM tl_modularis_template WHERE alias = ? AND id != ?',
            [$alias, (int) $dc->id],
        );

        if ('' === $value) {
            $name = (string) (Input::post('name') ?: ($dc->getCurrentRecord()['name'] ?? ''));

            return $this->slug->generate($name ?: 'snippet', [], $aliasExists);
        }

        if (preg_match('/^[1-9]\d*$/', $value)) {
            throw new \RuntimeException(sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $value));
        }

        if ($aliasExists($value)) {
            throw new \RuntimeException(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $value));
        }

        return $value;
    }

    #[AsCallback('tl_modularis_template', 'list.label.label')]
    public function formatLabel(array $row, string $label, DataContainer $dc, array $args): string
    {
        $placeholders = $this->parser->parse((string) $row['html']);

        $summary = implode(', ', array_map(
            static fn (Placeholder $p): string => $p->type.':'.$p->name,
            $placeholders,
        ));

        $out = '<strong>'.StringUtil::specialchars((string) $row['name']).'</strong>';

        if ('' !== (string) $row['alias']) {
            $out .= ' <span class="tl_gray">['.StringUtil::specialchars((string) $row['alias']).']</span>';
        }

        if ('' !== $summary) {
            $out .= '<br><span class="tl_gray" style="font-size:.9em">'.StringUtil::specialchars($summary).'</span>';
        }

        return $out;
    }

    #[AsCallback('tl_modularis_template', 'list.operations.delete.button')]
    public function deleteButton(array $row, string|null $href, string $label, string $title, string|null $icon, string $attributes): string
    {
        if ($this->countUsages((int) $row['id']) > 0) {
            return Image::getHtml(str_replace('.svg', '--disabled.svg', (string) $icon)).' ';
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            Controller::addToUrl($href.'&amp;id='.$row['id']),
            StringUtil::specialchars($title),
            $attributes,
            Image::getHtml((string) $icon, $label),
        );
    }

    #[AsCallback('tl_modularis_template', 'config.ondelete')]
    public function preventDeleteIfUsed(DataContainer $dc): void
    {
        $id = (int) $dc->id;
        $usages = $this->countUsages($id);

        if ($usages < 1) {
            return;
        }

        $name = (string) $this->connection->fetchOne('SELECT name FROM tl_modularis_template WHERE id = ?', [$id]);

        Message::addError(sprintf($GLOBALS['TL_LANG']['tl_modularis_template']['deleteBlocked'], $name, $usages));

        Controller::redirect(Controller::addToUrl('', true, ['act', 'id']));
    }

    private function countUsages(int $templateId): int
    {
        return (int) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_content WHERE type = 'modularis_element' AND modularisTemplate = ?",
            [$templateId],
        );
    }

    #[AsCallback('tl_modularis_template', 'fields.syntaxHelp.input_field')]
    public function renderSyntaxHelp(DataContainer $dc): string
    {
        $lang = $GLOBALS['TL_LANG']['tl_modularis_template']['syntax'] ?? [];
        $structure = $lang['structure'] ?? [];

        return sprintf(
            '<div class="widget long clr"><h3><label>%s</label></h3>'
            .'<div class="tl_help" style="padding:8px 12px;border:1px solid #d0d0d0;border-radius:2px;background:#f9f9f9;font-size:.9em;line-height:1.5">'
            .'<p style="margin:0 0 6px"><strong>%s:</strong> <code>%s</code></p>%s'
            .'<p style="margin:12px 0 6px"><strong>%s:</strong></p>%s'
            .'<p style="margin:8px 0 0">%s</p></div></div>',
            StringUtil::specialchars($lang['title'] ?? 'Syntax'),
            StringUtil::specialchars($structure['title'] ?? ''),
            StringUtil::specialchars($structure['pattern'] ?? ''),
            $this->renderHelpTable($structure['rows'] ?? []),
            StringUtil::specialchars($lang['examples'] ?? ''),
            $this->renderHelpTable($lang['rows'] ?? []),
            StringUtil::specialchars($lang['note'] ?? ''),
        );
    }

    /**
     * @param array<int, array{0: string, 1: string}> $rows
     */
    private function renderHelpTable(array $rows): string
    {
        $html = '';

        foreach ($rows as [$code, $text]) {
            $html .= sprintf(
                '<tr><td style="white-space:nowrap;padding:3px 12px 3px 0;vertical-align:top"><code>%s</code></td><td style="padding:3px 0">%s</td></tr>',
                StringUtil::specialchars($code),
                StringUtil::specialchars($text),
            );
        }

        return '<table style="border-collapse:collapse">'.$html.'</table>';
    }
}

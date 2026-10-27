<?php

declare(strict_types=1);

namespace Futura\ContaoModularisBuilder\Snippet;

use Contao\BackendUser;
use Contao\System;

/**
 * Baut aus einem Platzhalter die DCA-Felddefinition(en) für tl_content.
 *
 * Ein Platzhalter erzeugt in der Regel genau ein Feld (Suffix ""). Ein
 * image-Platzhalter erzeugt zusätzlich die von Contaos eigenem Bildelement
 * bekannten Begleitfelder (Bildgröße, Großansicht, Alt-Text, Titel,
 * Bildlink, Bildunterschrift), damit Redakteure dieselbe Kontrolle haben
 * wie bei einem nativen Bild-Inhaltselement.
 */
final class FieldDefinitionFactory
{
    public const IMAGE_SUFFIXES = ['_size', '_fullsize', '_alt', '_title', '_url', '_caption'];

    /**
     * @return array<string, array<string, mixed>> DCA-Felddefinitionen, indiziert nach Namenssuffix ("" für das Hauptfeld)
     */
    public function create(Placeholder $placeholder): array
    {
        if ('image' === $placeholder->type) {
            return $this->createImageFields($placeholder);
        }

        return ['' => $this->createSingleField($placeholder)];
    }

    /**
     * @return array<string, mixed>
     */
    private function createSingleField(Placeholder $placeholder): array
    {
        $definition = match ($placeholder->type) {
            'textarea' => [
                'inputType' => 'textarea',
                'eval' => ['tl_class' => 'clr'],
            ],
            'html' => [
                'inputType' => 'textarea',
                'eval' => [
                    'allowHtml' => true,
                    'preserveTags' => true,
                    'decodeEntities' => true,
                    'rte' => 'ace|html',
                    'class' => 'monospace',
                    'tl_class' => 'clr',
                ],
            ],
            'rte' => [
                'inputType' => 'textarea',
                'eval' => [
                    'rte' => 'tinyMCE',
                    'helpwizard' => true,
                    'tl_class' => 'clr',
                ],
                'explanation' => 'insertTags',
            ],
            'url' => [
                'inputType' => 'text',
                'eval' => [
                    'rgxp' => 'url',
                    'decodeEntities' => true,
                    'maxlength' => 2048,
                    'dcaPicker' => true,
                    'tl_class' => 'w50',
                ],
            ],
            'select' => [
                'inputType' => 'select',
                'options' => $placeholder->options,
                'eval' => [
                    'includeBlankOption' => true,
                    'chosen' => true,
                    'tl_class' => 'w50',
                ],
            ],
            'checkbox' => [
                'inputType' => 'checkbox',
                'eval' => ['tl_class' => 'w50 m12'],
            ],
            'number' => [
                'inputType' => 'text',
                'eval' => [
                    'rgxp' => 'digit',
                    'maxlength' => 32,
                    'tl_class' => 'w50',
                ],
            ],
            default => [
                'inputType' => 'text',
                'eval' => [
                    'maxlength' => 255,
                    'tl_class' => 'w50',
                ],
            ],
        };

        if (null !== $placeholder->cssClass) {
            $definition['eval']['tl_class'] = str_replace('+', ' ', $placeholder->cssClass);
        }

        $definition['label'] = [$placeholder->label, ''];
        $definition['exclude'] = true;

        return $definition;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function createImageFields(Placeholder $placeholder): array
    {
        $main = [
            'inputType' => 'fileTree',
            'eval' => [
                'filesOnly' => true,
                'fieldType' => 'radio',
                'extensions' => '%contao.image.valid_extensions%',
                'tl_class' => $placeholder->cssClass ? str_replace('+', ' ', $placeholder->cssClass) : 'clr',
            ],
            'label' => [$placeholder->label, ''],
            'exclude' => true,
        ];

        $fields = ['' => $main];

        foreach (self::IMAGE_SUFFIXES as $suffix) {
            $fields[$suffix] = $this->createImageCompanionField($placeholder, $suffix);
        }

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    private function createImageCompanionField(Placeholder $placeholder, string $suffix): array
    {
        $lang = $GLOBALS['TL_LANG']['tl_content']['snippet_image'][substr($suffix, 1)] ?? ['', ''];

        $definition = match ($suffix) {
            '_size' => [
                'inputType' => 'imageSize',
                'reference' => &$GLOBALS['TL_LANG']['MSC'],
                'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50 clr'],
                'options_callback' => static fn () => System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance()),
            ],
            '_fullsize' => [
                'inputType' => 'checkbox',
                'eval' => ['tl_class' => 'w50 m12'],
            ],
            '_alt' => [
                'inputType' => 'text',
                'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            ],
            '_title' => [
                'inputType' => 'text',
                'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            ],
            '_url' => [
                'inputType' => 'text',
                'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'dcaPicker' => true, 'tl_class' => 'w50 clr'],
            ],
            '_caption' => [
                'inputType' => 'text',
                'eval' => ['maxlength' => 255, 'allowHtml' => true, 'tl_class' => 'w50'],
            ],
            default => ['inputType' => 'text', 'eval' => ['tl_class' => 'w50']],
        };

        $definition['label'] = [trim($placeholder->label.' – '.$lang[0], ' –'), $lang[1] ?? ''];
        $definition['exclude'] = true;

        return $definition;
    }
}

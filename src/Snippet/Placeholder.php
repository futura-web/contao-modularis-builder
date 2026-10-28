<?php

declare(strict_types=1);

namespace Futuraweb\ContaoModularisBuilder\Snippet;

/**
 * Ein Platzhalter im Snippet-HTML, z. B. [[image:bild|Bild]].
 */
final class Placeholder
{
    public const TYPES = ['text', 'textarea', 'html', 'rte', 'image', 'url', 'select', 'checkbox', 'number'];

    public const FIELD_PREFIX = 'modularis_';

    /**
     * @param array<string, string>        $options   Schlüssel => Label (nur für select)
     * @param string|null                  $cssClass  Contao-Breitenklasse(n) fürs Backend-Formular, z. B. "w50" oder "w33+clr"
     * @param array{0:int,1:int,2:string}|int|string|null $imageSize Eigene Bildgröße (nur für image), überschreibt die Vorlagen-Bildgröße
     * @param bool                         $lightbox  Bild in der Lightbox öffnen (nur für image)
     */
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $label,
        public readonly array $options = [],
        public readonly string|null $cssClass = null,
        public readonly array|int|string|null $imageSize = null,
        public readonly bool $lightbox = false,
    ) {
    }

    public function getFieldName(): string
    {
        return self::FIELD_PREFIX.$this->name;
    }
}

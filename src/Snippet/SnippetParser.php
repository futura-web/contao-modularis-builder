<?php

declare(strict_types=1);

namespace Futura\ContaoModularisBuilder\Snippet;

/**
 * Erkennt Platzhalter im Snippet-HTML und ersetzt sie beim Rendern.
 *
 * Syntax:
 *   [[name]]                                Textfeld
 *   [[typ:name]]                            Feld vom Typ "typ"
 *   [[typ:name.w50]]                        mit Contao-Breitenklasse im Backend-Formular
 *   [[typ:name|Label]]                      mit Beschriftung im Backend
 *   [[select:name|Label|a,b=Beta]]          Auswahl mit Optionen
 *   [[image:name|Label|800x600_box]]        eigene Bildgröße statt der Vorlagen-Bildgröße
 *   [[image:name|Label|800x600_box,lightbox]] zusätzlich in der Lightbox öffnen
 *   [[if:name]] ... [[/if]]                 Block nur ausgeben, wenn Wert gesetzt
 */
final class SnippetParser
{
    private const PLACEHOLDER = '/\[\[(?:(?<type>[a-z]+):)?(?<name>[a-z][a-z0-9_]*)(?:\.(?<cssclass>[a-z0-9+]+))?(?:\|(?<label>[^|\]]*))?(?:\|(?<options>[^\]]*))?\]\]/i';

    private const CONDITION = '/\[\[if:(?<name>[a-z][a-z0-9_]*)\]\](?<body>.*?)\[\[\/if\]\]/is';

    /**
     * @return array<string, Placeholder> Platzhalter, indiziert nach Name, in Reihenfolge des Auftretens
     */
    public function parse(string $html): array
    {
        $placeholders = [];

        preg_match_all(self::PLACEHOLDER, $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $type = strtolower($match['type'] ?? '');

            if ('if' === $type) {
                continue;
            }

            if (!\in_array($type, Placeholder::TYPES, true)) {
                $type = 'text';
            }

            $name = strtolower($match['name']);

            if (isset($placeholders[$name])) {
                continue;
            }

            $label = trim($match['label'] ?? '');

            if ('' === $label) {
                $label = ucfirst(str_replace('_', ' ', $name));
            }

            $cssClass = trim($match['cssclass'] ?? '');
            $raw = $match['options'] ?? '';

            [$imageSize, $lightbox] = 'image' === $type ? $this->parseImageSize($raw) : [null, false];

            $placeholders[$name] = new Placeholder(
                $name,
                $type,
                $label,
                'select' === $type ? $this->parseOptions($raw) : [],
                '' === $cssClass ? null : strtolower($cssClass),
                $imageSize,
                $lightbox,
            );
        }

        // Bedingungen ohne eigenes Feld bekommen eine Checkbox
        preg_match_all(self::CONDITION, $html, $conditions, PREG_SET_ORDER);

        foreach ($conditions as $condition) {
            $name = strtolower($condition['name']);

            if (!isset($placeholders[$name])) {
                $placeholders[$name] = new Placeholder($name, 'checkbox', ucfirst(str_replace('_', ' ', $name)));
            }
        }

        return $placeholders;
    }

    /**
     * @param array<string, string> $rendered Fertig gerenderter HTML-Wert je Platzhaltername
     * @param array<string, bool>   $isEmpty  Ob der Wert als "leer" gilt (für [[if:…]])
     */
    public function render(string $html, array $rendered, array $isEmpty): string
    {
        $html = preg_replace_callback(
            self::CONDITION,
            static fn (array $m): string => ($isEmpty[strtolower($m['name'])] ?? true) ? '' : $m['body'],
            $html,
        );

        $html = preg_replace_callback(
            self::PLACEHOLDER,
            static function (array $m) use ($rendered): string {
                if ('if' === strtolower($m['type'] ?? '')) {
                    return '';
                }

                return $rendered[strtolower($m['name'])] ?? '';
            },
            $html,
        );

        // Verwaiste schließende Tags entfernen
        return str_replace('[[/if]]', '', $html);
    }

    /**
     * Erkennt eine eigene Bildgröße für einen image-Platzhalter, z. B. "800x600_box,lightbox"
     * oder die ID einer bestehenden Contao-Bildgröße, z. B. "12,lightbox".
     *
     * @return array{0: array{0:int,1:int,2:string}|int|null, 1: bool}
     */
    private function parseImageSize(string $raw): array
    {
        $size = null;
        $lightbox = false;

        foreach (explode(',', $raw) as $token) {
            $token = trim($token);

            if ('' === $token) {
                continue;
            }

            if ('lightbox' === strtolower($token)) {
                $lightbox = true;
            } elseif (preg_match('/^(\d+)x(\d+)(?:_([a-z_]+))?$/i', $token, $m)) {
                $size = [(int) $m[1], (int) $m[2], strtolower($m[3] ?? '')];
            } elseif (ctype_digit($token)) {
                $size = (int) $token;
            }
        }

        return [$size, $lightbox];
    }

    /**
     * @return array<string, string>
     */
    private function parseOptions(string $raw): array
    {
        $options = [];

        foreach (explode(',', $raw) as $option) {
            $option = trim($option);

            if ('' === $option) {
                continue;
            }

            [$key, $label] = array_pad(explode('=', $option, 2), 2, null);
            $key = trim($key);
            $options[$key] = trim($label ?? $key);
        }

        return $options;
    }
}

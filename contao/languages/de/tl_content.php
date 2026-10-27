<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_content']['modularisTemplate'] = ['Modularis-Vorlage', 'Bitte eine Vorlage wählen. Die zugehörigen Felder erscheinen anschließend.'];
$GLOBALS['TL_LANG']['tl_content']['snippet_legend'] = 'Modularis';
$GLOBALS['TL_LANG']['tl_content']['snippet_fields_legend'] = 'Modularis-Inhalte';

// Begleitfelder, die automatisch neben jedem Bild-Platzhalter erscheinen
$GLOBALS['TL_LANG']['tl_content']['snippet_image'] = [
    'size' => ['Bildgröße', 'Hier können Sie die Abmessungen des Bildes und den Skalierungsmodus festlegen. Ohne Auswahl wird die Bildgröße der Vorlage verwendet.'],
    'fullsize' => ['Großansicht/Neues Fenster', 'Großansicht des Bildes in einer Lightbox bzw. den Link in einem neuen Browserfenster öffnen.'],
    'alt' => ['Alternativer Text', 'Hier können Sie einen alternativen Text für das Bild eingeben (alt-Attribut).'],
    'title' => ['Bildtitel', 'Hier können Sie den Titel des Bildes eingeben (title-Attribut).'],
    'url' => ['Bildlink-Adresse', 'Eine eigene Bildlink-Adresse überschreibt den Lightbox-Link, sodass das Bild nicht mehr in der Großansicht dargestellt werden kann.'],
    'caption' => ['Bildunterschrift', 'Hier können Sie einen kurzen Text eingeben, der unterhalb des Bildes angezeigt wird.'],
];

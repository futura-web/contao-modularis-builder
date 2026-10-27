<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_modularis_template']['name'] = ['Name', 'Interner Name der Vorlage, erscheint in der Auswahl im Inhaltselement.'];
$GLOBALS['TL_LANG']['tl_modularis_template']['alias'] = ['Alias', 'Eindeutiger Kurzname. Leer lassen, um ihn automatisch zu erzeugen.'];
$GLOBALS['TL_LANG']['tl_modularis_template']['description'] = ['Beschreibung', 'Optionale Notiz für Redakteure.'];
$GLOBALS['TL_LANG']['tl_modularis_template']['html'] = ['HTML-Snippet', 'Das HTML mit Platzhaltern in doppelten eckigen Klammern, z. B. [[text:headline|Überschrift]].'];
$GLOBALS['TL_LANG']['tl_modularis_template']['imgSize'] = ['Bildgröße', 'Standard für alle Bild-Platzhalter (Typ image) dieser Vorlage, falls der Platzhalter keine eigene Größe angibt.'];

$GLOBALS['TL_LANG']['tl_modularis_template']['title_legend'] = 'Vorlage';
$GLOBALS['TL_LANG']['tl_modularis_template']['snippet_legend'] = 'HTML-Snippet';
$GLOBALS['TL_LANG']['tl_modularis_template']['image_legend'] = 'Bildeinstellungen';

$GLOBALS['TL_LANG']['tl_modularis_template']['syntax'] = [
    'title' => 'Platzhalter-Syntax',
    'structure' => [
        'title' => 'Aufbau',
        'pattern' => '[[typ:name.cssklasse|Beschriftung]]',
        'rows' => [
            ['typ', 'Feldtyp: text, textarea, rte, html, image, url, number, checkbox, select. Optional, Standard ist text.'],
            ['name', 'Technischer Feldname: Kleinbuchstaben, Ziffern, Unterstrich. Pflicht.'],
            ['.cssklasse', 'Breite des Felds im Backend-Formular, direkt an den Namen angehängt, z. B. .w50. Optional, jeder Feldtyp hat eine sinnvolle Standardbreite.'],
            ['Beschriftung', 'Text über dem Eingabefeld im Backend. Optional, Standard ist der Name.'],
        ],
    ],
    'examples' => 'Beispiele (die Feldnamen darin sind frei erfunden, du vergibst eigene)',
    'rows' => [
        ['[[slogan]]', 'Kurzform ohne Typ und Beschriftung: Textfeld, im Backend beschriftet mit "Slogan"'],
        ['[[text:slogan|Werbeslogan]]', 'Langform: dasselbe Textfeld, im Backend beschriftet mit "Werbeslogan"'],
        ['[[textarea:intro|Einleitung]]', 'Mehrzeiliger Text, Zeilenumbrüche werden zu <br>'],
        ['[[rte:inhalt|Fließtext]]', 'Formatierter Text mit TinyMCE'],
        ['[[html:code|Embed-Code]]', 'Freies HTML mit Code-Editor'],
        ['[[image:bild|Bild]]', 'Bild aus der Dateiverwaltung, Ausgabe als <figure> in der eingestellten Bildgröße'],
        ['[[image:bild|Bild|800x600_box]]', 'Eigene Bildgröße statt der Vorlagen-Bildgröße'],
        ['[[image:bild|Bild|800x600_box,lightbox]]', 'Zusätzlich: Bild öffnet beim Klick in der Lightbox'],
        ['[[url:link|Link-Ziel]]', 'Link-Adresse mit Seiten- und Dateiauswahl'],
        ['[[number:anzahl|Anzahl]]', 'Ganzzahl'],
        ['[[checkbox:rahmen|Mit Rahmen]]', 'Ja/Nein, Ausgabe 1 oder leer'],
        ['[[select:variante|Variante|hell,dunkel]]', 'Auswahlfeld mit den Optionen hell und dunkel'],
        ['[[select:farbe|Farbe|red=Rot,blue=Blau]]', 'Auswahlfeld; vor dem = steht der Wert, dahinter das Label'],
        ['[[if:link]] Inhalt [[/if]]', 'Block nur ausgeben, wenn das Feld "link" befüllt bzw. angehakt ist'],
        ['[[text:vorname.w50|Vorname]] [[text:nachname.w50|Nachname]]', 'Zwei Felder nebeneinander, je halbe Breite'],
        ['[[text:ort.w33|Ort]]', 'Drittelbreite, nützlich für drei Felder in einer Zeile'],
    ],
    'note' => 'Namen bestehen aus Kleinbuchstaben, Ziffern und Unterstrich. Gängige Breitenklassen: w25, w33, w50, w66, w75, clr (erzwingt eine neue Zeile). Mehrere Klassen mit + verbinden, z. B. .w33+clr. Insert-Tags wie {{link_url::12}} funktionieren weiterhin. Derselbe Platzhalter darf mehrfach vorkommen und wird nur einmal abgefragt.',
];

$GLOBALS['TL_LANG']['tl_modularis_template']['deleteBlocked'] = 'Die Vorlage "%s" wird noch von %d Inhaltselement(en) verwendet und kann nicht gelöscht werden.';

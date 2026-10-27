<img src="futura-logo.svg" alt="Futura Web" width="220">

# Contao Modularis Builder

Wiederverwendbare HTML-Snippets mit Platzhaltern als Inhaltselemente für **Contao 5**.

Ein Projekt von [Futura Web](https://futura-web.de).

Ein Admin legt einmal eine HTML-Vorlage an, z. B. einen Embed-Code, eine Karte oder ein Layout-Fragment, und markiert die veränderlichen Stellen mit Platzhaltern. Redakteure wählen die Vorlage danach als normales Inhaltselement aus und füllen nur noch die Felder aus. HTML-Kenntnisse sind dafür nicht nötig.

Für wiederkehrende, strukturierte Bausteine entfällt damit die langwierige eigene Modulprogrammierung: kein eigener DCA, kein eigener Fragment-Controller, kein eigenes Twig-Template pro Baustein mehr. Die Vorlage im Backend übernimmt diese Arbeit.

## Installation

### Aus diesem Repository (empfohlen)

In der `composer.json` der Contao-Installation ein VCS-Repository eintragen:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/futura-web/contao-modularis-builder" }
]
```

```bash
composer require futura/contao-modularis-builder
vendor/bin/contao-console contao:migrate
vendor/bin/contao-console cache:clear
```

### Als lokales Paket

Für die Entwicklung am Bundle selbst, z. B. in `packages/contao-modularis-builder` der Contao-Installation:

```json
"repositories": [
    { "type": "path", "url": "packages/contao-modularis-builder" }
]
```

```bash
composer require futura/contao-modularis-builder:@dev
vendor/bin/contao-console contao:migrate
vendor/bin/contao-console cache:clear
```

## Benutzung

1. Backend → **Inhalte → Modularis** → neue Vorlage anlegen.
2. Einen sprechenden **Namen** vergeben, z. B. "Teaser-Kachel" oder "YouTube-Embed". Unter diesem Namen erscheint die Vorlage später in der Auswahl. Optional eine Beschreibung als Notiz für Redakteure ergänzen.
3. HTML einfügen und Platzhalter setzen.
4. Im Artikel ein Inhaltselement vom Typ **Modularis-Element** anlegen, im Dropdown die Vorlage nach Namen wählen, Felder befüllen.

### Platzhalter-Syntax

**Aufbau:** `[[typ:name.cssklasse|Beschriftung]]`

| Bestandteil | Bedeutung | Pflicht |
| --- | --- | --- |
| `typ` | Feldtyp: text, textarea, rte, html, image, url, number, checkbox, select | nein, Standard ist text |
| `name` | Technischer Feldname: Kleinbuchstaben, Ziffern, Unterstrich | ja |
| `.cssklasse` | Breite des Felds im Backend-Formular, direkt an den Namen angehängt | nein, jeder Feldtyp hat eine sinnvolle Standardbreite |
| `Beschriftung` | Text über dem Eingabefeld im Backend | nein, Standard ist der Name |

**Beispiele.** Die Feldnamen darin (`slogan`, `intro`, `bild` usw.) sind frei erfunden, du vergibst eigene.

| Beispiel | Bedeutung |
| --- | --- |
| `[[slogan]]` | Kurzform ohne Typ und Beschriftung: Textfeld, im Backend beschriftet mit "Slogan" |
| `[[text:slogan\|Werbeslogan]]` | Langform: dasselbe Textfeld, im Backend beschriftet mit "Werbeslogan" |
| `[[textarea:intro\|Einleitung]]` | Mehrzeiliger Text, Zeilenumbrüche werden zu `<br>` |
| `[[rte:inhalt\|Fließtext]]` | Formatierter Text mit TinyMCE |
| `[[html:code\|Embed-Code]]` | Freies HTML mit Code-Editor |
| `[[image:bild\|Bild]]` | Bild aus der Dateiverwaltung, Ausgabe als `<figure>` in der Bildgröße der Vorlage |
| `[[image:bild\|Bild\|800x600_box]]` | Eigene Bildgröße statt der Vorlagen-Bildgröße, hier 800×600 im Box-Modus |
| `[[image:bild\|Bild\|800x600_box,lightbox]]` | Zusätzlich: Bild öffnet beim Klick in der Lightbox |
| `[[url:link\|Link-Ziel]]` | Link-Adresse mit Seiten- und Dateiauswahl |
| `[[number:anzahl\|Anzahl]]` | Ganzzahl |
| `[[checkbox:rahmen\|Mit Rahmen]]` | Ja/Nein, Ausgabe `1` oder leer |
| `[[select:variante\|Variante\|hell,dunkel]]` | Auswahlfeld mit den Optionen hell und dunkel |
| `[[select:farbe\|Farbe\|red=Rot,blue=Blau]]` | Auswahlfeld, vor dem `=` steht der Wert, dahinter das Label |
| `[[if:link]] Inhalt [[/if]]` | Block nur ausgeben, wenn `link` befüllt oder angehakt ist |
| `[[text:vorname.w50\|Vorname]] [[text:nachname.w50\|Nachname]]` | Zwei Felder nebeneinander, je halbe Breite |
| `[[text:ort.w33\|Ort]]` | Drittelbreite, nützlich für drei Felder in einer Zeile |

Namen bestehen aus Kleinbuchstaben, Ziffern und Unterstrich. Derselbe Platzhalter darf mehrfach vorkommen und wird im Backend nur einmal abgefragt. Contao-Insert-Tags wie `{{link_url::12}}` funktionieren weiterhin, sowohl in der Vorlage als auch in den eingegebenen Werten.

### Breite der Backend-Felder

Jeder Feldtyp hat eine sinnvolle Standardbreite: einzeilige Felder wie `text`, `url`, `number` und `select` nehmen standardmäßig die halbe Zeile ein (`w50`), mehrzeilige und komplexere Felder wie `textarea`, `rte`, `html` und `image` die volle Breite.

Diese Standardbreite lässt sich pro Platzhalter überschreiben, indem eine Contao-CSS-Klasse mit einem Punkt an den Namen angehängt wird, z. B. `[[text:vorname.w33|Vorname]]`. Gängige Klassen:

| Klasse | Breite |
| --- | --- |
| `w25` | ein Viertel |
| `w33` | ein Drittel |
| `w50` | die Hälfte |
| `w66` | zwei Drittel |
| `w75` | drei Viertel |
| `clr` | erzwingt eine neue Zeile (volle Breite) |

Mehrere Klassen lassen sich mit `+` verbinden, z. B. `.w33+clr`, um ein drittelbreites Feld zu erzeugen, das trotzdem eine eigene Zeile beginnt. Felder mit derselben Breitenklasse, die direkt aufeinanderfolgen, stehen im Backend-Formular nebeneinander, genau wie bei Contaos eigenen Formularen.

### Beispiel: Teaser-Kachel

```html
<div class="teaser teaser--[[select:variante|Variante|hell,dunkel]]">
  [[image:bild|Bild]]
  <h3>[[text:titel|Titel]]</h3>
  <p>[[textarea:intro|Einleitung]]</p>
  [[if:link]]<a class="button" href="[[url:link|Link-Ziel]]">[[text:linktext|Linktext]]</a>[[/if]]
</div>
```

### Beispiel: YouTube-Embed

```html
<div class="video-wrapper">
  <iframe src="https://www.youtube-nocookie.com/embed/[[text:video_id|YouTube-Video-ID]]"
          title="[[text:titel|Titel]]" loading="lazy" allowfullscreen></iframe>
</div>
```

## Ausgabe

Werte werden je nach Typ behandelt:

- `text`, `url`, `number`, `select`, `checkbox`: HTML-Sonderzeichen werden maskiert.
- `textarea`: maskiert plus `nl2br`.
- `html`, `rte`: unverändert ausgegeben.
- `image`: über Contao Image Studio als `<figure>` mit `srcset`.

### Bildfelder im Inhaltselement

Ein `image`-Platzhalter erzeugt im Inhaltselement nicht nur die Dateiauswahl, sondern dieselben Begleitfelder wie Contaos eigenes Bild-Inhaltselement. Der Redakteur hat damit bei jedem Bild die volle Kontrolle, ganz ohne Vorlagen-Änderung:

| Feld | Bedeutung |
| --- | --- |
| Quelldatei | Dateiauswahl aus der Dateiverwaltung |
| Bildgröße | Abmessungen und Skalierungsmodus, wie beim nativen Contao-Bildfeld |
| Großansicht/Neues Fenster | Bild in der Lightbox öffnen, bzw. bei eigener Bildlink-Adresse den Link in einem neuen Fenster öffnen |
| Alternativer Text | Überschreibt den Alt-Text der Datei |
| Bildtitel | Überschreibt den Titel der Datei |
| Bildlink-Adresse | Eigenes Linkziel, überschreibt die Lightbox |
| Bildunterschrift | Text unterhalb des Bildes |

Lässt der Redakteur ein Feld leer, wird die Bildgröße in folgender Reihenfolge ermittelt: eigene Angabe im Platzhalter der Vorlage, sonst die Bildgröße der Vorlage selbst, sonst die Originalgröße der Datei. Alt-Text, Titel und Unterschrift fallen bei leerem Feld auf die Metadaten der Datei zurück.

### Eigene Bildgröße im Platzhalter vorbelegen

Hat eine Vorlage mehrere Bilder mit unterschiedlichen Anforderungen, etwa ein großes Hero-Bild und ein kleines Icon, lässt sich für jeden Platzhalter eine eigene Standardgröße vorbelegen: als dritter Teil nach dem zweiten `|`, genauso wie bei `select` die Optionen. Das ist nur ein Vorschlag, der Redakteur kann ihn im Inhaltselement jederzeit überschreiben.

| Angabe | Bedeutung |
| --- | --- |
| *(leer)* | Verwendet die Bildgröße der Vorlage |
| `800x600` | Feste Breite × Höhe, proportionales Zuschneiden (Contao-Standardmodus) |
| `800x600_box` | Breite × Höhe mit Zuschneidemodus, z. B. `box`, `crop`, `proportional`, `landscape`, `portrait` |
| `12` | Verweist auf eine bestehende, im Contao-Backend angelegte Bildgröße mit der ID 12 |
| `800x600_box,lightbox` | Zusätzlich `lightbox` angeben: Lightbox ist vorausgewählt, lässt sich aber im Inhaltselement wieder abwählen |

Das Twig-Template `content_element/modularis_element.html.twig` kann wie üblich überschrieben werden. Verfügbare Variablen: `html` (fertiges Snippet), `snippet` (`id`, `name`, `alias`) und `values` (Rohwerte).

## Rechte

Das Backend-Modul **Modularis** wird über die Benutzergruppen freigegeben. Da Vorlagen beliebiges HTML enthalten, sollte nur Admins oder vertrauenswürdigen Gruppen Zugriff gegeben werden. Redakteure benötigen nur Zugriff auf das Inhaltselement.

## Technik

- Vorlagen liegen in `tl_modularis_template`.
- Das Inhaltselement speichert die Vorlagen-ID in `tl_content.modularisTemplate` und alle Feldwerte als JSON in `tl_content.modularisData`. Neue Platzhalter erfordern deshalb keine Datenbankmigration.
- Die Felder werden im `config.onload` von `tl_content` dynamisch registriert und per `PaletteManipulator` eingehängt.
- Die Frontend-Ausgabe wird mit `contao.db.tl_modularis_template.<id>` getaggt, damit der HTTP-Cache bei Änderungen an der Vorlage invalidiert wird.

## Löschschutz

Eine Vorlage, die noch von Inhaltselementen verwendet wird, kann nicht gelöscht werden. Das Löschen-Icon ist dann ausgegraut, und ein Löschversuch über "Mehrere bearbeiten" wird mit einer Fehlermeldung abgebrochen.

## Bekannte Grenzen

- Bedingungsblöcke mit `[[if:name]]` können nicht verschachtelt werden.
- Beim Wechsel der Vorlage bleiben bereits eingegebene Werte gleichnamiger Felder erhalten.

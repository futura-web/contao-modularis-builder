<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_modularis_template']['name'] = ['Name', 'Internal name, shown in the content element selection.'];
$GLOBALS['TL_LANG']['tl_modularis_template']['alias'] = ['Alias', 'Unique short name. Leave empty to generate it automatically.'];
$GLOBALS['TL_LANG']['tl_modularis_template']['description'] = ['Description', 'Optional note for editors.'];
$GLOBALS['TL_LANG']['tl_modularis_template']['html'] = ['HTML snippet', 'The HTML with placeholders in double square brackets, e.g. [[text:headline|Headline]].'];
$GLOBALS['TL_LANG']['tl_modularis_template']['imgSize'] = ['Image size', 'Default for all image placeholders (type image) of this template, unless the placeholder specifies its own size.'];

$GLOBALS['TL_LANG']['tl_modularis_template']['title_legend'] = 'Template';
$GLOBALS['TL_LANG']['tl_modularis_template']['snippet_legend'] = 'HTML snippet';
$GLOBALS['TL_LANG']['tl_modularis_template']['image_legend'] = 'Image settings';

$GLOBALS['TL_LANG']['tl_modularis_template']['syntax'] = [
    'title' => 'Placeholder syntax',
    'structure' => [
        'title' => 'Structure',
        'pattern' => '[[type:name.cssclass|Label]]',
        'rows' => [
            ['type', 'Field type: text, textarea, rte, html, image, url, number, checkbox, select. Optional, defaults to text.'],
            ['name', 'Technical field name: lowercase letters, digits, underscore. Required.'],
            ['.cssclass', 'Width of the field in the back end form, appended directly to the name, e.g. .w50. Optional, every field type has a sensible default width.'],
            ['Label', 'Text above the input field in the back end. Optional, defaults to the name.'],
        ],
    ],
    'examples' => 'Examples (the field names in them are made up, choose your own)',
    'rows' => [
        ['[[slogan]]', 'Short form without type and label: text field, labelled "Slogan" in the back end'],
        ['[[text:slogan|Tagline]]', 'Long form: the same text field, labelled "Tagline" in the back end'],
        ['[[textarea:intro|Introduction]]', 'Multi-line text, line breaks become <br>'],
        ['[[rte:content|Body text]]', 'Rich text with TinyMCE'],
        ['[[html:code|Embed code]]', 'Raw HTML with code editor'],
        ['[[image:picture|Picture]]', 'Image from the file manager, rendered as <figure> in the configured size'],
        ['[[image:picture|Picture|800x600_box]]', 'Own image size instead of the template size'],
        ['[[image:picture|Picture|800x600_box,lightbox]]', 'Also: image opens in a lightbox when clicked'],
        ['[[url:link|Link target]]', 'Link address with page and file picker'],
        ['[[number:count|Count]]', 'Integer'],
        ['[[checkbox:border|With border]]', 'Yes/no, outputs 1 or empty'],
        ['[[select:variant|Variant|light,dark]]', 'Select field with the options light and dark'],
        ['[[select:color|Color|red=Red,blue=Blue]]', 'Select field; value before the =, label after it'],
        ['[[if:link]] content [[/if]]', 'Output block only if field "link" is filled or checked'],
        ['[[text:firstname.w50|First name]] [[text:lastname.w50|Last name]]', 'Two fields side by side, each half width'],
        ['[[text:city.w33|City]]', 'One-third width, useful for three fields in one row'],
    ],
    'note' => 'Names consist of lowercase letters, digits and underscore. Common width classes: w25, w33, w50, w66, w75, clr (forces a new line). Combine several with +, e.g. .w33+clr. Insert tags such as {{link_url::12}} keep working. The same placeholder may appear several times and is asked for only once.',
];

$GLOBALS['TL_LANG']['tl_modularis_template']['deleteBlocked'] = 'The template "%s" is still used by %d content element(s) and cannot be deleted.';

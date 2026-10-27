<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_content']['palettes']['modularis_element'] = '{type_legend},type,headline;{snippet_legend},modularisTemplate;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['modularisTemplate'] = [
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_modularis_template.name',
    'eval' => [
        'mandatory' => true,
        'includeBlankOption' => true,
        'submitOnChange' => true,
        'chosen' => true,
        'tl_class' => 'w50',
    ],
    'sql' => 'int(10) unsigned NOT NULL default 0',
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

// Werte der dynamischen Felder, als JSON
$GLOBALS['TL_DCA']['tl_content']['fields']['modularisData'] = [
    'sql' => 'mediumtext NULL',
];

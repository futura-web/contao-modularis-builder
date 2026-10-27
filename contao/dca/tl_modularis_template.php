<?php

declare(strict_types=1);

use Contao\BackendUser;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;

$GLOBALS['TL_DCA']['tl_modularis_template'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'markAsCopy' => 'name',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'alias' => 'index',
            ],
        ],
    ],

    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'fields' => ['name'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields' => ['name'],
            'format' => '%s',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit',
            'copy',
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset()"',
                // button_callback wird im ModularisTemplateListener registriert
            ],
            'show',
        ],
    ],

    'palettes' => [
        'default' => '{title_legend},name,alias,description;{snippet_legend},html,syntaxHelp;{image_legend},imgSize',
    ],

    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'name' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'alias' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'alias', 'doNotCopy' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) BINARY NOT NULL default ''",
        ],
        'description' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['style' => 'height:60px', 'tl_class' => 'clr'],
            'sql' => 'text NULL',
        ],
        'html' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => [
                'mandatory' => true,
                'allowHtml' => true,
                'preserveTags' => true,
                'decodeEntities' => true,
                'rte' => 'ace|html',
                'class' => 'monospace',
                'tl_class' => 'clr',
            ],
            'sql' => 'mediumtext NULL',
        ],
        'syntaxHelp' => [
            // input_field_callback wird im ModularisTemplateListener registriert
        ],
        'imgSize' => [
            'exclude' => true,
            'inputType' => 'imageSize',
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
            'options_callback' => static fn () => System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance()),
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];

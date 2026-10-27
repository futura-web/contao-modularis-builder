<?php

declare(strict_types=1);

use Contao\ArrayUtil;

// Backend-Modul "Modularis" unter "Layout" direkt vor "Templates" einhängen
$modularisPosition = array_search('tpl_editor', array_keys($GLOBALS['BE_MOD']['design']), true);

ArrayUtil::arrayInsert(
    $GLOBALS['BE_MOD']['design'],
    false !== $modularisPosition ? $modularisPosition : \count($GLOBALS['BE_MOD']['design']),
    [
        'modularis_templates' => [
            'tables' => ['tl_modularis_template'],
        ],
    ]
);

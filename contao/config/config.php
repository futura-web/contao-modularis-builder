<?php

declare(strict_types=1);

use Contao\ArrayUtil;

// Backend-Modul "Modularis" direkt nach "Artikel" einhängen
ArrayUtil::arrayInsert($GLOBALS['BE_MOD']['content'], 1, [
    'modularis_templates' => [
        'tables' => ['tl_modularis_template'],
    ],
]);

<?php

declare(strict_types=1);

use Contao\ArrayUtil;

// Backend-Modul "Modularis" unter "Layout" einhängen
ArrayUtil::arrayInsert($GLOBALS['BE_MOD']['design'], 1, [
    'modularis_templates' => [
        'tables' => ['tl_modularis_template'],
    ],
]);

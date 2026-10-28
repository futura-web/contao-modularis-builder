<?php

declare(strict_types=1);

namespace Futuraweb\ContaoModularisBuilder;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoModularisBuilderBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}

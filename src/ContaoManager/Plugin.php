<?php

declare(strict_types=1);

namespace Futura\ContaoModularisBuilder\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Futura\ContaoModularisBuilder\ContaoModularisBuilderBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoModularisBuilderBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}

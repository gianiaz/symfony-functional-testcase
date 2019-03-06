<?php

declare(strict_types=1);

namespace Facile\SymfonyFunctionalTestCase\Tests\AppConfigLeanFramework;

use Facile\SymfonyFunctionalTestCase\Tests\App\AppKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppConfigLeanFrameworkKernel extends AppKernel
{
    /**
     * Load the config.yml from the current directory.
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        // Load the default file.
        parent::registerContainerConfiguration($loader);

        // Load the file with lean framework configuration
        $loader->load(__DIR__.'/config.yml');
    }
}

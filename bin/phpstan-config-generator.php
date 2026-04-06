<?php declare(strict_types=1);

use Composer\InstalledVersions;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\StaticKernelPluginLoader;
use Shopware\Core\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$baseClass = \Zeobv\AbandonedCart\ZeobvAbandonedCart::class; // e.g. \Zeobv\ExamplePlugin\ZeobvExamplePlugin::class

$classLoader = require __DIR__ . '/../../../../vendor/autoload.php';
(new Dotenv())->load(__DIR__ . '/../../../../.env');

$shopwareVersion = InstalledVersions::getVersion('shopware/core');

$pluginRootPath = dirname(__DIR__);
$composerJson = json_decode((string) file_get_contents($pluginRootPath . '/composer.json'), true);

$bundle = [
    'name' => $composerJson['name'],
    'version' => $composerJson['version'],
    'autoload' => $composerJson['autoload'],
    'baseClass' => $baseClass,
    'managedByComposer' => false,
    'active' => true,
    'path' => $pluginRootPath,
];
$pluginLoader = new StaticKernelPluginLoader($classLoader, null, [$bundle]);

$kernel = new Kernel('dev', true, $pluginLoader, $shopwareVersion);
$kernel->boot();
$projectDir = $kernel->getProjectDir();
$cacheDir = $kernel->getCacheDir();

$relativeCacheDir = str_replace($projectDir, '', $cacheDir);

$phpStanConfigDist = file_get_contents(__DIR__ . '/../phpstan.neon.dist');
if ($phpStanConfigDist === false) {
    throw new RuntimeException('phpstan.neon.dist file not found');
}

// because the cache dir is hashed by Shopware, we need to set the PHPStan config dynamically
$phpStanConfig = str_replace(
    [
        "\n        # the placeholder \"%ShopwareHashedCacheDir%\" will be replaced on execution by bin/phpstan-config-generator.php script",
        '%ShopwareHashedCacheDir%',
    ],
    [
        '',
        $relativeCacheDir,
    ],
    $phpStanConfigDist
);

file_put_contents(__DIR__ . '/../phpstan.neon', $phpStanConfig);

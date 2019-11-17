<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace TfRpc\Config;

use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;

class ConfigFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $configPath = APP_PATH . '/config/';
        var_dump($configPath);
        $config = $this->readConfig($configPath . 'config.php');
        $serverConfig = $this->readConfig($configPath . 'server.php');
        $autoloadConfig = $this->readPaths([APP_PATH . '/config/autoload']);
        $merged = array_merge_recursive(ProviderConfig::load(), $serverConfig, $config, ...$autoloadConfig);
        return new Config($merged);
    }

    /**
     * 读取配置文件
     *
     * @param string $configPath
     *
     * @return array
     */
    private function readConfig(string $configPath): array
    {
        $config = [];
        if (isFile($configPath)) {
            $config = require $configPath;
        }
        return is_array($config) ? $config : [];
    }

    /**
     *
     * 读取多个目录下的 php 文件
     *
     * @param array $paths
     *
     * @return array
     */
    private function readPaths(array $paths)
    {
        $configs = [];
        $finder = new Finder();
        $finder->files()->in($paths)->name('*.php');
        foreach ($finder as $file) {
            $configs[] = [
                $file->getBasename('.php') => require $file->getRealPath(),
            ];
        }
        return $configs;
    }
}

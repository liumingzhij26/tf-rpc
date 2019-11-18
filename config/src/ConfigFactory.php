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

use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;
use TfRpc\Utils\Str;

class ConfigFactory
{
    /**
     * @return Config
     */
    public function __invoke()
    {
        if (file_exists(APP_PATH . '/.env')) {
            Dotenv::create([APP_PATH])->load();
        }
        $configPath = APP_PATH . '/config/';
        $config = $this->readConfig($configPath . 'config.php');
        $serverConfig = $this->readConfig($configPath . 'server.php');
        $autoloadConfig = $this->readPaths([APP_PATH . '/bin/config/']);
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
     * api/wechat/thefair/english/Reading.php => dev/api/wechat/thefair/english/Reading.php
     *
     * @param array $paths
     *
     * @return array
     */
    private function readPaths(array $paths)
    {
        $configs = [];
        $finder = new Finder();
        $phase = env('PHASE');//开发环境，如 dev
        $finder->files()->in($paths)->exclude($phase);//如果指定了环境变量，就排除环境变量的配置扫描
        foreach ($finder as $file) {
            $path = $file->getRelativePath() . '/' . $file->getBasename('.php');
            $filePath = $file->getRealPath();
            if ($phase) {
                $tmpPath = str_replace($file->getRelativePathname(), $phase . '/' . $file->getRelativePathname(), $file->getPathname());
                if (isFile($tmpPath)) {//如果开发环境中指定了文件路径，就替换读取文件
                    $filePath = $tmpPath;
                }
            }
            $configs[] = Str::pathToArray(mb_strtolower($path), require $filePath);
        }
        return $configs;
    }
}

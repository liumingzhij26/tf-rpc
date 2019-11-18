<?php
declare(strict_types=1);

/***************************************************************************
 *
 * Copyright (c) 2019 liumingzhi, Inc. All Rights Reserved
 *
 **************************************************************************
 *
 * @file Functions.php
 * @author liumingzhi(liumingzhij26@gmail.com)
 * @date 2019-11-14 16:03:00
 *
 **/

use TfRpc\Utils\Arr;
use TfRpc\Utils\Collection;

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof \Closure ? $value() : $value;
    }
}

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param $key
     * @param null $default
     * @return array|bool|false|mixed|string|void
     */
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return value($default);
        }
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }
        if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
            return substr($value, 1, -1);
        }
        return $value;
    }
}

if (!function_exists('configGet')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param $target
     * @param $key
     * @param null $default
     * @return array|mixed
     */
    function configGet($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', is_int($key) ? (string)$key : $key);
        while (!is_null($segment = array_shift($key))) {
            if ($segment === '*') {
                if ($target instanceof Collection) {
                    $target = $target->all();
                } elseif (!is_array($target)) {
                    return value($default);
                }
                $result = [];
                foreach ($target as $item) {
                    $result[] = configGet($item, $key);
                }
                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }
            if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }
        return $target;
    }
}
if (!function_exists('configSet')) {
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param $target
     * @param $key
     * @param $value
     * @param bool $overwrite
     *
     * @return array
     */
    function configSet(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);
        if (($segment = array_shift($segments)) === '*') {
            if (!Arr::accessible($target)) {
                $target = [];
            }
            if ($segments) {
                foreach ($target as &$inner) {
                    configSet($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (Arr::accessible($target)) {
            if ($segments) {
                if (!Arr::exists($target, $segment)) {
                    $target[$segment] = [];
                }
                configSet($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !Arr::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (!isset($target->{$segment})) {
                    $target->{$segment} = [];
                }
                configSet($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || !isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];
            if ($segments) {
                configSet($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }
        return $target;
    }
}

if (!function_exists('collect')) {
    /**
     * 创建一个集合
     *
     * @param null|mixed $value
     * @return Collection
     */
    function collect($value = null)
    {
        return new Collection($value);
    }
}

if (!function_exists('isFile')) {
    /**
     * 判断文件是否可读
     *
     * @param $configPath
     * @return bool
     */
    function isFile($configPath)
    {
        return file_exists($configPath) && is_readable($configPath);
    }
}

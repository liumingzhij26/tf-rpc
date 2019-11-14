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

use Tfrpc\Utils\Arr;
use Tfrpc\Utils\Collection;

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

if (!function_exists('config_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param $target
     * @param $key
     * @param null $default
     * @return array|mixed
     */
    function config_get($target, $key, $default = null)
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
                    $result[] = config_get($item, $key);
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
if (!function_exists('config_set')) {
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
    function config_set(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);
        if (($segment = array_shift($segments)) === '*') {
            if (!Arr::accessible($target)) {
                $target = [];
            }
            if ($segments) {
                foreach ($target as &$inner) {
                    config_set($inner, $segments, $value, $overwrite);
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
                config_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !Arr::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (!isset($target->{$segment})) {
                    $target->{$segment} = [];
                }
                config_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || !isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];
            if ($segments) {
                config_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }
        return $target;
    }
}

if (!function_exists('collect')) {
    /**
     * Create a collection from the given value.
     *
     * @param null|mixed $value
     * @return Collection
     */
    function collect($value = null)
    {
        return new Collection($value);
    }
}

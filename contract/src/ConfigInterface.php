<?php

declare(strict_types=1);
/***************************************************************************
 *
 * Copyright (c) 2019 liumingzhi, Inc. All Rights Reserved
 *
 **************************************************************************
 *
 * @file Config.php
 * @author liumingzhi(liumingzhij26@gmail.com)
 * @date 2019-11-14 16:40:00
 *
 **/

namespace TfRpc\Contract;

interface ConfigInterface
{

    /**
     * 获得配置文件，$key 值可以通过 . 连接符定位到下级数组，$default 则是当对应的值不存在时返回的默认值
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * set 配置文件
     *
     * @param string $key
     * @param $value
     *
     * @return void
     */
    public function set(string $key, $value);

    /**
     * 判断是否有这个配置，$key 值可以通过 . 连接符定位到下级数组
     *
     * @param string $key
     *
     * @return mixed
     */
    public function has(string $key);

    /**
     * 获得所有配置文件
     *
     * @return array
     */
    public function getConfigs();
}

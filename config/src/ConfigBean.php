<?php
declare(strict_types=1);
/***************************************************************************
 *
 * Copyright (c) 2019 liumingzhi, Inc. All Rights Reserved
 *
 **************************************************************************
 *
 * @file ConfigBean.php
 * @author liumingzhi(liumingzhij26@gmail.com)
 * @date 2019-11-15 11:14:00
 *
 **/

namespace TfRpc\Config;

use TfRpc\Contract\ConfigInterface;

class ConfigBean
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                ConfigInterface::class => ConfigFactory::class,
            ]
        ];
    }
}
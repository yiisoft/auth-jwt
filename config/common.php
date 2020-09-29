<?php

use Yiisoft\Auth\Jwt\TokenManager;
use Yiisoft\Auth\Jwt\TokenManagerInterface;

/**
 * @var $params array
 */
return [
    TokenManagerInterface::class => static function () use ($params) {
        return new TokenManager($params['yiisoft/auth-jwt']['secret']);
    }
];

<?php

declare(strict_types=1);

use Jose\Component\Signature\Algorithm\HS256;
use Jose\Component\Signature\Serializer\CompactSerializer;

return [
    'yiisoft/auth-jwt' => [
        'algorithms' => [
            HS256::class,
        ],
        'serializers' => [
            CompactSerializer::class,
        ],
        'key' => [
            'secret' => '',
            'file' => '',
            'password' => '',
        ],
    ],
];

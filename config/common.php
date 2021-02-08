<?php

declare(strict_types=1);

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Yiisoft\Auth\Jwt\KeyFactory\FromSecret;
use Yiisoft\Auth\Jwt\KeyFactoryInterface;
use Yiisoft\Auth\Jwt\TokenFactory;
use Yiisoft\Auth\Jwt\TokenFactoryInterface;
use Yiisoft\Auth\Jwt\TokenRepository;
use Yiisoft\Auth\Jwt\TokenRepositoryInterface;
use Yiisoft\Injector\Injector;

/**
 * @var $params array
 */
return [
    KeyFactoryInterface::class => [
        '__class' => FromSecret::class,
        '__construct()' => [$params['yiisoft/auth-jwt']['key']['secret']],
    ],
    AlgorithmManager::class => static function (Injector $injector) use ($params) {
        $algorithms = array_map(
            static fn ($algorithm) => $injector->make($algorithm),
            $params['yiisoft/auth-jwt']['algorithms'] ?? []
        );
        return $injector->make(AlgorithmManager::class, ['algorithms' => $algorithms]);
    },
    JWSSerializerManager::class => static function (Injector $injector) use ($params) {
        $serializers = array_map(
            static fn ($serializer) => $injector->make($serializer),
            $params['yiisoft/auth-jwt']['serializers'] ?? []
        );
        return $injector->make(JWSSerializerManager::class, ['serializers' => $serializers]);
    },
    TokenFactoryInterface::class => TokenFactory::class,
    TokenRepositoryInterface::class => TokenRepository::class,
];

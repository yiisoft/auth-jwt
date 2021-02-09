<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\Tests;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Algorithm\HS256;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Auth\Jwt\KeyFactory\FromKeyFile;
use Yiisoft\Auth\Jwt\KeyFactory\FromSecret;
use Yiisoft\Auth\Jwt\KeyFactoryInterface;
use Yiisoft\Auth\Jwt\TokenFactory;
use Yiisoft\Auth\Jwt\TokenFactoryInterface;
use Yiisoft\Auth\Jwt\TokenRepository;
use Yiisoft\Auth\Jwt\TokenRepositoryInterface;
use Yiisoft\Http\Method;

class TestCase extends BaseTestCase
{
    private const SECRET = 'dsgsdgr45t3eEF$G3G$3gee44tdsSagsdgGDsdLsadfaGsSfGDgEGEgsgrbswg344wgv34b5sdy67sdS';

    protected function createRequest(array $headers = []): ServerRequestInterface
    {
        return new ServerRequest(Method::GET, '/', $headers);
    }

    protected function getTokenRepository(): TokenRepositoryInterface
    {
        return new TokenRepository($this->getKeyFactory(), $this->getAlgorithmManager(), $this->getSerializerManager());
    }

    protected function getTokenFactory(): TokenFactoryInterface
    {
        return new TokenFactory($this->getKeyFactory(), $this->getAlgorithmManager(), $this->getSerializerManager());
    }

    protected function getKeyFactory(): KeyFactoryInterface
    {
        return new FromSecret(self::SECRET);
    }

    protected function getAlgorithmManager(): AlgorithmManager
    {
        return new AlgorithmManager([new HS256()]);
    }

    protected function getSerializerManager(): JWSSerializerManager
    {
        return new JWSSerializerManager([new CompactSerializer()]);
    }
}

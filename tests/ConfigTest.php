<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\Tests;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Yiisoft\Auth\Jwt\KeyFactory\FromSecret;
use Yiisoft\Auth\Jwt\KeyFactoryInterface;
use Yiisoft\Auth\Jwt\TokenFactory;
use Yiisoft\Auth\Jwt\TokenFactoryInterface;
use Yiisoft\Auth\Jwt\TokenRepository;
use Yiisoft\Auth\Jwt\TokenRepositoryInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;

final class ConfigTest extends \PHPUnit\Framework\TestCase
{
    public function testBase(): void
    {
        $container = $this->createContainer();

        $keyFactory = $container->get(KeyFactoryInterface::class);
        $algorithmManager = $container->get(AlgorithmManager::class);
        $jwsSerializerManager = $container->get(JWSSerializerManager::class);
        $tokenFactory = $container->get(TokenFactoryInterface::class);
        $tokenRepository = $container->get(TokenRepositoryInterface::class);

        $this->assertInstanceOf(FromSecret::class, $keyFactory);
        $this->assertInstanceOf(AlgorithmManager::class, $algorithmManager);
        $this->assertInstanceOf(JWSSerializerManager::class, $jwsSerializerManager);
        $this->assertInstanceOf(TokenFactory::class, $tokenFactory);
        $this->assertInstanceOf(TokenRepository::class, $tokenRepository);
    }

    private function createContainer(?array $params = null): Container
    {
        return new Container(
            ContainerConfig::create()->withDefinitions(
                $this->getDiConfig($params)
            )
        );
    }

    private function getDiConfig(?array $params = null): array
    {
        if ($params === null) {
            $params = $this->getParams();
        }
        return require dirname(__DIR__) . '/config/di.php';
    }

    private function getParams(): array
    {
        return require dirname(__DIR__) . '/config/params.php';
    }
}

<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\Tests;

use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\Algorithm\HS384;
use Jose\Component\Signature\Serializer\JSONFlattenedSerializer;
use Jose\Component\Signature\Serializer\JSONGeneralSerializer;
use PHPUnit\Framework\TestCase;
use Yiisoft\Auth\Jwt\TokenManager;
use Yiisoft\Auth\Jwt\TokenManagerInterface;

class TokenManagerTest extends TestCase
{
    private const SECRET = 'dsgsdgr45t3eEF$G3G$3gee44tdsSagsdgGDsdLsadfaGsSfGDgEGEgsgrbswg344wgv34b5sdy67sdS';
    private TokenManagerInterface $tokenManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenManager = new TokenManager(self::SECRET);
    }

    public function testCreateToken(): void
    {
        $payload = $this->getPayload();
        $token = $this->tokenManager->createToken($payload);
        $this->assertIsString($token);
    }

    public function testGetClaims(): void
    {
        $payload = $this->getPayload();
        $token = $this->tokenManager->createToken($payload);
        $claims = $this->tokenManager->getClaims($token);
        $this->assertSame($payload, $claims);
        $this->assertEquals($claims['sub'], $payload['sub']);
    }

    public function testEmptyPayload(): void
    {
        $payload = [];
        $token = $this->tokenManager->createToken($payload);
        $claims = $this->tokenManager->getClaims($token);
        $this->assertEmpty($claims);
    }

    public function testImmutability(): void
    {
        $original = new TokenManager(self::SECRET);

        $this->assertNotSame($original, $original->withAlgorithms([new ES256()]));
        $this->assertNotSame($original, $original->withSecret('another-secret'));
        $this->assertNotSame($original, $original->withSerializer(new JSONGeneralSerializer()));
    }

    public function testWithCustomAlgorithms(): void
    {
        $payload = $this->getPayload();
        $token = $this->tokenManager->withAlgorithms([new HS384()])->createToken($payload);
        $this->assertIsString($token);
    }


    public function testWithCustomSerializer(): void
    {
        $payload = $this->getPayload();
        $token = $this->tokenManager->withSerializer(new JSONFlattenedSerializer())->createToken($payload);
        $this->assertIsString($token);
    }

    public function testWithCustomSecret(): void
    {
        $payload = $this->getPayload();
        $secret = 'adg$#fv4ggsg5g5EG%h5shgsdsh55eggb4shjtj6sw5hesvd0h5h';
        $token = $this->tokenManager->withSecret($secret)->createToken($payload);
        $this->assertIsString($token);
    }

    private function getPayload(): array
    {
        return [
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
            'sub' => 1,
            'iss' => 'Yii Framework',
            'aud' => 'Yii 3',
        ];
    }
}

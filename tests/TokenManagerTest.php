<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Auth\Jwt\TokenManager;
use Yiisoft\Auth\Jwt\TokenManagerInterface;

class TokenManagerTest extends TestCase
{
    private const SECRET = 'dsgsdgr45t3eEF$G3G$3gee44tdsSagsdgGDsdLsadfaGsSfGDgEGEgsgrbswg344wgv34b5sdy67sdS';
    private TokenManagerInterface $tokenManager;

    protected function setUp():void
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

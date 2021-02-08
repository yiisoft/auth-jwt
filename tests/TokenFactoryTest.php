<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\Tests;

use Jose\Component\Signature\Serializer\CompactSerializer;
use Yiisoft\Auth\Jwt\KeyFactory\FromSecret;
use Yiisoft\Auth\Jwt\TokenFactory;
use Yiisoft\Auth\Jwt\TokenFactoryInterface;
use Yiisoft\Auth\Jwt\TokenRepositoryInterface;

class TokenFactoryTest extends TestCase
{
    private const SECRET = 'dsgsdgr45t3eEF$G3G$3gee44tdsSagsdgGDsdLsadfaGsSfGDgEGEgsgrbswg344wgv34b5sdy67sdS';
    private TokenFactoryInterface $tokenFactory;
    private TokenRepositoryInterface $tokenRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenFactory = $this->getTokenFactory();
        $this->tokenRepository = $this->getTokenRepository();
    }

    public function testCreateToken(): void
    {
        $payload = $this->getPayload();
        $token = $this->tokenFactory->createToken($payload, CompactSerializer::NAME);
        $this->assertIsString($token);
    }

    public function testWrongKeyToken(): void
    {
        $payload = [];
        $token = (new TokenFactory(
            new FromSecret(self::SECRET . 'wrong'),
            $this->getAlgorithmManager(),
            $this->getSerializerManager()
        ))->createToken($payload, CompactSerializer::NAME);
        $claims = $this->tokenRepository->getClaims($token);
        $this->assertNull($claims);
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

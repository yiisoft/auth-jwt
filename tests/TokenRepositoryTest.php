<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\Tests;

use Jose\Component\Signature\Serializer\CompactSerializer;
use Yiisoft\Auth\Jwt\TokenFactoryInterface;
use Yiisoft\Auth\Jwt\TokenRepositoryInterface;

class TokenRepositoryTest extends TestCase
{
    private TokenFactoryInterface $tokenFactory;
    private TokenRepositoryInterface $tokenRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenFactory = $this->getTokenFactory();
        $this->tokenRepository = $this->getTokenRepository();
    }

    public function testGetClaims(): void
    {
        $payload = $this->getPayload();
        $token = $this->tokenFactory->create($payload, CompactSerializer::NAME);
        $claims = $this->tokenRepository->getClaims($token);
        $this->assertSame($payload, $claims);
        $this->assertEquals($claims['sub'], $payload['sub']);
    }

    public function testEmptyPayload(): void
    {
        $payload = [];
        $token = $this->tokenFactory->create($payload, CompactSerializer::NAME);
        $claims = $this->tokenRepository->getClaims($token);
        $this->assertEmpty($claims);
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

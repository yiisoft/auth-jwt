<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\Tests;

use Jose\Component\Checker\InvalidClaimException;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\Jwt\JwtMethod;
use Yiisoft\Auth\Jwt\Tests\Stub\FakeIdentity;
use Yiisoft\Auth\Jwt\Tests\Stub\FakeIdentityRepository;
use Yiisoft\Auth\Jwt\TokenManager;
use Yiisoft\Http\Header;
use Yiisoft\Http\Method;

class JwtMethodTest extends TestCase
{
    private const SECRET = 'dsgsdgr45t3eEF$G3G$3gee44tdsSagsdgGDsdLsadfaGsSfGDgEGEgsgrbswg344wgv34b5sdy67sdS';

    public function testSuccessfulAuthentication(): void
    {
        $identityRepository = new FakeIdentityRepository($this->createIdentity());
        $tokenManager = new TokenManager(self::SECRET);
        $token = $tokenManager->createToken($this->getPayload());
        $result = (new JwtMethod($identityRepository, $tokenManager))->authenticate(
            $this->createRequest([Header::AUTHORIZATION => 'Bearer ' . $token])
        );

        $this->assertNotNull($result);
        $this->assertEquals('123', $result->getId());
    }

    private function createIdentity(?string $id = '123'): IdentityInterface
    {
        return new FakeIdentity($id);
    }

    private function getPayload(): array
    {
        return [
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
            'sub' => 123,
            'iss' => 'Yii Framework',
            'aud' => 'Yii 3',
        ];
    }

    private function createRequest(array $headers = []): ServerRequestInterface
    {
        return new ServerRequest(Method::GET, '/', $headers);
    }

    public function testUnSuccessfulAuthentication(): void
    {
        $identityRepository = new FakeIdentityRepository($this->createIdentity('111'));
        $tokenManager = new TokenManager(self::SECRET);
        $payload = $this->getPayload();
        $token = $tokenManager->createToken($payload);
        $result = (new JwtMethod($identityRepository, $tokenManager))->authenticate(
            $this->createRequest([Header::AUTHORIZATION => 'Bearer ' . $token])
        );

        $this->assertNull($result);
    }

    public function testCheckTokenIsExpired(): void
    {
        $this->expectException(InvalidClaimException::class);
        $identityRepository = new FakeIdentityRepository($this->createIdentity());
        $tokenManager = new TokenManager(self::SECRET);
        $payload = $this->getPayload();
        $payload['exp'] = time() - 1;
        $token = $tokenManager->createToken($payload);
        (new JwtMethod($identityRepository, $tokenManager))->authenticate(
            $this->createRequest([Header::AUTHORIZATION => 'Bearer ' . $token])
        );
    }
}

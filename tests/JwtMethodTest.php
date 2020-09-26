<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\Tests;

use Jose\Component\Checker\InvalidClaimException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\Jwt\JwtMethod;
use Yiisoft\Auth\Jwt\Tests\Stub\FakeIdentity;
use Yiisoft\Auth\Jwt\Tests\Stub\FakeIdentityRepository;
use Yiisoft\Auth\Jwt\TokenManager;
use Yiisoft\Auth\Jwt\TokenManagerInterface;
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

    public function testSuccessfulQueryParameterAuthentication(): void
    {
        $identityRepository = new FakeIdentityRepository($this->createIdentity());
        $tokenManager = new TokenManager(self::SECRET);
        $payload = $this->getPayload();
        $token = $tokenManager->createToken($payload);
        $result = (new JwtMethod($identityRepository, $tokenManager))->authenticate(
            $this->createRequest()->withQueryParams(['access-token' => $token])
        );

        $this->assertNotNull($result);
        $this->assertEquals($result->getId(), $payload['sub']);
    }

    public function testEmptyToken(): void
    {
        $identityRepository = new FakeIdentityRepository($this->createIdentity('111'));
        $tokenManager = new TokenManager(self::SECRET);
        $result = (new JwtMethod($identityRepository, $tokenManager))->authenticate(
            $this->createRequest()
        );

        $this->assertNull($result);
    }

    public function testChallengeIsCorrect(): void
    {
        $response = new Response(400);
        $identityRepository = new FakeIdentityRepository($this->createIdentity());
        $authenticationMethod = new JwtMethod($identityRepository, $this->getTokenManager());

        $this->assertEquals(400, $authenticationMethod->challenge($response)->getStatusCode());
        $this->assertNotEmpty($authenticationMethod->challenge($response)->getHeaderLine(Header::WWW_AUTHENTICATE));
    }

    public function testCheckTokenIsExpired(): void
    {
        $this->expectException(InvalidClaimException::class);
        $this->expectErrorMessage('The token expired.');
        $identityRepository = new FakeIdentityRepository($this->createIdentity());
        $tokenManager = $this->getTokenManager();
        $payload = $this->getPayload();
        $payload['exp'] = time() - 1;
        $token = $tokenManager->createToken($payload);
        (new JwtMethod($identityRepository, $tokenManager))->authenticate(
            $this->createRequest([Header::AUTHORIZATION => 'Bearer ' . $token])
        );
    }

    public function testImmutability(): void
    {
        $identityRepository = new FakeIdentityRepository($this->createIdentity());
        $original = new JwtMethod($identityRepository, $this->getTokenManager());
        $this->assertNotSame($original, $original->withHeaderName('headerName'));
        $this->assertNotSame($original, $original->withIdentifier('id'));
        $this->assertNotSame($original, $original->withQueryParameterName('token'));
        $this->assertNotSame($original, $original->withRealm('gateway'));
        $this->assertNotSame($original, $original->withHeaderTokenPattern('pattern'));
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

    private function getTokenManager(): TokenManagerInterface
    {
        return new TokenManager(self::SECRET);
    }
}

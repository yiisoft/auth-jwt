<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\Tests;

use Jose\Component\Checker\InvalidClaimException;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Nyholm\Psr7\Response;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\Jwt\JwtMethod;
use Yiisoft\Auth\Jwt\Tests\Stub\FakeIdentity;
use Yiisoft\Auth\Jwt\Tests\Stub\FakeIdentityRepository;
use Yiisoft\Http\Header;

class JwtMethodTest extends TestCase
{
    public function testSuccessfulAuthentication(): void
    {
        $identityRepository = new FakeIdentityRepository($this->createIdentity());
        $tokenManager = $this->getTokenFactory();
        $token = $tokenManager->createToken($this->getPayload(), CompactSerializer::NAME);
        $result = (new JwtMethod($identityRepository, $this->getTokenRepository()))->authenticate(
            $this->createRequest([Header::AUTHORIZATION => 'Bearer ' . $token])
        );

        $this->assertNotNull($result);
        $this->assertEquals('123', $result->getId());
    }

    public function testUnSuccessfulAuthentication(): void
    {
        $identityRepository = new FakeIdentityRepository($this->createIdentity('111'));
        $tokenManager = $this->getTokenFactory();
        $payload = $this->getPayload();
        $token = $tokenManager->createToken($payload, CompactSerializer::NAME);
        $result = (new JwtMethod($identityRepository, $this->getTokenRepository()))->authenticate(
            $this->createRequest([Header::AUTHORIZATION => 'Bearer ' . $token])
        );

        $this->assertNull($result);
    }

    public function testSuccessfulQueryParameterAuthentication(): void
    {
        $identityRepository = new FakeIdentityRepository($this->createIdentity());
        $tokenFactory = $this->getTokenFactory();
        $payload = $this->getPayload();
        $token = $tokenFactory->createToken($payload, CompactSerializer::NAME);
        $result = (new JwtMethod($identityRepository, $this->getTokenRepository()))->authenticate(
            $this->createRequest()->withQueryParams(['access-token' => $token])
        );

        $this->assertNotNull($result);
        $this->assertEquals($result->getId(), $payload['sub']);
    }

    public function testEmptyToken(): void
    {
        $identityRepository = new FakeIdentityRepository($this->createIdentity('111'));
        $result = (new JwtMethod($identityRepository, $this->getTokenRepository()))->authenticate(
            $this->createRequest()
        );

        $this->assertNull($result);
    }

    public function testEmptyPayload(): void
    {
        $identityRepository = new FakeIdentityRepository($this->createIdentity('111'));
        $tokenManager = $this->getTokenFactory();
        $token = $tokenManager->createToken([], CompactSerializer::NAME);
        $result = (new JwtMethod($identityRepository, $this->getTokenRepository()))->authenticate(
            $this->createRequest()->withQueryParams(['access-token' => $token])
        );

        $this->assertNull($result);
    }

    public function testChallengeIsCorrect(): void
    {
        $response = new Response(400);
        $identityRepository = new FakeIdentityRepository($this->createIdentity());
        $authenticationMethod = new JwtMethod($identityRepository, $this->getTokenRepository());

        $this->assertEquals(400, $authenticationMethod->challenge($response)->getStatusCode());
        $this->assertNotEmpty($authenticationMethod->challenge($response)->getHeaderLine(Header::WWW_AUTHENTICATE));
    }

    public function testCheckTokenIsExpired(): void
    {
        $this->expectException(InvalidClaimException::class);
        $this->expectErrorMessage('The token expired.');
        $identityRepository = new FakeIdentityRepository($this->createIdentity());
        $tokenFactory = $this->getTokenFactory();
        $payload = $this->getPayload();
        $payload['exp'] = time() - 1;
        $token = $tokenFactory->createToken($payload, CompactSerializer::NAME);
        (new JwtMethod($identityRepository, $this->getTokenRepository()))->authenticate(
            $this->createRequest([Header::AUTHORIZATION => 'Bearer ' . $token])
        );
    }

    public function testImmutability(): void
    {
        $identityRepository = new FakeIdentityRepository($this->createIdentity());
        $original = new JwtMethod($identityRepository, $this->getTokenRepository());
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

}

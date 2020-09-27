<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\HS256;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializer;
use Yiisoft\Json\Json;

final class TokenManager implements TokenManagerInterface
{
    private string $secret;
    private array $algorithms;
    private JWSSerializer $serializer;

    public function __construct(
        string $secret,
        ?array $algorithms = null,
        ?JWSSerializer $serializer = null
    ) {
        $this->secret = $secret;
        $this->algorithms = $algorithms ?? [new HS256()];
        $this->serializer = $serializer ?? new CompactSerializer();
    }

    public function createToken(array $payload): string
    {
        $algorithmManager = new AlgorithmManager($this->algorithms);
        $jwk = $this->createKey();
        $jwsBuilder = new JWSBuilder($algorithmManager);
        $jws = $jwsBuilder->create()->withPayload(Json::encode($payload));

        foreach ($this->algorithms as $algorithm) {
            $jws = $jws->addSignature($jwk, ['alg' => $algorithm->name()]);
        }

        return $this->serializer->serialize($jws->build());
    }

    public function getClaims(string $token): ?array
    {
        $jwk = $this->createKey();
        $jws = $this->serializer->unserialize($token);

        $isVerified = $this->verifyToken($jws, $jwk);
        if (!$isVerified || $jws->getPayload() === null) {
            return null;
        }
        return Json::decode($jws->getPayload());
    }

    public function withSecret(string $secret): self
    {
        $new = clone $this;
        $new->secret = $secret;
        return $new;
    }

    public function withAlgorithms(array $algorithms): self
    {
        $new = clone $this;
        $new->algorithms = $algorithms;
        return $new;
    }

    public function withSerializer(JWSSerializer $serializer): self
    {
        $new = clone $this;
        $new->serializer = $serializer;
        return $new;
    }

    private function createKey(): JWK
    {
        return JWKFactory::createFromSecret($this->secret);
    }

    private function verifyToken(JWS $jws, JWK $jwk, int $signature = 0): bool
    {
        $algorithmManager = new AlgorithmManager($this->algorithms);
        $jwsVerifier = new JWSVerifier($algorithmManager);

        return $jwsVerifier->verifyWithKey($jws, $jwk, $signature);
    }
}

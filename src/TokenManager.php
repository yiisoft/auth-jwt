<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

use Jose\Component\Core\Algorithm;
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

/**
 * Token manager creates a token and is getting a list of clams for it.
 * This implementation signs a token with JSON Web Signature.
 */
final class TokenManager implements TokenManagerInterface
{
    private string $secret;
    /**
     * @var Algorithm[]
     */
    private array $algorithms;
    private JWSSerializer $serializer;

    /**
     * @param string $secret A shared secret used to create a JSON Web Key.
     * @param Algorithm[]|null $algorithms Algorithms for signing JSON Web Signature. {@see HS256} by default.
     * @param JWSSerializer|null $serializer JSON Web Signature serializer. {@see CompactSerializer} by default.
     */
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
        if (!$isVerified) {
            return null;
        }

        /** @var array<array-key, mixed>|null */
        return Json::decode($jws->getPayload() ?? '');
    }

    /**
     * @param string $secret A shared secret used to create a JSON Web Key.
     *
     * @see https://tools.ietf.org/html/rfc7517
     *
     * @return $this
     */
    public function withSecret(string $secret): self
    {
        $new = clone $this;
        $new->secret = $secret;
        return $new;
    }

    /**
     * @param Algorithm[] $algorithms Algorithms for signing JSON Web Signature.
     *
     * @see https://tools.ietf.org/html/rfc7515
     *
     * @return self
     */
    public function withAlgorithms(array $algorithms): self
    {
        $new = clone $this;
        $new->algorithms = $algorithms;
        return $new;
    }

    /**
     * @param JWSSerializer $serializer JSON Web Signature serializer.
     *
     * @see https://tools.ietf.org/html/rfc7515
     *
     * @return self
     */
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

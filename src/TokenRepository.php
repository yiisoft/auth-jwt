<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Yiisoft\Json\Json;

/**
 * Token manager creates a token and is getting a list of clams for it.
 * This implementation signs a token with JSON Web Signature.
 */
final class TokenRepository implements TokenRepositoryInterface
{
    private KeyFactoryInterface $keyFactory;

    /**
     * @param AlgorithmManager $algorithmManager Algorithms manager for signing JSON Web Signature.
     *
     * @see https://tools.ietf.org/html/rfc7515
     */
    private AlgorithmManager $algorithmManager;

    /**
     * @param JWSSerializerManager $serializerManager JSON Web Signature serializer manager.
     *
     * @see https://tools.ietf.org/html/rfc7515
     */
    private JWSSerializerManager $serializerManager;

    /**
     * @param KeyFactoryInterface $keyFactory A factory to create a JSON Web Key.
     * @param AlgorithmManager $algorithmManager Algorithms manager for signing JSON Web Signature.
     * @param JWSSerializerManager $serializerManager JSON Web Signature serializer manager.
     */
    public function __construct(
        KeyFactoryInterface $keyFactory,
        AlgorithmManager $algorithmManager,
        JWSSerializerManager $serializerManager
    ) {
        $this->keyFactory = $keyFactory;
        $this->algorithmManager = $algorithmManager;
        $this->serializerManager = $serializerManager;
    }

    public function getClaims(string $token, ?string &$format = null): ?array
    {
        $jws = $this->serializerManager->unserialize($token, $format);
        $jwk = $this->keyFactory->create();

        foreach ($this->algorithmManager->list() as $index => $algorithm) {
            /** @var int $index */
            if ($this->verifyToken($jws, $jwk, $index)) {
                /** @var array<array-key, mixed>|null */
                return Json::decode($jws->getPayload() ?? '');
            }
        }
        return null;
    }

    private function verifyToken(JWS $jws, JWK $jwk, int $signature = 0): bool
    {
        $jwsVerifier = new JWSVerifier($this->algorithmManager);

        return $jwsVerifier->verifyWithKey($jws, $jwk, $signature);
    }
}

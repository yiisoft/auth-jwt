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
 * Token repository is getting a list of claims for a token.
 * The token JWS is being verified before doing so.
 */
final class TokenRepository implements TokenRepositoryInterface
{
    /**
     * @param KeyFactoryInterface $keyFactory A factory to create a JSON Web Key.
     * @param AlgorithmManager $algorithmManager Algorithms manager for signing JSON Web Signature.
     * @param JWSSerializerManager $serializerManager JSON Web Signature serializer manager.
     *
     * @see https://tools.ietf.org/html/rfc7515
     */
    public function __construct(
        private KeyFactoryInterface $keyFactory,
        private AlgorithmManager $algorithmManager,
        private JWSSerializerManager $serializerManager
    ) {
    }

    public function getClaims(string $token, ?string &$format = null): ?array
    {
        try {
            $jws = $this->serializerManager->unserialize($token, $format);
        } catch (\InvalidArgumentException) {
            return null;
        }

        $jwk = $this->keyFactory->create();

        foreach ($this->algorithmManager->list() as $index => $_algorithm) {
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
        return (new JWSVerifier($this->algorithmManager))->verifyWithKey($jws, $jwk, $signature);
    }
}

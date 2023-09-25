<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Yiisoft\Json\Json;

/**
 * Token factory creates a token signed with JSON Web Signature.
 */
final class TokenFactory implements TokenFactoryInterface
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

    public function create(array $payload, string $format, ?int $signatureIndex = null): string
    {
        $jwsBuilder = new JWSBuilder($this->algorithmManager);
        $jws = $jwsBuilder->create()->withPayload(Json::encode($payload));
        $jwk = $this->keyFactory->create();

        foreach ($this->algorithmManager->list() as $algorithm) {
            $jws = $jws->addSignature($jwk, ['alg' => $algorithm]);
        }

        return $this->serializerManager->serialize($format, $jws->build(), $signatureIndex);
    }
}

<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Yiisoft\Json\Json;

/**
 * Token manager creates a token and is getting a list of clams for it.
 * This implementation signs a token with JSON Web Signature.
 */
final class TokenFactory implements TokenFactoryInterface
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

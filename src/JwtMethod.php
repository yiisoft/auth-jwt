<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

use Jose\Component\Checker\ClaimCheckerManager;
use Jose\Component\Checker\ExpirationTimeChecker;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Auth\AuthenticationMethodInterface;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Http\Header;

/**
 * Authentication method based on JWT token.
 */
final class JwtMethod implements AuthenticationMethodInterface
{
    private string $headerName = Header::AUTHORIZATION;
    private string $queryParameterName = 'access-token';
    private string $headerTokenPattern = '/^Bearer\s+(.*?)$/';
    private string $realm = 'api';
    private string $identifier = 'sub';
    private array $claimCheckers;
    private IdentityRepositoryInterface $identityRepository;
    private TokenManagerInterface $tokenManager;

    public function __construct(
        IdentityRepositoryInterface $identityRepository,
        TokenManagerInterface $tokenManager,
        ?array $claimCheckers = null
    ) {
        $this->identityRepository = $identityRepository;
        $this->tokenManager = $tokenManager;
        $this->claimCheckers = $claimCheckers ?? [new ExpirationTimeChecker()];
    }

    public function authenticate(ServerRequestInterface $request): ?IdentityInterface
    {
        $token = $this->getAuthenticationToken($request);
        if ($token === null) {
            return null;
        }

        $claims = $this->tokenManager->getClaims($token);
        if ($claims !== null && isset($claims[$this->identifier])) {
            $this->getClaimCheckerManager()->check($claims);
            return $this->identityRepository->findIdentity((string)$claims[$this->identifier]);
        }

        return null;
    }

    private function getAuthenticationToken(ServerRequestInterface $request): ?string
    {
        $authHeaders = $request->getHeader($this->headerName);
        $authHeader = \reset($authHeaders);
        if (!empty($authHeader) && preg_match($this->headerTokenPattern, $authHeader, $matches)) {
            return $matches[1];
        }

        return $request->getQueryParams()[$this->queryParameterName] ?? null;
    }

    private function getClaimCheckerManager(): ClaimCheckerManager
    {
        return new ClaimCheckerManager($this->claimCheckers);
    }

    public function challenge(ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader(Header::WWW_AUTHENTICATE, "{$this->headerName} realm=\"{$this->realm}\"");
    }

    /**
     * @param string $realm The HTTP authentication realm.
     * @return self
     */
    public function withRealm(string $realm): self
    {
        $new = clone $this;
        $new->realm = $realm;
        return $new;
    }

    /**
     * @param string $headerName
     * @return self
     */
    public function withHeaderName(string $headerName): self
    {
        $new = clone $this;
        $new->headerName = $headerName;
        return $new;
    }

    /**
     * @param string $headerTokenPattern
     * @return self
     */
    public function withHeaderTokenPattern(string $headerTokenPattern): self
    {
        $new = clone $this;
        $new->headerTokenPattern = $headerTokenPattern;
        return $new;
    }

    /**
     * @param string $queryParameterName
     * @return self
     */
    public function withQueryParameterName(string $queryParameterName): self
    {
        $new = clone $this;
        $new->queryParameterName = $queryParameterName;
        return $new;
    }

    /**
     * @param string $identifier
     * @return self
     */
    public function withIdentifier(string $identifier): self
    {
        $new = clone $this;
        $new->identifier = $identifier;
        return $new;
    }
}

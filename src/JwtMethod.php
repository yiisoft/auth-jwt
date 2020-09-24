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
    private string $queryParamName = 'access-token';
    private string $headerTokenPattern = '/^Bearer\s+(.*?)$/';
    private string $realm = 'api';
    private string $identifier = 'sub';
    private IdentityRepositoryInterface $identityRepository;
    private TokenManagerInterface $tokenManager;
    private ?array $claimCheckers = null;

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
        $this->getClaimChecker()->check($claims);

        if ($claims !== null && isset($claims[$this->identifier])) {
            return $this->identityRepository->findIdentity($claims[$this->identifier]);
        }
        return null;
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
     * @param string $queryParamName
     * @return self
     */
    public function withQueryParamName(string $queryParamName): self
    {
        $new = clone $this;
        $new->queryParamName = $queryParamName;
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

    private function getAuthenticationToken(ServerRequestInterface $request): ?string
    {
        $authHeaders = $request->getHeader($this->headerName);
        $authHeader = \reset($authHeaders);
        if (!empty($authHeader)) {
            if (preg_match($this->headerTokenPattern, $authHeader, $matches)) {
                $authHeader = $matches[1];
            } else {
                return null;
            }
            return $authHeader;
        }

        return $request->getQueryParams()[$this->queryParamName] ?? null;
    }

    private function getClaimChecker(): ClaimCheckerManager
    {
        return new ClaimCheckerManager($this->claimCheckers);
    }
}

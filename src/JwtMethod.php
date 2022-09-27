<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

use Jose\Component\Checker\ClaimChecker;
use Jose\Component\Checker\ClaimCheckerManager;
use Jose\Component\Checker\ExpirationTimeChecker;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Auth\AuthenticationMethodInterface;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Http\Header;
use function is_string;
use function reset;

/**
 * Authentication method based on JWT token.
 *
 * @link https://tools.ietf.org/html/rfc7519
 * @link https://jwt.io/
 */
final class JwtMethod implements AuthenticationMethodInterface
{
    private string $headerName = Header::AUTHORIZATION;
    private string $queryParameterName = 'access-token';
    private string $headerTokenPattern = '/^Bearer\s+(.*?)$/';
    private string $realm = 'api';
    private string $identifier = 'sub';

    /**
     * @var ClaimChecker[]
     */
    private array $claimCheckers;

    /**
     * @param IdentityRepositoryInterface $identityRepository Repository to get identity from.
     * @param TokenRepositoryInterface $tokenRepository Token manager to obtain claims from.
     * @param ClaimChecker[]|null $claimCheckers Claim checkers. If not specified, {@see ExpirationTimeChecker} is used.
     */
    public function __construct(
        private IdentityRepositoryInterface $identityRepository,
        private TokenRepositoryInterface $tokenRepository,
        ?array $claimCheckers = null
    ) {
        $this->claimCheckers = $claimCheckers ?? [new ExpirationTimeChecker()];
    }

    public function authenticate(ServerRequestInterface $request): ?IdentityInterface
    {
        $token = $this->getAuthenticationToken($request);
        if ($token === null) {
            return null;
        }

        $claims = $this->tokenRepository->getClaims($token, $name);
        if ($claims === null || !isset($claims[$this->identifier])) {
            return null;
        }

        $this
            ->getClaimCheckerManager()
            ->check($claims);
        return $this->identityRepository->findIdentity((string)$claims[$this->identifier]);
    }

    private function getAuthenticationToken(ServerRequestInterface $request): ?string
    {
        $authHeaders = $request->getHeader($this->headerName);
        $authHeader = reset($authHeaders);
        if (!empty($authHeader) && preg_match($this->headerTokenPattern, $authHeader, $matches)) {
            return $matches[1];
        }

        /** @var mixed */
        $token = $request->getQueryParams()[$this->queryParameterName] ?? null;
        return is_string($token) ? $token : null;
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
     */
    public function withRealm(string $realm): self
    {
        $new = clone $this;
        $new->realm = $realm;
        return $new;
    }

    /**
     * @param string $headerName Authorization header name.
     */
    public function withHeaderName(string $headerName): self
    {
        $new = clone $this;
        $new->headerName = $headerName;
        return $new;
    }

    /**
     * @param string $headerTokenPattern Regular expression to use for getting a token from authorization header.
     * Token value should match first capturing group.
     */
    public function withHeaderTokenPattern(string $headerTokenPattern): self
    {
        $new = clone $this;
        $new->headerTokenPattern = $headerTokenPattern;
        return $new;
    }

    /**
     * @param string $queryParameterName Request parameter name to check for a token.
     */
    public function withQueryParameterName(string $queryParameterName): self
    {
        $new = clone $this;
        $new->queryParameterName = $queryParameterName;
        return $new;
    }

    /**
     * @param string $identifier Identifier to check claims for.
     */
    public function withIdentifier(string $identifier): self
    {
        $new = clone $this;
        $new->identifier = $identifier;
        return $new;
    }
}

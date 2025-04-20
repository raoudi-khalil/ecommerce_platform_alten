<?php

namespace App\Security;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JwtAuthenticator extends AbstractAuthenticator
{
    private string $jwtSecret;
    private string $jwtAlgo;

    public function __construct(
        private UserRepository $userRepository,
        ParameterBagInterface $params
    ) {
        $this->jwtSecret = $params->get('app.jwt_secret');
        $this->jwtAlgo   = $params->get('app.jwt_algo') ?? 'HS256';
    }

    public function supports(Request $request): ?bool
    {
        // Auth only if Authorization header with Bearer token is present
        return $request->headers->has('Authorization') &&
               str_starts_with($request->headers->get('Authorization'), 'Bearer ');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new AuthenticationException('No Bearer token provided');
        }

        $jwt = substr($authHeader, 7);

        try {
            $payload = JWT::decode($jwt, new Key($this->jwtSecret, $this->jwtAlgo));
        } catch (\Throwable $e) {
            throw new AuthenticationException('Invalid or expired JWT token');
        }

        // UserBadge: identifier is the email (or getUserIdentifier)
        return new SelfValidatingPassport(
            new UserBadge($payload->user, function (string $identifier) {
                $user = $this->userRepository->findOneBy(['email' => $identifier]);
                if (!$user) {
                    throw new AuthenticationException('User not found');
                }
                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?JsonResponse
    {
        // Let the request continue (null means allow)
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse(['error' => 'Unauthorized: ' . $exception->getMessage()], 401);
    }
}

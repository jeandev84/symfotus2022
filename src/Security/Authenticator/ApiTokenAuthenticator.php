<?php
namespace App\Security\Authenticator;

use App\Manager\UserManager;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;


class ApiTokenAuthenticator extends AbstractAuthenticator
{


    public function __construct(protected UserManager $userManager)
    {
    }



    public function supports(Request $request): ?bool
    {
         return true;
    }



    public function authenticate(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');
        $token = $extractor->extract($request);

        if ($token === null) {
            throw new CustomUserMessageAuthenticationException('No API token was provided');
        }

        return new SelfValidatingPassport(
            new UserBadge($token, function ($userIdentifier) {
                 return $this->userManager->findUserByToken($userIdentifier);
            })
        );
    }



    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
          return null;
    }



    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['message' => 'Invalid Token'], Response::HTTP_FORBIDDEN);
    }
}
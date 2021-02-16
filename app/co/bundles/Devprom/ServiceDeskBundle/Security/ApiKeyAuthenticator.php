<?php
namespace Devprom\ServiceDeskBundle\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface
{
    private $em = null;

    function __construct( EntityManager $em ) {
        $this->em = $em;
    }

    public function createToken(Request $request, $providerKey)
    {
        // look for an apikey query parameter
        $apiKey = $request->query->get('apikey');
        if (!$apiKey) return new AnonymousToken(null, 'anon.', ['IS_AUTHENTICATED_ANONYMOUSLY']);

        return new PreAuthenticatedToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $apiKey = $token->getCredentials();
        $user = $token->getUser();

        if ($user instanceof User) {
            return new PreAuthenticatedToken(
                $user,
                $apiKey,
                $providerKey,
                $user->getRoles()
            );
        }

        $users = $this->em->getRepository('DevpromServiceDeskBundle:User')->findAll();
        foreach( $users as $user ) {
            if ( md5(INSTALLATION_UID.$user->getUsername()) == $apiKey ) {
                return new PreAuthenticatedToken(
                    $user,
                    $apiKey,
                    $providerKey,
                    $user->getRoles()
                );
            }
            if ( md5(INSTALLATION_UID.$user->getEmail()) == $apiKey ) {
                return new PreAuthenticatedToken(
                    $user,
                    $apiKey,
                    $providerKey,
                    $user->getRoles()
                );
            }
        }
        return new AnonymousToken(null, 'anon.', ['IS_AUTHENTICATED_ANONYMOUSLY']);
    }
}
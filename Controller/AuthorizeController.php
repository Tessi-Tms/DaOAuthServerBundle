<?php

namespace Da\OAuthServerBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\OAuthServerBundle\Controller\AuthorizeController as BaseAuthorizeController;
use Da\OAuthServerBundle\Security\ClientProviderInterface;

/**
 * Controller handling authorization with authspaces.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 */
class AuthorizeController extends BaseAuthorizeController implements ClientProviderInterface
{
    /**
     * Authorize
     *
     * @Route("/oauth/v2/auth", name="fos_oauth_server_authorize")
     * @Method({"GET", "POST"})
     */
    public function authorizeAction(Request $request)
    {
        $client = $this->getClient();
        $authspace = $client->getAuthSpace()->getCode();

        if (null !== $request->request->get('accepted', null) || null !== $request->request->get('rejected', null)) {
            $request->attributes->add(array('authspace' => $authspace));
            return parent::authorizeAction($request);
        }

        return new RedirectResponse(
            sprintf(
                '%s?%s',
                $this->container->get('router')->generate(
                    'da_oauthserver_authorize_authorizeauthspace',
                    array('authspace' => $authspace)
                ),
                $this->retrieveQueryString($request)
            ),
            302
        );
    }

    /**
     * Authorize for an authspace.
     *
     * @Route("/oauth/v2/auth/{authspace}")
     * @Method({"GET", "POST"})
     */
    public function authorizeAuthSpaceAction(Request $request)
    {
        return parent::authorizeAction($request);
    }

    /**
     * Disconnect
     *
     * @Route("/oauth/v2/disconnect")
     * @Method({"GET"})
     */
    public function disconnectAction(Request $request)
    {
        $client = $this->getClient();
        $authspace = $client->getAuthSpace()->getCode();
        $redirectUri = $request->query->get('redirect_uri');

        if ($client->isTrusted()) {
            return new RedirectResponse(
                sprintf(
                    '%s?redirect_uri=%s',
                    $this->container->get('router')->generate(
                        'da_oauthserver_security_logoutauthspace',
                        array('authspace' => $authspace)
                    ),
                    urlencode($redirectUri)
                ),
                302
            );
        }

        return new RedirectResponse(
            sprintf(
                '%s?%s',
                $this->container->get('router')->generate(
                    'da_oauthserver_security_disconnectauthspace',
                    array('authspace' => $authspace)
                ),
                $this->retrieveQueryString($request)
            ),
            302
        );
    }

    /**
     * Retrieve the query string of the request.
     *
     * @param Request $request The resquest.
     *
     * @return string The query string.
     */
    public function retrieveQueryString(Request $request)
    {
        $queryString = '';

        $skippedItems = array(
            '_submit'  => true
        );

        foreach (array_merge($request->query->all(), $request->request->all()) as $name => $value) {
            if (!isset($skippedItems[$name])) {
                if (is_array($value)) {
                    foreach ($value as $subName => $subValue) {
                        if (is_array($subValue)) {
                            foreach ($subValue as $embeddedName => $embeddedValue) {
                                $queryString .= sprintf('%s[%s][%s]=%s&', $name, $subName, $embeddedName, urlencode($embeddedValue));
                            }
                        } else {
                            $queryString .= sprintf('%s[%s]=%s&', $name, $subName, urlencode($subValue));
                        }
                    }
                } else {
                    $queryString .= sprintf('%s=%s&', $name, urlencode($value));
                }
            }
        }

        return $queryString;
    }

    /**
     * {@inheritDoc}
     */
    public function getClient()
    {
        return parent::getClient();
    }
}

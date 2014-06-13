<?php

namespace Da\OAuthServerBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\OAuthServerBundle\Controller\AuthorizeController as BaseAuthorizeController;

/**
 * Controller handling authorization with authspaces.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 */
class AuthorizeController extends BaseAuthorizeController
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

        if (null !== $request->request->get('accepted', null) || null !== $request->request->get('rejected', null)) {
            $request->attributes->add(array('authspace' => $client->getAuthSpace()->getCode()));
            return parent::authorizeAction($request);
        }

        return new RedirectResponse(
            sprintf(
                '%s?%s',
                $this->container->get('router')->generate(
                    'da_oauthserver_authorize_authorizeauthspace',
                    array('authspace' => $client->getAuthSpace()->getCode())
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

        return new RedirectResponse(
            sprintf(
                '%s?%s',
                $this->container->get('router')->generate(
                    'da_oauthserver_security_disconnectauthspace',
                    array('authspace' => $client->getAuthSpace()->getCode())
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

        foreach (array_merge($request->query->all(), $request->request->all()) as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $subName => $subValue) {
                    $queryString .= sprintf('%s[%s]=%s&', $name, $subName, $subValue);
                }
            } else {
                $queryString .= sprintf('%s=%s&', $name, $value);
            }
        }

        return $queryString;
    }
}

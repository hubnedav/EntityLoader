<?php

declare(strict_types=1);

namespace Arachne\EntityLoader\Application;

use Nette\Application\BadRequestException;
use Nette\Application\Request;
use Nette\Application\Responses\ForwardResponse;
use Nette\Security\User;
use Nette\Utils\Random;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
trait EntityLoaderPresenterTrait
{
    /**
     * @var RequestEntityLoader
     */
    private $loader;

    /**
     * @var RequestEntityUnloader
     */
    private $unloader;

    /**
     * @var User|null
     */
    private $user;

    final public function injectEntityLoader(RequestEntityLoader $loader, RequestEntityUnloader $unloader, ?User $user = null): void
    {
        $this->loader = $loader;
        $this->unloader = $unloader;
        $this->user = $user;
    }

    /**
     * Stores request to session.
     *
     * @param mixed $request
     * @param mixed $expiration
     *
     * @return string
     */
    public function storeRequest($request = null, $expiration = '+ 10 minutes'): string
    {
        // Both parameters are optional.
        if ($request === null) {
            $request = $this->getRequest();
        } elseif (!$request instanceof Request) {
            $expiration = $request;
            $request = $this->getRequest();
        }

        assert($request instanceof Request);
        $request = clone $request;
        $params = $request->toArray();
        $this->unloader->filterOut($params);
        $request->setParameters($params);

        $session = $this->getSession('Arachne.Application/requests');
        do {
            $key = Random::generate(5);
        } while (isset($session[$key]));

        $session[$key] = [$this->user !== null ? $this->user->getId() : null, $request];
        $session->setExpiration($expiration, $key);

        return $key;
    }

    /**
     * Restores request from session.
     *
     * @param string $key
     */
    public function restoreRequest($key): void
    {
        $session = $this->getSession('Arachne.Application/requests');
        if (!isset($session[$key]) || ($this->user !== null && $session[$key][0] !== null && $session[$key][0] !== $this->user->getId())) {
            return;
        }
        $request = clone $session[$key][1];
        unset($session[$key]);

        try {
            $this->loader->filterIn($request);
        } catch (BadRequestException $e) {
            return;
        }
        $request->setFlag(Request::RESTORED, true);
        $parameters = $request->getParameters();
        $parameters[self::FLASH_KEY] = $this->getParameter(self::FLASH_KEY);
        $request->setParameters($parameters);
        $this->sendResponse(new ForwardResponse($request));
    }
}

<?php

declare(strict_types=1);

namespace Arachne\EntityLoader\Routing;

use Arachne\EntityLoader\Application\RequestEntityUnloader;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Http\IRequest;
use Nette\Http\Url;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class RouterWrapper implements IRouter
{
    /**
     * @var IRouter
     */
    private $router;

    /**
     * @var RequestEntityUnloader
     */
    private $unloader;

    /**
     * @var bool
     */
    private $envelopes;

    public function __construct(IRouter $router, RequestEntityUnloader $unloader, bool $envelopes)
    {
        $this->router = $router;
        $this->unloader = $unloader;
        $this->envelopes = $envelopes;
    }

    /**
     * {@inheritdoc}
     */
    public function match(IRequest $httpRequest): ?Request
    {
        return $this->router->match($httpRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function constructUrl(Request $request, Url $refUrl): ?string
    {
        $request = clone $request;
        $this->unloader->filterOut($request, $this->envelopes);

        return $this->router->constructUrl($request, $refUrl);
    }
}

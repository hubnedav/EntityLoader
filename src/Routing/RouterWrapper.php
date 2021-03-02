<?php

declare(strict_types=1);

namespace Arachne\EntityLoader\Routing;

use Arachne\EntityLoader\Application\RequestEntityUnloader;
use Nette\Http\IRequest;
use Nette\Http\UrlScript;
use Nette\Routing\Router;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class RouterWrapper implements Router
{
    /**
     * @var Router
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

    public function __construct(Router $router, RequestEntityUnloader $unloader, bool $envelopes)
    {
        $this->router = $router;
        $this->unloader = $unloader;
        $this->envelopes = $envelopes;
    }

    /**
     * {@inheritdoc}
     */
    public function match(IRequest $httpRequest): ?array
    {
        return $this->router->match($httpRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function constructUrl(array $params, UrlScript $refUrl): ?string
    {
        $this->unloader->filterOut($params, $this->envelopes);

        return $this->router->constructUrl($params, $refUrl);
    }
}

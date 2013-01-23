<?php

/**
 * This file is part of the EntityLoader extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and licence information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\EntityLoader;

/**
 * @author Jáchym Toušek
 */
class RouteList extends \Nette\Application\Routers\RouteList
{

	/** @var EntityLoader */
	protected $loader;

	/**
	 * @param EntityLoader $loader
	 * @param string $module
	 */
	public function __construct(EntityLoader $loader, $module = NULL)
	{
		parent::__construct($module);
		$this->loader = $loader;
	}

	/**
	 * Maps HTTP request to a Request object.
	 * @param \Nette\Http\IRequest $httpRequest
	 * @return \Nette\Application\Request|NULL
	 */
	public function match(\Nette\Http\IRequest $httpRequest)
	{
		$request = parent::match($httpRequest);
		if ($request && $this->loader->loadEntities($request)) {
			return $request;
		}
	}

	/**
	 * Constructs absolute URL from Request object.
	 * @param \Nette\Application\Request $request
	 * @param \Nette\Http\Url $refUrl
	 * @return string|NULL
	 */
	public function constructUrl(\Nette\Application\Request $request, \Nette\Http\Url $refUrl)
	{
		$parameters = $request->getParameters();
		if ($this->loader->removeEntities($request)) {
			$return = parent::constructUrl($request, $refUrl);
			$request->setParameters($parameters);
			return $return;
		}
	}

}

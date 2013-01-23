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
trait TPresenter
{

	/** @var EntityLoader */
	private $loader;

	/**
	 * @param \Arachne\EntityLoader\EntityLoader $loader
	 */
	final public function injectLoader(EntityLoader $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Stores current request to session.
	 * @param mixed $expiration
	 * @return string
	 */
	public function storeRequest($expiration = '+ 10 minutes')
	{
		$session = $this->getSession('Arachne.Application/requests');
		do {
			$key = \Nette\Utils\Strings::random(5);
		} while (isset($session[$key]));

		$request = clone $this->request;
		if (!$this->loader->removeEntities($request)) {
			throw new InvalidStateException("Failed to remove entities from request.");
		}
		$session[$key] = array($this->getUser()->getId(), $request);
		$session->setExpiration($expiration, $key);
		return $key;
	}

	/**
	 * Restores current request to session.
	 * @param string $key
	 * @return void
	 */
	public function restoreRequest($key)
	{
		$session = $this->getSession('Arachne.Application/requests');
		if (!isset($session[$key]) || ($session[$key][0] !== NULL && $session[$key][0] !== $this->getUser()->getId())) {
			return;
		}
		$request = $session[$key][1];
		unset($session[$key]);
		if (!$this->loader->loadEntities($request)) {
			return;
		}
		$request->setFlag(\Nette\Application\Request::RESTORED, TRUE);
		$params = $request->getParameters();
		$params[self::FLASH_KEY] = $this->getParameter(self::FLASH_KEY);
		$request->setParameters($params);
		$this->sendResponse(new \Nette\Application\Responses\ForwardResponse($request));
	}

}

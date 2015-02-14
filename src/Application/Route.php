<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\EntityLoader\Application;

use Nette\Application\Routers\Route as BaseRoute;
use Nette\InvalidArgumentException;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class Route extends BaseRoute
{

	/**
	 * {@inheritdoc}
	 */
	public function __construct($mask, $metadata = [], $flags = 0)
	{
		// Copied from Nette\Application\Routers\Route::__construct()
		if (is_string($metadata)) {
			$pos = strrpos($metadata, ':');
			if (!$pos) {
				throw new InvalidArgumentException("Second argument must be array or string in format Presenter:action, '$metadata' given.");
			}
			$metadata = [
				self::PRESENTER_KEY => substr($metadata, 0, $pos),
				'action' => $a === strlen($metadata) - 1 ? NULL : substr($metadata, $pos + 1),
			];
		} elseif ($metadata instanceof \Closure || $metadata instanceof Nette\Callback) {
			$metadata = array(
				self::PRESENTER_KEY => 'Nette:Micro',
				'callback' => $metadata,
			);
		}

		// Filter for handling Envelopes correctly
		$filter = function (array $parameters) use ($metadata) {
			foreach ($parameters as $key => & $value) {
				if ($value instanceof Envelope && !isset($metadata[$key][self::FILTER_OUT])) {
					$value = $value->getIdentifier();
				}
			}
			return $parameters;
		};

		// Hack to invoke the filter after custom global filter
		if (isset($metadata[NULL][self::FILTER_OUT])) {
			$custom = $metadata[NULL][self::FILTER_OUT];
			$metadata[NULL][self::FILTER_OUT] = function (array $parameters) use ($custom, $filter) {
				$parameters = call_user_func($custom, $parameters);
				if (!is_array($parameters)) {
					return $parameters;
				}
				return call_user_func($filter, $parameters);
			};
		} else {
			$metadata[NULL][self::FILTER_OUT] = $filter;
		}

		parent::__construct($mask, $metadata, $flags);
	}

}

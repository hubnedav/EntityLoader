<?php

declare(strict_types=1);

namespace Arachne\EntityLoader\Application;

use Arachne\EntityLoader\EntityUnloader;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class RequestEntityUnloader
{
    /**
     * @var EntityUnloader
     */
    private $entityUnloader;

    public function __construct(EntityUnloader $entityUnloader)
    {
        $this->entityUnloader = $entityUnloader;
    }

    public function filterOut(array &$params, bool $envelopes = false): void
    {
        foreach ($params as &$value) {
            if (is_object($value)) {
                $parameter = $this->entityUnloader->filterOut($value);
                $value = $envelopes ? new Envelope($value, $parameter) : $parameter;
            }
        }
    }
}

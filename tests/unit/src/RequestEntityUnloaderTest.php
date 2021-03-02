<?php

declare(strict_types=1);

namespace Tests\Unit;

use Arachne\EntityLoader\Application\Envelope;
use Arachne\EntityLoader\Application\RequestEntityUnloader;
use Arachne\EntityLoader\EntityUnloader;
use Codeception\Test\Unit;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Phpunit\Phony;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class RequestEntityUnloaderTest extends Unit
{
    /**
     * @var RequestEntityUnloader
     */
    private $requestEntityUnloader;

    /**
     * @var InstanceHandle
     */
    private $entityUnloaderHandle;

    protected function _before(): void
    {
        $this->entityUnloaderHandle = Phony::mock(EntityUnloader::class);
        $this->requestEntityUnloader = new RequestEntityUnloader($this->entityUnloaderHandle->get());
    }

    public function testFilterOut(): void
    {
        $stub = Phony::stub();
        $params = $this->createRequestParams($stub);

        $this->entityUnloaderHandle
            ->filterOut
            ->with($stub)
            ->returns('value');

        $this->requestEntityUnloader->filterOut($params);

        self::assertSame(
            [
               'entity' => 'value',
            ],
            $params
        );
    }

    public function testFilterOutEmptyMapping(): void
    {
        $params = $this->createRequestParams('value');

        $this->requestEntityUnloader->filterOut($params);

        self::assertSame(
            [
               'entity' => 'value',
            ],
            $params
        );
    }

    public function testFilterOutEnvelopes(): void
    {
        $stub = Phony::stub();
        $params = $this->createRequestParams($stub);

        $this->entityUnloaderHandle
            ->filterOut
            ->with($stub)
            ->returns('value');

        $this->requestEntityUnloader->filterOut($params, true);

        self::assertEquals(
            [
               'entity' => new Envelope($stub, 'value'),
            ],
            $params
        );
    }

    public function testFilterOutNullable(): void
    {
        $params = $this->createRequestParams(null);

        $this->requestEntityUnloader->filterOut($params);

        self::assertSame(
            [
               'entity' => null,
            ],
            $params
        );
    }

    /**
     * @param mixed $value
     */
    private function createRequestParams($value): array
    {
        return ['entity' => $value];
    }
}

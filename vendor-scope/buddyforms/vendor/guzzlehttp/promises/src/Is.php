<?php

namespace tk\GuzzleHttp\Promise;

final class Is
{
    /**
     * Returns true if a promise is pending.
     *
     * @return bool
     */
    public static function pending(\tk\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \tk\GuzzleHttp\Promise\PromiseInterface::PENDING;
    }
    /**
     * Returns true if a promise is fulfilled or rejected.
     *
     * @return bool
     */
    public static function settled(\tk\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() !== \tk\GuzzleHttp\Promise\PromiseInterface::PENDING;
    }
    /**
     * Returns true if a promise is fulfilled.
     *
     * @return bool
     */
    public static function fulfilled(\tk\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \tk\GuzzleHttp\Promise\PromiseInterface::FULFILLED;
    }
    /**
     * Returns true if a promise is rejected.
     *
     * @return bool
     */
    public static function rejected(\tk\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \tk\GuzzleHttp\Promise\PromiseInterface::REJECTED;
    }
}

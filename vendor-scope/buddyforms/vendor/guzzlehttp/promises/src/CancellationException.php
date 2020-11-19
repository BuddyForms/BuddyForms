<?php

namespace tk\GuzzleHttp\Promise;

/**
 * Exception that is set as the reason for a promise that has been cancelled.
 */
class CancellationException extends \tk\GuzzleHttp\Promise\RejectionException
{
}

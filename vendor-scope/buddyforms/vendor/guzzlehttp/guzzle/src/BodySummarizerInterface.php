<?php

namespace tk\GuzzleHttp;

use tk\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(\tk\Psr\Http\Message\MessageInterface $message) : ?string;
}

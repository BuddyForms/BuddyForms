<?php

namespace tk\GuzzleHttp;

use tk\Psr\Http\Message\MessageInterface;
final class BodySummarizer implements \tk\GuzzleHttp\BodySummarizerInterface
{
    /**
     * @var int|null
     */
    private $truncateAt;
    public function __construct(int $truncateAt = null)
    {
        $this->truncateAt = $truncateAt;
    }
    /**
     * Returns a summarized message body.
     */
    public function summarize(\tk\Psr\Http\Message\MessageInterface $message) : ?string
    {
        return $this->truncateAt === null ? \tk\GuzzleHttp\Psr7\Message::bodySummary($message) : \tk\GuzzleHttp\Psr7\Message::bodySummary($message, $this->truncateAt);
    }
}

<?php

namespace tk\GuzzleHttp\Handler;

use tk\GuzzleHttp\Exception\ConnectException;
use tk\GuzzleHttp\Exception\RequestException;
use tk\GuzzleHttp\Promise as P;
use tk\GuzzleHttp\Promise\FulfilledPromise;
use tk\GuzzleHttp\Promise\PromiseInterface;
use tk\GuzzleHttp\Psr7;
use tk\GuzzleHttp\TransferStats;
use tk\GuzzleHttp\Utils;
use tk\Psr\Http\Message\RequestInterface;
use tk\Psr\Http\Message\ResponseInterface;
use tk\Psr\Http\Message\StreamInterface;
use tk\Psr\Http\Message\UriInterface;
/**
 * HTTP handler that uses PHP's HTTP stream wrapper.
 *
 * @final
 */
class StreamHandler
{
    /**
     * @var array
     */
    private $lastHeaders = [];
    /**
     * Sends an HTTP request.
     *
     * @param RequestInterface $request Request to send.
     * @param array            $options Request transfer options.
     */
    public function __invoke(\tk\Psr\Http\Message\RequestInterface $request, array $options) : \tk\GuzzleHttp\Promise\PromiseInterface
    {
        // Sleep if there is a delay specified.
        if (isset($options['delay'])) {
            \usleep($options['delay'] * 1000);
        }
        $startTime = isset($options['on_stats']) ? \tk\GuzzleHttp\Utils::currentTime() : null;
        try {
            // Does not support the expect header.
            $request = $request->withoutHeader('Expect');
            // Append a content-length header if body size is zero to match
            // cURL's behavior.
            if (0 === $request->getBody()->getSize()) {
                $request = $request->withHeader('Content-Length', '0');
            }
            return $this->createResponse($request, $options, $this->createStream($request, $options), $startTime);
        } catch (\InvalidArgumentException $e) {
            throw $e;
        } catch (\Exception $e) {
            // Determine if the error was a networking error.
            $message = $e->getMessage();
            // This list can probably get more comprehensive.
            if (\false !== \strpos($message, 'getaddrinfo') || \false !== \strpos($message, 'Connection refused') || \false !== \strpos($message, "couldn't connect to host") || \false !== \strpos($message, "connection attempt failed")) {
                $e = new \tk\GuzzleHttp\Exception\ConnectException($e->getMessage(), $request, $e);
            } else {
                $e = \tk\GuzzleHttp\Exception\RequestException::wrapException($request, $e);
            }
            $this->invokeStats($options, $request, $startTime, null, $e);
            return \tk\GuzzleHttp\Promise\Create::rejectionFor($e);
        }
    }
    private function invokeStats(array $options, \tk\Psr\Http\Message\RequestInterface $request, ?float $startTime, \tk\Psr\Http\Message\ResponseInterface $response = null, \Throwable $error = null) : void
    {
        if (isset($options['on_stats'])) {
            $stats = new \tk\GuzzleHttp\TransferStats($request, $response, \tk\GuzzleHttp\Utils::currentTime() - $startTime, $error, []);
            $options['on_stats']($stats);
        }
    }
    /**
     * @param resource $stream
     */
    private function createResponse(\tk\Psr\Http\Message\RequestInterface $request, array $options, $stream, ?float $startTime) : \tk\GuzzleHttp\Promise\PromiseInterface
    {
        $hdrs = $this->lastHeaders;
        $this->lastHeaders = [];
        $parts = \explode(' ', \array_shift($hdrs), 3);
        $ver = \explode('/', $parts[0])[1];
        $status = (int) $parts[1];
        $reason = $parts[2] ?? null;
        $headers = \tk\GuzzleHttp\Utils::headersFromLines($hdrs);
        [$stream, $headers] = $this->checkDecode($options, $headers, $stream);
        $stream = \tk\GuzzleHttp\Psr7\Utils::streamFor($stream);
        $sink = $stream;
        if (\strcasecmp('HEAD', $request->getMethod())) {
            $sink = $this->createSink($stream, $options);
        }
        $response = new \tk\GuzzleHttp\Psr7\Response($status, $headers, $sink, $ver, $reason);
        if (isset($options['on_headers'])) {
            try {
                $options['on_headers']($response);
            } catch (\Exception $e) {
                $msg = 'An error was encountered during the on_headers event';
                $ex = new \tk\GuzzleHttp\Exception\RequestException($msg, $request, $response, $e);
                return \tk\GuzzleHttp\Promise\Create::rejectionFor($ex);
            }
        }
        // Do not drain when the request is a HEAD request because they have
        // no body.
        if ($sink !== $stream) {
            $this->drain($stream, $sink, $response->getHeaderLine('Content-Length'));
        }
        $this->invokeStats($options, $request, $startTime, $response, null);
        return new \tk\GuzzleHttp\Promise\FulfilledPromise($response);
    }
    private function createSink(\tk\Psr\Http\Message\StreamInterface $stream, array $options) : \tk\Psr\Http\Message\StreamInterface
    {
        if (!empty($options['stream'])) {
            return $stream;
        }
        $sink = $options['sink'] ?? \fopen('php://temp', 'r+');
        return \is_string($sink) ? new \tk\GuzzleHttp\Psr7\LazyOpenStream($sink, 'w+') : \tk\GuzzleHttp\Psr7\Utils::streamFor($sink);
    }
    /**
     * @param resource $stream
     */
    private function checkDecode(array $options, array $headers, $stream) : array
    {
        // Automatically decode responses when instructed.
        if (!empty($options['decode_content'])) {
            $normalizedKeys = \tk\GuzzleHttp\Utils::normalizeHeaderKeys($headers);
            if (isset($normalizedKeys['content-encoding'])) {
                $encoding = $headers[$normalizedKeys['content-encoding']];
                if ($encoding[0] === 'gzip' || $encoding[0] === 'deflate') {
                    $stream = new \tk\GuzzleHttp\Psr7\InflateStream(\tk\GuzzleHttp\Psr7\Utils::streamFor($stream));
                    $headers['x-encoded-content-encoding'] = $headers[$normalizedKeys['content-encoding']];
                    // Remove content-encoding header
                    unset($headers[$normalizedKeys['content-encoding']]);
                    // Fix content-length header
                    if (isset($normalizedKeys['content-length'])) {
                        $headers['x-encoded-content-length'] = $headers[$normalizedKeys['content-length']];
                        $length = (int) $stream->getSize();
                        if ($length === 0) {
                            unset($headers[$normalizedKeys['content-length']]);
                        } else {
                            $headers[$normalizedKeys['content-length']] = [$length];
                        }
                    }
                }
            }
        }
        return [$stream, $headers];
    }
    /**
     * Drains the source stream into the "sink" client option.
     *
     * @param string $contentLength Header specifying the amount of
     *                              data to read.
     *
     * @throws \RuntimeException when the sink option is invalid.
     */
    private function drain(\tk\Psr\Http\Message\StreamInterface $source, \tk\Psr\Http\Message\StreamInterface $sink, string $contentLength) : \tk\Psr\Http\Message\StreamInterface
    {
        // If a content-length header is provided, then stop reading once
        // that number of bytes has been read. This can prevent infinitely
        // reading from a stream when dealing with servers that do not honor
        // Connection: Close headers.
        \tk\GuzzleHttp\Psr7\Utils::copyToStream($source, $sink, \strlen($contentLength) > 0 && (int) $contentLength > 0 ? (int) $contentLength : -1);
        $sink->seek(0);
        $source->close();
        return $sink;
    }
    /**
     * Create a resource and check to ensure it was created successfully
     *
     * @param callable $callback Callable that returns stream resource
     *
     * @return resource
     *
     * @throws \RuntimeException on error
     */
    private function createResource(callable $callback)
    {
        $errors = [];
        \set_error_handler(static function ($_, $msg, $file, $line) use(&$errors) : bool {
            $errors[] = ['message' => $msg, 'file' => $file, 'line' => $line];
            return \true;
        });
        $resource = $callback();
        \restore_error_handler();
        if (!$resource) {
            $message = 'Error creating resource: ';
            foreach ($errors as $err) {
                foreach ($err as $key => $value) {
                    $message .= "[{$key}] {$value}" . \PHP_EOL;
                }
            }
            throw new \RuntimeException(\trim($message));
        }
        return $resource;
    }
    /**
     * @return resource
     */
    private function createStream(\tk\Psr\Http\Message\RequestInterface $request, array $options)
    {
        static $methods;
        if (!$methods) {
            $methods = \array_flip(\get_class_methods(__CLASS__));
        }
        // HTTP/1.1 streams using the PHP stream wrapper require a
        // Connection: close header
        if ($request->getProtocolVersion() == '1.1' && !$request->hasHeader('Connection')) {
            $request = $request->withHeader('Connection', 'close');
        }
        // Ensure SSL is verified by default
        if (!isset($options['verify'])) {
            $options['verify'] = \true;
        }
        $params = [];
        $context = $this->getDefaultContext($request);
        if (isset($options['on_headers']) && !\is_callable($options['on_headers'])) {
            throw new \InvalidArgumentException('on_headers must be callable');
        }
        if (!empty($options)) {
            foreach ($options as $key => $value) {
                $method = "add_{$key}";
                if (isset($methods[$method])) {
                    $this->{$method}($request, $context, $value, $params);
                }
            }
        }
        if (isset($options['stream_context'])) {
            if (!\is_array($options['stream_context'])) {
                throw new \InvalidArgumentException('stream_context must be an array');
            }
            $context = \array_replace_recursive($context, $options['stream_context']);
        }
        // Microsoft NTLM authentication only supported with curl handler
        if (isset($options['auth'][2]) && 'ntlm' === $options['auth'][2]) {
            throw new \InvalidArgumentException('Microsoft NTLM authentication only supported with curl handler');
        }
        $uri = $this->resolveHost($request, $options);
        $contextResource = $this->createResource(static function () use($context, $params) {
            return \stream_context_create($context, $params);
        });
        return $this->createResource(function () use($uri, &$http_response_header, $contextResource, $context, $options, $request) {
            $resource = \fopen((string) $uri, 'r', \false, $contextResource);
            $this->lastHeaders = $http_response_header;
            if (\false === $resource) {
                throw new \tk\GuzzleHttp\Exception\ConnectException(\sprintf('Connection refused for URI %s', $uri), $request, null, $context);
            }
            if (isset($options['read_timeout'])) {
                $readTimeout = $options['read_timeout'];
                $sec = (int) $readTimeout;
                $usec = ($readTimeout - $sec) * 100000;
                \stream_set_timeout($resource, $sec, $usec);
            }
            return $resource;
        });
    }
    private function resolveHost(\tk\Psr\Http\Message\RequestInterface $request, array $options) : \tk\Psr\Http\Message\UriInterface
    {
        $uri = $request->getUri();
        if (isset($options['force_ip_resolve']) && !\filter_var($uri->getHost(), \FILTER_VALIDATE_IP)) {
            if ('v4' === $options['force_ip_resolve']) {
                $records = \dns_get_record($uri->getHost(), \DNS_A);
                if (\false === $records || !isset($records[0]['ip'])) {
                    throw new \tk\GuzzleHttp\Exception\ConnectException(\sprintf("Could not resolve IPv4 address for host '%s'", $uri->getHost()), $request);
                }
                return $uri->withHost($records[0]['ip']);
            }
            if ('v6' === $options['force_ip_resolve']) {
                $records = \dns_get_record($uri->getHost(), \DNS_AAAA);
                if (\false === $records || !isset($records[0]['ipv6'])) {
                    throw new \tk\GuzzleHttp\Exception\ConnectException(\sprintf("Could not resolve IPv6 address for host '%s'", $uri->getHost()), $request);
                }
                return $uri->withHost('[' . $records[0]['ipv6'] . ']');
            }
        }
        return $uri;
    }
    private function getDefaultContext(\tk\Psr\Http\Message\RequestInterface $request) : array
    {
        $headers = '';
        foreach ($request->getHeaders() as $name => $value) {
            foreach ($value as $val) {
                $headers .= "{$name}: {$val}\r\n";
            }
        }
        $context = ['http' => ['method' => $request->getMethod(), 'header' => $headers, 'protocol_version' => $request->getProtocolVersion(), 'ignore_errors' => \true, 'follow_location' => 0]];
        $body = (string) $request->getBody();
        if (!empty($body)) {
            $context['http']['content'] = $body;
            // Prevent the HTTP handler from adding a Content-Type header.
            if (!$request->hasHeader('Content-Type')) {
                $context['http']['header'] .= "Content-Type:\r\n";
            }
        }
        $context['http']['header'] = \rtrim($context['http']['header']);
        return $context;
    }
    /**
     * @param mixed $value as passed via Request transfer options.
     */
    private function add_proxy(\tk\Psr\Http\Message\RequestInterface $request, array &$options, $value, array &$params) : void
    {
        if (!\is_array($value)) {
            $options['http']['proxy'] = $value;
        } else {
            $scheme = $request->getUri()->getScheme();
            if (isset($value[$scheme])) {
                if (!isset($value['no']) || !\tk\GuzzleHttp\Utils::isHostInNoProxy($request->getUri()->getHost(), $value['no'])) {
                    $options['http']['proxy'] = $value[$scheme];
                }
            }
        }
    }
    /**
     * @param mixed $value as passed via Request transfer options.
     */
    private function add_timeout(\tk\Psr\Http\Message\RequestInterface $request, array &$options, $value, array &$params) : void
    {
        if ($value > 0) {
            $options['http']['timeout'] = $value;
        }
    }
    /**
     * @param mixed $value as passed via Request transfer options.
     */
    private function add_verify(\tk\Psr\Http\Message\RequestInterface $request, array &$options, $value, array &$params) : void
    {
        if ($value === \false) {
            $options['ssl']['verify_peer'] = \false;
            $options['ssl']['verify_peer_name'] = \false;
            return;
        }
        if (\is_string($value)) {
            $options['ssl']['cafile'] = $value;
            if (!\file_exists($value)) {
                throw new \RuntimeException("SSL CA bundle not found: {$value}");
            }
        } elseif ($value !== \true) {
            throw new \InvalidArgumentException('Invalid verify request option');
        }
        $options['ssl']['verify_peer'] = \true;
        $options['ssl']['verify_peer_name'] = \true;
        $options['ssl']['allow_self_signed'] = \false;
    }
    /**
     * @param mixed $value as passed via Request transfer options.
     */
    private function add_cert(\tk\Psr\Http\Message\RequestInterface $request, array &$options, $value, array &$params) : void
    {
        if (\is_array($value)) {
            $options['ssl']['passphrase'] = $value[1];
            $value = $value[0];
        }
        if (!\file_exists($value)) {
            throw new \RuntimeException("SSL certificate not found: {$value}");
        }
        $options['ssl']['local_cert'] = $value;
    }
    /**
     * @param mixed $value as passed via Request transfer options.
     */
    private function add_progress(\tk\Psr\Http\Message\RequestInterface $request, array &$options, $value, array &$params) : void
    {
        self::addNotification($params, static function ($code, $a, $b, $c, $transferred, $total) use($value) {
            if ($code == \STREAM_NOTIFY_PROGRESS) {
                $value($total, $transferred, null, null);
            }
        });
    }
    /**
     * @param mixed $value as passed via Request transfer options.
     */
    private function add_debug(\tk\Psr\Http\Message\RequestInterface $request, array &$options, $value, array &$params) : void
    {
        if ($value === \false) {
            return;
        }
        static $map = [\STREAM_NOTIFY_CONNECT => 'CONNECT', \STREAM_NOTIFY_AUTH_REQUIRED => 'AUTH_REQUIRED', \STREAM_NOTIFY_AUTH_RESULT => 'AUTH_RESULT', \STREAM_NOTIFY_MIME_TYPE_IS => 'MIME_TYPE_IS', \STREAM_NOTIFY_FILE_SIZE_IS => 'FILE_SIZE_IS', \STREAM_NOTIFY_REDIRECTED => 'REDIRECTED', \STREAM_NOTIFY_PROGRESS => 'PROGRESS', \STREAM_NOTIFY_FAILURE => 'FAILURE', \STREAM_NOTIFY_COMPLETED => 'COMPLETED', \STREAM_NOTIFY_RESOLVE => 'RESOLVE'];
        static $args = ['severity', 'message', 'message_code', 'bytes_transferred', 'bytes_max'];
        $value = \tk\GuzzleHttp\Utils::debugResource($value);
        $ident = $request->getMethod() . ' ' . $request->getUri()->withFragment('');
        self::addNotification($params, static function (int $code, ...$passed) use($ident, $value, $map, $args) : void {
            \fprintf($value, '<%s> [%s] ', $ident, $map[$code]);
            foreach (\array_filter($passed) as $i => $v) {
                \fwrite($value, $args[$i] . ': "' . $v . '" ');
            }
            \fwrite($value, "\n");
        });
    }
    private static function addNotification(array &$params, callable $notify) : void
    {
        // Wrap the existing function if needed.
        if (!isset($params['notification'])) {
            $params['notification'] = $notify;
        } else {
            $params['notification'] = self::callArray([$params['notification'], $notify]);
        }
    }
    private static function callArray(array $functions) : callable
    {
        return static function (...$args) use($functions) {
            foreach ($functions as $fn) {
                $fn(...$args);
            }
        };
    }
}

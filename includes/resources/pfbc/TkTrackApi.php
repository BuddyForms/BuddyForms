<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class TkTrackApi {

	protected $api_version = 'v1';

	/**
	 * Tracking API URL
	 *
	 * @var string
	 */
	protected $api_url_base = 'https://themekraft.com/wp-json/freev/';

	/**
	 * API resources
	 *
	 * @var array
	 */
	protected $resources = array();

	/**
	 * Additional markup
	 *
	 * @var array
	 */
	protected $markup = array();

	/**
	 * Debug
	 *
	 * @var boolean
	 */
	protected $debug;

	/**
	 * Debug
	 *
	 * @var boolean
	 */
	protected $debug_logger;

	/**
	 * Guzzle Http Client
	 *
	 * @var object
	 */
	protected $client;

	/**
	 * Constructor for Tracking instance
	 *
	 * @param boolean $debug if log debug info.
	 *
	 * @throws Exception
	 */
	public function __construct( $debug = false ) {

		$this->debug  = $debug;
		$this->client = new Client();

		if ( $debug ) {
			$this->debug_logger = new Logger( 'tk-debug' );
			$this->debug_logger->pushHandler( new StreamHandler( __DIR__ . '/logs/debug.log', Logger::DEBUG ) );
		}
	}

	/**
	 * Send a tracking event
	 *
	 * @param $options
	 * @param string $product
	 *
	 * @return array|bool|mixed|object
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function track( $options, $product = 'buddyforms' ) {
		if ( ! is_array( $options ) ) {
			throw new \InvalidArgumentException;
		}

		$request = $this->api_version . sprintf( '/track/%s', $product );

		return $this->make_request( $request, 'POST', $options );
	}

	/**
	 * @param $endpoint
	 * @param $method
	 * @param array $args
	 *
	 * @return array|bool|mixed|object
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	private function make_request( $endpoint, $method, $args = array() ) {

		if ( ! is_string( $endpoint ) || ! is_string( $method ) || ! is_array( $args ) ) {
			throw new \InvalidArgumentException;
		}

		$url = $this->api_url_base . $endpoint;

		$this->create_log( sprintf( "Making request on %s.", $url ) );

		$request_body = json_encode( $args );

		$this->create_log( sprintf( "%s, Request body: %s", $method, $request_body ) );

		if ( $method === "GET" ) {
			if ( $args ) {
				$url .= '?' . http_build_query( $args );
			}
			$request = new Request( $method, $url );
		} else {
			$request = new Request( $method, $url, array(
				'Content-Type'   => 'application/json',
				'Content-Length' => strlen( $request_body )
			), $request_body );
		}

		$response = $this->client->send( $request, [
			'exceptions' => false
		] );

		$status_code = $response->getStatusCode();

		// If not between 200 and 300
		if ( ! preg_match( "/^[2-3][0-9]{2}/", $status_code ) ) {
			$this->create_log( sprintf( "Response code is %s.", $status_code ) );

			return false;
		}

		$response_body = json_decode( $response->getBody()->getContents() );

		if ( $response_body ) {
			$this->create_log( "Finish request successfully." );

			return $response_body;
		}

		$this->create_log( "Failed to finish request." );

		return false;

	}

	private function create_log( $message ) {
		if ( $this->debug ) {
			$this->debug_logger->info( $message );
		}
	}

}
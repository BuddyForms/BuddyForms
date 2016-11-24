<?php
	if ( ! class_exists( 'Freemius_Exception' ) ) {
		exit;
	}

/**
 * Class Freemius_OAuthException
 */
class Freemius_OAuthException extends Freemius_Exception
    {
	/**
	 * Freemius_OAuthException constructor.
	 *
	 * @param array $pResult
	 */
	public function __construct($pResult)
        {
            parent::__construct($pResult);
        }
    }
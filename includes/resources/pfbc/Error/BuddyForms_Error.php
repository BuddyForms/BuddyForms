<?php

class BuddyForms_Error extends WP_Error {
	/**
	 * @var string
	 */
	private $form_slug;

	public function __construct( $code = '', $message = '', $data = '', $form_slug = '' ) {
		$this->form_slug = $form_slug;

		parent::__construct( $code, $message, $data );
	}

	/**
	 * @return string
	 */
	public function get_form_slug() {
		return $this->form_slug;
	}

	/**
	 * @param string $form_slug
	 */
	public function set_form_slug( $form_slug ) {
		$this->form_slug = $form_slug;
	}
	public function get_error_message( $code = '' ) {
		if ( empty( $code ) ) {
			$code = $this->get_error_code();
		}
		$messages = $this->get_error_messages( $code );
		if ( empty( $messages ) ) {
			return '';
		}

		$first_item = reset( $messages );
		$first_key  = key( $messages );
		if ( strpos( $first_item, $first_key ) !== false ) {
			return $first_item;
		}
		return key( $messages ) . ': ' . $first_item;
	}

	public function add( $code, $message, $data = '', $form_slug = '' ) {
		if ( ! empty( $form_slug ) ) {
			$this->set_form_slug( $form_slug );
		}

		if ( ! empty( $data ) ) {
			$this->error_data[ $code ]      = $data;
			$this->errors[ $code ][ $data ] = $message;
		} else {
			$this->errors[ $code ][] = $message;
		}
	}


}

<?php
/**
 * @package     Freemius
 * @copyright   Copyright (c) 2015, Freemius, Inc.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FS_Plugin_Info
 */
class FS_Plugin_Info extends FS_Entity {
	/**
	 * @var
	 */
	public $plugin_id;
	/**
	 * @var
	 */
	public $description;
	/**
	 * @var
	 */
	public $short_description;
	/**
	 * @var
	 */
	public $banner_url;
	/**
	 * @var
	 */
	public $card_banner_url;
	/**
	 * @var
	 */
	public $selling_point_0;
	/**
	 * @var
	 */
	public $selling_point_1;
	/**
	 * @var
	 */
	public $selling_point_2;
	/**
	 * @var
	 */
	public $screenshots;

	/**
	 * @param stdClass|bool $plugin_info
	 */
	function __construct( $plugin_info = false ) {
		parent::__construct( $plugin_info );
	}

	/**
	 * @return string
	 */
	static function get_type() {
		return 'plugin';
	}
}
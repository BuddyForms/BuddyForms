<?php
	/**
	 * @package     Freemius
	 * @copyright   Copyright (c) 2015, Freemius, Inc.
	 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 * @since       1.0.4
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

/**
 * Class FS_Plugin_Tag
 */
class FS_Plugin_Tag extends FS_Entity {
	/**
	 * @var
	 */
	public $version;
	/**
	 * @var
	 */
	public $url;

	/**
	 * FS_Plugin_Tag constructor.
	 *
	 * @param bool $tag
	 */
	function __construct( $tag = false ) {
			parent::__construct( $tag );
		}

	/**
	 * @return string
	 */
	static function get_type() {
			return 'tag';
		}
	}
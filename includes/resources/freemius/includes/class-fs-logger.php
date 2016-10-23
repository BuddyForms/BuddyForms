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
 * Class FS_Logger
 */
class FS_Logger {
	/**
	 * @var array
	 */
	private static $LOGGERS = array();
	/**
	 * @var array
	 */
	private static $LOG = array();
	/**
	 * @var int
	 */
	private static $CNT = 0;
	/**
	 * @var bool
	 */
	private static $_HOOKED_FOOTER = false;
	/**
	 * @var
	 */
	private $_id;
	/**
	 * @var bool
	 */
	private $_on = false;
	/**
	 * @var bool
	 */
	private $_echo = false;
	/**
	 * @var bool|int
	 */
	private $_file_start = 0;

	/**
	 * FS_Logger constructor.
	 *
	 * @param $id
	 * @param bool $on
	 * @param bool $echo
	 */
	private function __construct( $id, $on = false, $echo = false ) {
		$this->_id = $id;

		$bt     = debug_backtrace();
		$caller = $bt[2];

		$this->_file_start = strpos( $caller['file'], 'plugins' ) + strlen( 'plugins/' );

		if ( $on ) {
			$this->on();
		}
		if ( $echo ) {
			$this->echo_on();
		}
	}

	function on() {
		$this->_on = true;

		self::_hook_footer();
	}

	private static function _hook_footer() {
		if ( self::$_HOOKED_FOOTER ) {
			return;
		}

		if ( is_admin() ) {
			add_action( 'admin_footer', 'FS_Logger::dump', 100 );
		} else {
			add_action( 'wp_footer', 'FS_Logger::dump', 100 );
		}
	}

	function echo_on() {
		$this->on();

		$this->_echo = true;
	}

	/**
	 * @param string $id
	 * @param bool $on
	 * @param bool $echo
	 *
	 * @return FS_Logger
	 */
	public static function get_logger( $id, $on = false, $echo = false ) {
		$id = strtolower( $id );

		if ( ! isset( self::$LOGGERS[ $id ] ) ) {
			self::$LOGGERS[ $id ] = new FS_Logger( $id, $on, $echo );
		}

		return self::$LOGGERS[ $id ];
	}

	static function dump() {
		?>
		<!-- BEGIN: Freemius PHP Console Log -->
		<script type="text/javascript">
			<?php
			foreach ( self::$LOG as $log ) {
				echo 'console.' . $log['type'] . '(' . json_encode( self::format( $log, false ) ) . ')' . "\n";
			}
			?>
		</script>
		<!-- END: Freemius PHP Console Log -->
		<?php
	}

	/**
	 * @param $log
	 * @param bool $show_type
	 *
	 * @return string
	 */
	private static function format( $log, $show_type = true ) {
		return '[' . str_pad( $log['cnt'], strlen( self::$CNT ), '0', STR_PAD_LEFT ) . '] [' . $log['logger']->_id . '] ' . ( $show_type ? '[' . $log['type'] . ']' : '' ) . $log['function'] . ' >> ' . $log['msg'] . ( isset( $log['file'] ) ? ' (' . substr( $log['file'], $log['logger']->_file_start ) . ' ' . $log['line'] . ') ' : '' ) . ' [' . $log['timestamp'] . ']';
	}

	/**
	 * @return array
	 */
	static function get_log() {
		return self::$LOG;
	}

	function get_id() {
		return $this->_id;
	}

	/**
	 * @return bool|int
	 */
	function get_file() {
		return $this->_file_start;
	}

	/**
	 * @param $message
	 * @param bool $wrapper
	 */
	function log( $message, $wrapper = false ) {
		$this->_log( $message, 'log', $wrapper );
	}

	/**
	 * @param $message
	 * @param string $type
	 * @param $wrapper
	 */
	private function _log( &$message, $type = 'log', $wrapper ) {
		if ( ! $this->is_on() ) {
			return;
		}

		$bt    = debug_backtrace();
		$depth = $wrapper ? 3 : 2;
		while ( $depth < count( $bt ) - 1 && 'eval' === $bt[ $depth ]['function'] ) {
			$depth ++;
		}

		$caller = $bt[ $depth ];

		$log = array_merge( $caller, array(
			'cnt'       => self::$CNT ++,
			'logger'    => $this,
			'timestamp' => microtime( true ),
			'type'      => $type,
			'msg'       => $message,
		) );

		self::$LOG[] = $log;

		if ( $this->is_echo_on() ) {
			echo self::format_html( $log ) . "\n";
		}
	}

	/**
	 * @return bool
	 */
	function is_on() {
		return $this->_on;
	}

	/**
	 * @return bool
	 */
	function is_echo_on() {
		return $this->_echo;
	}

	/**
	 * @param $log
	 *
	 * @return string
	 */
	private static function format_html( $log ) {
		return '<div style="font-size: 11px; padding: 3px; background: #ccc; margin-bottom: 3px;">[' . $log['cnt'] . '] [' . $log['logger']->_id . '] [' . $log['type'] . '] <b><code style="color: blue;">' . $log['function'] . '</code> >> <b style="color: darkorange;">' . $log['msg'] . '</b></b>' . ( isset( $log['file'] ) ? ' (' . substr( $log['file'], $log['logger']->_file_start ) . ' ' . $log['line'] . ')' : '' ) . ' [' . $log['timestamp'] . ']</div>';
	}

	/**
	 * @param $message
	 * @param bool $wrapper
	 */
	function info( $message, $wrapper = false ) {
		$this->_log( $message, 'info', $wrapper );
	}

	/**
	 * @param $message
	 * @param bool $wrapper
	 */
	function warn( $message, $wrapper = false ) {
		$this->_log( $message, 'warn', $wrapper );
	}

	/**
	 * @param $message
	 * @param bool $wrapper
	 */
	function error( $message, $wrapper = false ) {
		$this->_log( $message, 'error', $wrapper );
	}

	/**
	 * @param string $message
	 * @param bool $wrapper
	 */
	function entrance( $message = '', $wrapper = false ) {
		$msg = 'Entrance' . ( empty( $message ) ? '' : ' > ' ) . $message;

		$this->_log( $msg, 'log', $wrapper );
	}

	/**
	 * @param string $message
	 * @param bool $wrapper
	 */
	function departure( $message = '', $wrapper = false ) {
		$msg = 'Departure' . ( empty( $message ) ? '' : ' > ' ) . $message;

		$this->_log( $msg, 'log', $wrapper );
	}
}
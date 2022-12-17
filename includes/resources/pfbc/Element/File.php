<?php

/**
 * Class Element_File
 */
class Element_File extends Element_Textbox {
	/**
	 * @var int
	 */
	public $bootstrapVersion = 3;
	/**
	 * @var array
	 */
	protected $_attributes = array( 'type' => 'file' );

	public function render() {
		ob_start();
		parent::render();
		$box = ob_get_contents();
		ob_end_clean();
		if ( $this->bootstrapVersion == 3 ) {
			echo wp_kses( $box, buddyforms_wp_kses_allowed_atts() );
		} else {
			echo wp_kses(
				preg_replace(
					'/(.*)(<input .*\/>)(.*)/i',
					'${1}<label class="file">${2}<span class="file-custom"></span></label>${3}',
					$box
				),
				buddyforms_wp_kses_allowed_atts()
			);
		}
	}
}

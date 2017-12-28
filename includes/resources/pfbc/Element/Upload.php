<?php

/**
 * Class Element_Upload
 */
class Element_Upload extends Element_Textbox {
	/**
	 * @var int
	 */
	public $bootstrapVersion = 3;
	/**
	 * @var array
	 */
	protected $_attributes = array( "type" => "file" );

	public function render() {
		ob_start();
		parent::render();
		$box = ob_get_contents();
		ob_end_clean();
		if ( $this->bootstrapVersion == 3 ) {
			echo $box;
		} else {
			echo preg_replace( "/(.*)(<input .*\/>)(.*)/i",
				'${1}<label class="file">${2}<span class="file-custom"></span></label>${3}', $box );
		}
	}

	function renderJS() {
		$id = $this->getAttribute( 'id' );
		echo 'jQuery("#' . $id . '").dropzone();';
	}
}

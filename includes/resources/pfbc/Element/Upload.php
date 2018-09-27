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
	protected $_attributes = array(
		"type"           => "file",
		"file_limit"     => "",
		"accepted_files" => "",
		"multiple_files" => "",
		"delete_files"   => "",
		"description"    => "",
		"mandatory"      => ""
	);

	public function render() {
		global $buddyforms, $post_id;
		ob_start();
		parent::render();
		$box = ob_get_contents();
		ob_end_clean();

		$id     = $this->getAttribute( 'id' );
		$action = isset( $_GET['action'] ) ? $_GET['action'] : "";
		$entry  = isset( $_GET['entry'] ) ? $_GET['entry'] : "";
		$page   = isset( $_GET['page'] ) ? $_GET['page'] : "";

		//If Entry is empty we check if we are in a edit entry page
		if ( empty( $entry ) ) {
			$entry = isset( $_GET['post'] ) ? $_GET['post'] : "";
		}

		$column_val     = "";
		$result         = "";
		$result_value   = "";
		$entries        = array();
		$entries_result = "";
		if ( $post_id > 0 ) {
			$column_val = get_post_meta( $post_id, $id, true );

			$attachmet_id = explode( ",", $column_val );
			foreach ( $attachmet_id as $id_value ) {

				$metadata = wp_prepare_attachment_for_js( $id_value );
				if ( $metadata != null ) {
					$url                     = wp_get_attachment_thumb_url( $id_value );
					$result                  .= $id_value . ",";
					$mockFile                = new stdClass();
					$mockFile->name          = $metadata['filename'];
					$mockFile->url           = esc_url( $url );
					$mockFile->attachment_id = $id_value;
					$mockFile->size          = $metadata['filesizeInBytes'];
					$entries[ $id_value ]    = $mockFile;
				}

			}
		}
		if ( count( $entries ) > 0 ) {
			$entries_result = esc_attr( json_encode( $entries ) );
		}
		$message = "Drop files here to upload";
		if ( ! empty( $result ) ) {
			$result_value = rtrim( trim( $result ), ',' );
		}
		$required                          = $this->getAttribute( 'mandatory' );
        $ensure_amount                          = $this->getAttribute( 'ensure_amount' );
		$validation_error_message          = $this->getAttribute( 'validation_error_message' );
		$description                       = $this->getAttribute( 'description' );
		$max_size                          = $this->getAttribute( 'file_limit' );
		$accepted_files                    = $this->getAttribute( 'accepted_files' );
		$multiple_files                    = $this->getAttribute( 'multiple_files' );
		$multiple_files_validation_message = $this->getAttribute( 'multiple_files_validation_message' );
		$upload_error_validation_message   = $this->getAttribute( 'upload_error_validation_message' );
		$mime_type                         = '';
		$mime_type_result                  = '';
		$allowed_types                     = get_allowed_mime_types();
		foreach ( $accepted_files as $key => $value ) {
			$mime_type .= $allowed_types[ $value ] . ',';
		}
		if ( ! empty( $mime_type ) ) {
			$mime_type_result = rtrim( trim( $mime_type ), ',' );
		}

		if ( ! empty( $required ) ) {
			$required = 'data-rule-upload-required=\'true\' ';
			if ( ! empty( $validation_error_message ) ) {
				$required .= ' data-msg-upload-required=\'' . $validation_error_message . '\'';
			}
		}

        if ( ! empty( $ensure_amount ) ) {
            $ensure_amount = "data-rule-upload-ensure-amount ='$multiple_files'";
        }

		//$box = str_replace( "class=\"form-control\"", "class=\"dropzone\"", $box );
		$box = sprintf( '<div class="dropzone upload_field dz-clickable" id="%s" file_limit="%s" accepted_files="%s" multiple_files="%s" action="%s" data-entry="%s" page="%s">', $id, $max_size, $mime_type_result, $multiple_files, $action, $entries_result, $page );
		$box .= sprintf( '<div class="dz-default dz-message" data-dz-message=""><span>%s</span></div>', $message );
		$box .= sprintf( '<input type="text" style="visibility: hidden" class="upload_field_input" name="%s" value="%s" id="field_%s" data-rule-upload-max-exceeded="[%s]" multiple_files_validation_message = "%s"  data-rule-upload-group="true" data-rule-upload-error="true" upload_error_validation_message="%s" %s %s />', $id, $result_value, $id, $multiple_files, $multiple_files_validation_message, $upload_error_validation_message, $required,$ensure_amount );
		$box .= '</div>';
		if ( ! empty( $description ) ) {
			$box .= sprintf( '<span class="help-inline">%s</span>', $description );
		}
		if ( $this->bootstrapVersion == 3 ) {
			echo $box;
		} else {
			echo preg_replace( "/(.*)(<input .*\/>)(.*)/i",
				'${1}<label class="file">${2}<span class="file-custom"></span></label>${3}', $box );
		}
	}
}

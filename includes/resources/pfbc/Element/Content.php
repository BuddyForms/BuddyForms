<?php

/**
 * Class Element_Content
 */
class Element_Content extends Element {

	/**
	 * @var array
	 */
	protected $_attributes = array( "type" => "content" );

	/**
	 * @var string
	 */
	protected $message = "Error: %element% is a required field.";
	private $pattern = '/<textarea(.*?)+>((.*?)+)<\/textarea>/';

	/**
	 * Element_Content constructor.
	 *
	 * @param $value
	 */
	public function __construct( $label, $name, $value ) {
		global $field_id;

		$properties = array(
			"value"    => $value,
			"field_id" => $field_id
		);
		parent::__construct( $label, $name, $properties );
	}

	public function isValid( $value ) {
		$validation = new Validation_Required($this->message, $this->field_options);

		$value = $this->getAttribute('value');
		preg_match_all( $this->pattern, $value, $matches );

		$result = $validation->isNotApplicable( $value ) || !empty($matches[2][0]) ;

        if (!$result) {
            $this->_errors[] = str_replace( "%element%", $this->getLabel(), $validation->getMessage() );
        }

		return apply_filters( 'buddyforms_element_content_validation', $result, $this );
	}




	public function render() {
		echo $this->_attributes["value"];
	}
}

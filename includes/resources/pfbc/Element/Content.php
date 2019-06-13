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
	public function __construct( $value ) {
		global $field_id;

		$properties = array(
			"value"    => $value,
			"field_id" => $field_id
		);
		parent::__construct( "", "", $properties );
	}

	public function isValid( $value ) {
		$this->_errors[] = 'EEEE';
		$validation = new Validation_Required($this->message, $this->field_options);

		$value = $this->getAttribute('value');
		preg_match_all( $this->pattern, $value, $matches );

		$result = $validation->isNotApplicable( $value ) || !empty($matches[0][2]) ;

		return apply_filters( 'buddyforms_element_content_validation', $result, $this );
	}




	public function render() {
		echo $this->_attributes["value"];
	}
}

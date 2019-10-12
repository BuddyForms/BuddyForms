<?php

/**
 * Class Element
 */
abstract class Element extends Base {
	/**
	 * @var array
	 */
	protected $_errors = array();
	/**
	 * @var array
	 */
	protected $_attributes = array();
	/**
	 * @var Form
	 */
	protected $_form;

	/**
	 * @var
	 */
	protected $label;
	/**
	 * @var
	 */
	protected $shortDesc;
	/**
	 * @var
	 */
	protected $longDesc;
	/**
	 * @var bool
	 */
	protected $shared = false;
	/**
	 * @var array
	 */
	protected $validation = array();

	/**
	 * @var array
	 */
	protected $field_options = array();

	/**
	 * Element constructor.
	 *
	 * @param $label
	 * @param $name
	 * @param array|null $properties
	 * @param array|null $field_options
	 */
	public function __construct( $label, $name, array $properties = null, array $field_options = null ) {
		$configuration = array(
			"label" => $label,
			"name"  => $name
		);

		/*Merge any properties provided with an associative array containing the label
		and name properties.*/
		if ( is_array( $properties ) ) {
			$configuration = array_merge( $configuration, $properties );
		}

		if ( ! empty( $field_options ) ) {
			$this->field_options = $field_options;
		}

		$this->setRequired( ! empty( $configuration['required'] ) );

		$this->configure( $configuration );
	}

	/*When an element is serialized and stored in the session, this method prevents any non-essential
	information from being included.*/
	/**
	 * @return array
	 */
	public function __sleep() {
		return array( "_attributes", "label", "validation" );
	}

	/*If an element requires external stylesheets, this method is used to return an
	array of entries that will be applied before the form is rendered.*/
	public function getCSSFiles() {
	}

	/**
	 * @return array
	 */
	public function getErrors() {
		return $this->_errors;
	}

	/*If an element requires external javascript file, this method is used to return an
	array of entries that will be applied after the form is rendered.*/
	public function getJSFiles() {
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->_attributes['name'];
	}

	/**
	 * @param mixed $name
	 */
	public function setName( $name ) {
		$this->_attributes['name'] = $name;
	}

	/**
	 * @return mixed
	 */
	public function getLabel() {
		return ( ! empty( $this->label ) ) ? $this->label : '';
	}

	/**
	 * @param $label
	 */
	public function setLabel( $label ) {
		$this->label = $label;
	}

	/**
	 * @return mixed
	 */
	public function getLongDesc() {
		return $this->longDesc;
	}

	/*This method provides a shortcut for checking if an element is required.*/

	/**
	 * @return bool
	 */
	public function getShared() {
		return $this->shared;
	}

	/**
	 * @return bool
	 */
	public function isRequired() {
		if ( ! empty( $this->validation ) ) {
			foreach ( $this->validation as $validation ) {
				if ( $validation instanceof Validation_Required ) {
					return true;
				}
			}
		}

		return false;
	}

	/*The isValid method ensures that the provided value satisfies each of the
	element's validation rules.*/

	/**
	 * @return mixed
	 */
	public function getShortDesc() {
		return $this->shortDesc;
	}

	/*If an element requires jQuery, this method is used to include a section of javascript
	that will be applied within the jQuery(document).ready(function() {}); section after the
	form has been rendered.*/

	/**
	 * @param $value
	 * @param $field
	 *
	 * @return bool
	 * @since 2.4.6 added the $element parameter
	 *
	 */
	public function isValid( $value ) {
		$valid = true;
		if ( ! empty( $this->validation ) ) {
			if ( ! empty( $this->label ) ) {
				$element = $this->label;
			} elseif ( ! empty( $this->_attributes["placeholder"] ) ) {
				$element = $this->_attributes["placeholder"];
			} else {
				$element = $this->_attributes["name"];
			}

			if ( substr( $element, - 1 ) == ":" ) {
				$element = substr( $element, 0, - 1 );
			}

			if ( strlen( $value > 0 ) ) {
				if ( ! empty( $this->_attributes['minlength'] ) && strlen( $value ) < $this->_attributes['minlength'] ) {
					$this->_errors[] = $element . " should be at least {$this->_attributes['minlength']} characters";
					$valid           = false;
				}
				if ( ! empty( $this->_attributes['maxlength'] ) && strlen( $value ) > $this->_attributes['maxlength'] ) {
					$this->_errors[] = $element . " should be not more then {$this->_attributes['maxlength']} characters";
					$valid           = false;
				}
			}

			foreach ( $this->validation as $validation ) {
				if ( ! $validation->isValid( $value, $this ) ) {
					/*In the error message, %element% will be replaced by the element's label (or
					name if label is not provided).*/
					$this->_errors[] = str_replace( "%element%", $element, $validation->getMessage() );
					$valid           = false;
				}
			}
		}

		return $valid;
	}

	/*Elements that have the jQueryOptions property included (Date, Sort, Checksort, and Color)
	can make use of this method to render out the element's appropriate jQuery options.*/

	public function jQueryDocumentReady() {
	}

	/*Many of the included elements make use of the <input> tag for display.  These include the Hidden, Textbox,
	Password, Date, Color, Button, Email, and File element classes.  The project's other element classes will
	override this method with their own implementation.*/

	public function jQueryOptions() {
		if ( ! empty( $this->jQueryOptions ) ) {
			$options = "";
			foreach ( $this->jQueryOptions as $option => $value ) {
				if ( ! empty( $options ) ) {
					$options .= ", ";
				}
				$options .= $option . ': ';
				/*When javascript needs to be applied as a jQuery option's value, no quotes are needed.*/
				if ( is_string( $value ) && substr( $value, 0, 3 ) == "js:" ) {
					$options .= substr( $value, 3 );
				} else {
					$options .= var_export( $value, true );
				}
			}
			echo "{ ", $options, " }";
		}
	}

	/*If an element requires inline stylesheet definitions, this method is used send them to the browser before
	the form is rendered.*/

	public function render() {
		echo '<input', $this->getAttributes(), '/>';
	}

	/*If an element requires javascript to be loaded, this method is used send them to the browser after
	the form is rendered.*/

	public function renderCSS() {
	}

	public function renderJS() {
	}

	/**
	 * @param Form $form
	 */
	public function _setForm( Form $form ) {
		$this->_form = $form;
	}

	/**
	 * This method provides a shortcut for applying the Required validation class to an element.
	 *
	 * @param $required
	 */
	public function setRequired( $required ) {
		if ( ! empty( $required ) ) {
			$this->validation[] = new Validation_Required;
			$this->_attributes["required"] = "";
		}

	}

	/**
	 * This method applies one or more validation rules to an element.  If can accept a single concrete validation class or an array of entries.
	 *
	 * @param $validation
	 */
	public function setValidation( $validation ) {
		/*If a single validation class is provided, an array is created in order to reuse the same logic.*/
		if ( ! is_array( $validation ) ) {
			$validation = array( $validation );
		}
		foreach ( $validation as $object ) {
			/*Ensures $object contains a existing concrete validation class.*/
			if ( $object instanceof Validation ) {
				$this->validation[] = $object;
				if ( $object instanceof Validation_Required ) {
					$this->_attributes["required"] = "";
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function getFieldOptions() {
		return $this->field_options;
	}

	/**
	 * @param $attribute
	 *
	 * @return string
	 */
	public function getOption( $attribute ) {
		if ( ! empty ( $this->field_options[ $attribute ] ) ) {
			return $this->field_options[ $attribute ];
		}

		return '';
	}

	/**
	 * @param array $field_options
	 */
	public function setFieldOptions( $field_options ) {
		$this->field_options = $field_options;
	}
}

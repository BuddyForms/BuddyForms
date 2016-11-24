<?php

/**
 * Class Base
 */
abstract class Base {
	/**
	 * @param array|null $properties
	 *
	 * @return $this
	 */
	public function configure( array $properties = null ) {
		if ( ! empty( $properties ) ) {
			$class = get_class( $this );

			/*The property_reference lookup array is created so that properties can be set
			case-insensitively.*/
			$available          = array_keys( get_class_vars( $class ) );
			$property_reference = array();
			foreach ( $available as $property ) {
				$property_reference[ strtolower( $property ) ] = $property;
			}

			/*The method reference lookup array is created so that "set" methods can be called
			case-insensitively.*/
			$available        = get_class_methods( $class );
			$method_reference = array();
			foreach ( $available as $method ) {
				$method_reference[ strtolower( $method ) ] = $method;
			}

			foreach ( $properties as $property => $value ) {
				$property = strtolower( $property );
				/*Properties beginning with "_" cannot be set directly.*/
				if ( $property[0] != "_" ) {
					/*If the appropriate class has a "set" method for the property provided, then
					it is called instead or setting the property directly.*/
					if ( isset ( $method_reference[ "set" . $property ] ) ) {
						$funcName = $method_reference[ "set" . $property ];
						$this->$funcName ( $value );
					} elseif ( isset( $property_reference[ $property ] ) ) {
						$funcName        = $property_reference[ $property ];
						$this->$funcName = $value;
					} /*Entries that don't match an available class property are stored in the attributes
                    property if applicable.  Typically, these entries will be element attributes such as
                    class, value, onkeyup, etc.*/
					else {
						$this->setAttribute( $property, $value );
					}
				}
			}
		}

		return $this;
	}

	/*This method can be used to view a class' state.*/

	/**
	 * @param $attribute
	 * @param $value
	 */
	public function setAttribute( $attribute, $value ) {
		if ( isset ( $this->_attributes ) ) {
			$this->_attributes[ $attribute ] = $value;
		}
	}

	/*This method prevents double/single quotes in html attributes from breaking the markup.*/

	public function debug() {
		echo "<pre>", print_r( $this, true ), "</pre>";
	}

	/**
	 * @param $attribute
	 *
	 * @return string
	 */
	public function getAttribute( $attribute ) {
		if ( isset ( $this->_attributes[ $attribute ] ) ) {
			return $this->_attributes[ $attribute ];
		}

		return "";
	}

	/**
	 * Method is used by the Form class and all Element classes to return a string of html  attributes
	 *
	 * @param Parameter|string $ignore Parameter allows special attributes from being included.
	 *
	 * @return string
	 */
	public function getAttributes( $ignore = "" ) {
		$str = "";
		if ( ! empty( $this->_attributes ) ) {
			if ( ! is_array( $ignore ) ) {
				$ignore = array( $ignore );
			}
			$attributes = array_diff( array_keys( $this->_attributes ), $ignore );
			foreach ( $attributes as $attribute ) {
				$str .= ' ' . $attribute;
				if ( $this->_attributes[ $attribute ] !== "" ) {
					$str .= '="' . $this->filter( $this->_attributes[ $attribute ] ) . '"';
				}
			}
		}

		return $str;
	}

	/**
	 * @param $str
	 *
	 * @return string
	 */
	protected function filter( $str ) {
		return htmlspecialchars( $str );
	}

	/**
	 * @param $attribute
	 * @param $value
	 */
	public function appendAttribute( $attribute, $value ) {
		if ( isset ( $this->_attributes ) ) {
			if ( ! empty ( $this->_attributes[ $attribute ] ) ) {
				$this->_attributes[ $attribute ] .= " " . $value;
			} else {
				$this->_attributes[ $attribute ] = $value;
			}
		}
	}
}
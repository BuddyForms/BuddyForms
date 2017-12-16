<?php
/*
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
Copyright (c) 2015 Smart Systems
Copyright (c) 2009-2015 Andrew Porterfield

Version: 4.0
*/

/**
 * @param $class
 */
function PFBC_Load( $class ) {
	$file = dirname( __FILE__ ) . "/" . str_replace( "_", DIRECTORY_SEPARATOR, $class ) . ".php";
	if ( is_file( $file ) ) {
		include_once $file;
	}
}

spl_autoload_register( "PFBC_Load" );

/**
 * Class Form
 */
class Form extends Base {
	/**
	 * @var int
	 */
	public static $SUBMIT = 99;
	/**
	 * @var null
	 */
	protected static $form = null;
	/**
	 * @var array
	 */
	protected $_elements = array();
	/**
	 * @var string
	 */
	protected $_prefix = "http";
	/**
	 * @var array
	 */
	protected $_values = array();
	/**
	 * @var array
	 */
	protected $_attributes = array();
	/**
	 * @var
	 */
	protected $ajax;
	/**
	 * @var
	 */
	protected $ajaxCallback;
	/**
	 * @var ErrorView_Standard
	 */
	protected $errorView;
	/**
	 * @var bool
	 */
	protected $noLabel = false;
	/*Prevents various automated from being automatically applied.  Current options for this array
	included jQuery, bootstrap and focus.*/
	/**
	 * @var string
	 */
	protected $resourcesPath;
	/**
	 * @var array
	 */
	protected $prevent = array();
	/**
	 * @var View_SideBySide
	 */
	protected $view;

	/**
	 * Form constructor.
	 *
	 * @param string $id
	 */
	public function __construct( $id = "pfbc" ) {

		$this->configure( array(
			"action" => $_SERVER['REQUEST_URI'],
			"id"     => preg_replace( "/\W/", "-", $id ),
			"method" => "post"
		) );

		if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) {
			$this->_prefix = "https";
		}

		/*The Standard view class is applied by default and will be used unless a different view is
		specified in the form's configure method*/
		if ( empty( $this->view ) ) {
			$this->view = new View_SideBySide;
		}

		if ( empty( $this->errorView ) ) {
			$this->errorView = new ErrorView_Standard;
		}

		/*The resourcesPath property is used to identify where third-party resources needed by the
		project are located.  This property will automatically be set properly if the PFBC directory
		is uploaded within the server's document root.  If symbolic links are used to reference the PFBC
		directory, you may need to set this property in the form's configure method or directly in this
		constructor.*/
		$path = dirname( __FILE__ ) . "/Resources";
		if ( strpos( $path, $_SERVER["DOCUMENT_ROOT"] ) !== false ) {
			$this->resourcesPath = substr( $path, strlen( $_SERVER["DOCUMENT_ROOT"] ) );
		} else {
			$this->resourcesPath = "/PFBC/Resources";
		}
	}

	/*When a form is serialized and stored in the session, this function prevents any non-essential
	information from being included.*/

	/**
	 * @param string $id
	 * @param bool $clearValues
	 *
	 * @return bool
	 */
	public static function isValid( $id, $clearValues = true ) {
		$valid = true;
		/*The form's instance is recovered (unserialized) from the session.*/
		$form = self::recover( $id );
		if ( ! empty( $form ) ) {
			if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
				$data = $_POST;
			} else {
				$data = $_GET;
			}

			/*Any values/errors stored in the session for this form are cleared.*/
			self::clearValues( $id );
			self::clearErrors( $id );

			/*Each element's value is saved in the session and checked against any validation rules applied
			to the element.*/
			if ( ! empty( $form->_elements ) ) {
				foreach ( $form->_elements as $element ) {
					$name = $element->getAttribute( "name" );
					if ( substr( $name, - 2 ) == "[]" ) {
						$name = substr( $name, 0, - 2 );
					}

					/*The File element must be handled differently b/c it uses the $_FILES superglobal and
					not $_GET or $_POST.*/
					if ( $element instanceof Element_File ) {
						$data[ $name ] = $_FILES[ $name ]["name"];
					}

					if ( isset( $data[ $name ] ) ) {
						$value = $data[ $name ];
						if ( is_array( $value ) ) {
							$valueSize = sizeof( $value );
							for ( $v = 0; $v < $valueSize; ++ $v ) {
								$value[ $v ] = stripslashes( $value[ $v ] );
							}
						} else {
							$value = stripslashes( $value );
						}
						self::_setSessionValue( $id, $name, $value );
					} else {
						$value = null;
					}

					/*If a validation error is found, the error message is saved in the session along with
					the element's name.*/
					if ( is_array( $value ) ) {
						foreach ( $value as $v ) {
							if ( ! $element->isValid( $v ) ) {
								self::setError( $id, $element->getErrors(), $name );
								$valid = false;
							}
						}
					} else {
						if ( ! $element->isValid( $value ) ) {
							self::setError( $id, $element->getErrors(), $name );
							$valid = false;
						}
					}
				}
			}

			/*If no validation errors were found, the form's session values are cleared.*/
			if ( $valid ) {
				if ( $clearValues ) {
					self::clearValues( $id );
				}
				self::clearErrors( $id );
			}
		} else {
			$valid = false;
		}

		return $valid;
	}

	/**
	 * @param $id
	 *
	 * @return mixed|string
	 */
	protected static function recover( $id ) {
		$wp_session = WP_Session::get_instance();
		if ( isset( $wp_session[ $id . '_form' ] ) ) {
			$json = maybe_unserialize( $wp_session[ $id . '_form' ] );

			return $json;
		} else {
			return "";
		}
	}

	/*Values that have been set through the setValues method, either manually by the developer
	or after validation errors, are applied to elements within this method.*/

	/**
	 * @param string $id
	 */
	public static function clearValues( $id ) {
		$wp_session = WP_Session::get_instance();
		if ( ! empty( $wp_session[ $id . "_values" ] ) ) {
			unset( $wp_session[ $id . "_values" ] );
		}
	}

	/**
	 * @param string $id
	 */
	public static function clearErrors( $id ) {
		$wp_session = WP_Session::get_instance();
		if ( isset( $wp_session[ $id . "_errors" ] ) ) {
			unset( $wp_session[ $id . "_errors" ] );
		}
	}

	/**
	 * @param $id
	 * @param $element
	 * @param $value
	 */
	public static function _setSessionValue( $id, $element, $value ) {
		$wp_session                    = WP_Session::get_instance();
		$wp_session[ $id . "_values" ] = array( $element => $value );
	}

	/**
	 * @param $id
	 * @param $errors
	 * @param string $element
	 */
	public static function setError( $id, $errors, $element = "" ) {
		$wp_session = WP_Session::get_instance();

		if ( ! is_array( $errors ) ) {
			$errors_array   = array();
			$errors_array[] = $errors;
			$errors         = $errors_array;
		}

		$element_errors = json_decode( $wp_session[ $id . "_errors" ] );

		foreach ( $errors as $key => $error ) {
			$element_errors[] = $element . ' ' . $error;
		}

		$wp_session[ $id . "_errors" ] = json_encode( $element_errors );
	}

	/**
	 * @param string $id
	 */
	public static function renderAjaxErrorResponse( $id ) {
		$form = self::recover( $id );
		if ( ! empty( $form ) ) {
			$form->errorView->renderAjaxErrorResponse();
		}
	}

	/**
	 * @param $formId
	 * @param $items
	 * @param $values
	 * @param int $buttons
	 */
	public static function renderArray( $formId, $items, $values, $buttons = 1 ) {
		$form = new Form( $formId );
		$opts = Array();

		if ( empty ( $items['ajax'] ) ) {
			$items["ajax"] = Array( "Hidden", "", "", Array( "value" => "false" ) );
		} else {
			$opts['ajax']         = true;
			$opts['ajaxCallback'] = $items['ajax'];
			unset ( $items['ajax'] );
		}
		if ( $buttons ) {
			$items["noneSubmitButton"] = Array( "Button", "Submit" );
			if ( $buttons != Form::$SUBMIT ) {
				if ( ! empty ( $values['id'] ) ) {
					if ( is_array( $buttons ) ) {
						foreach ( $buttons as $k => $b ) {
							$items[ $k ] = $b;
						}
						$items['noneRemoveButton'] = Array(
							"Button",
							"Remove",
							"button",
							array( "class" => "btn-danger", "data-toggle" => "modal", "data-target" => "#rmConfirm" )
						);
					}
				}
				if ( ! empty ( $items['ajax'] ) ) {
					$items["noneCancelButton"] = Array(
						"Button",
						"Cancel",
						"button",
						array( "onclick" => "history.go(-1);" )
					);
				}
			}
		}
		$form->configure( $opts );
		$form->addElements( $items );
		if ( ! empty ( $values ) ) {
			$form->setValues( $values );
		}
		$form->render();
	}

	/**
	 * @param $items
	 */
	public function addElements( $items ) {
		foreach ( $items as $id => $props ) {
			$elementClassName = "Element_" . $props[0];
			for ( $i = 1; $i <= 4; $i ++ ) {
				if ( ! isset ( $props[ $i ] ) ) {
					$props[ $i ] = null;
				}
			}
			$element = new $elementClassName ( $props[1], $props[2], $props[3], $props[4] );
			if ( ! preg_match( "/^none/i", $id ) ) {
				$element->setAttribute( 'name', $id );
			}
			$this->AddElement( $element );
		}
	}

	/**
	 * @param Element $element
	 */
	public function addElement( Element $element ) {
		$element->_setForm( $this );

		//If the element doesn't have a specified id, a generic identifier is applied.
		$id   = $element->getAttribute( "id" );
		$name = $element->getAttribute( "name" );
		if ( empty ( $id ) && $name ) {
			$element->setAttribute( "id", $name );
		} else if ( empty ( $id ) ) {
			$element->setAttribute( "id", $this->_attributes["id"] . "-element-" . sizeof( $this->_elements ) );
		}
		$this->_elements[] = $element;

		/*For ease-of-use, the form tag's encytype attribute is automatically set if the File element
		class is added.*/
		if ( $element instanceof Element_File ) {
			$this->_attributes["enctype"] = "multipart/form-data";
		}
	}

	/**
	 * @param array $values
	 */
	public function setValues( array $values ) {
		$this->_values = array_merge( $this->_values, $values );
	}

	/**
	 * @param null $element
	 * @param bool $returnHTML
	 *
	 * @return string
	 */
	public function render( $element = null, $returnHTML = false ) {
		$this->view->_setForm( $this );
		$this->errorView->_setForm( $this );

		/*When validation errors occur, the form's submitted values are saved in a session
		array, which allows them to be pre-populated when the user is redirected to the form.*/
		$values = self::getSessionValues( $this->_attributes["id"] );
		if ( ! empty( $values ) ) {
			$this->setValues( $values );
		}
		$this->applyValues();

		if ( $returnHTML ) {
			ob_start();
		}

		if ( ! $element ) {
			$this->renderCSS();
			$this->renderJS();
		}
		$this->view->noLabel = $this->noLabel;
		$this->view->render( $element );

		/*The form's instance is serialized and saved in a session variable for use during validation.*/
		$this->save();

		if ( $returnHTML ) {
			$html = ob_get_contents();
			ob_end_clean();

			return $html;
		}

		return false;
	}

	/**
	 * @param string $id
	 *
	 * @return array
	 */
	protected static function getSessionValues( $id ) {
		$wp_session = WP_Session::get_instance();
		$values     = array();
		if ( ! empty( $wp_session[ $id . "_values" ] ) ) {
			$values = (array) $wp_session[ $id . "_values" ];
		}

		return $values;
	}

	protected function applyValues() {
		foreach ( $this->_elements as $element ) {
			$name = $element->getAttribute( "name" );
			if ( isset( $this->_values[ $name ] ) ) {
				$element->setAttribute( "value", $this->_values[ $name ] );
			} elseif ( substr( $name, - 2 ) == "[]" && isset( $this->_values[ substr( $name, 0, - 2 ) ] ) ) {
				$element->setAttribute( "value", $this->_values[ substr( $name, 0, - 2 ) ] );
			}
		}
	}

	/*This method restores the serialized form instance.*/

	protected function renderCSS() {
		//$this->renderCSSFiles();

		echo '<style type="text/css">';
		$this->view->renderCSS();
		$this->errorView->renderCSS();
		foreach ( $this->_elements as $element ) {
			$element->renderCSS();
		}
		echo '</style>';
	}

	protected function renderCSSFiles() {
		$urls = array();
		foreach ( $this->_elements as $element ) {
			$elementUrls = $element->getCSSFiles();
			if ( is_array( $elementUrls ) ) {
				$urls = array_merge( $urls, $elementUrls );
			}
		}

		/*This section prevents duplicate css files from being loaded.*/
		if ( ! empty( $urls ) ) {
			$urls = array_values( array_unique( $urls ) );
			foreach ( $urls as $url ) {
				echo '<link type="text/css" rel="stylesheet" href="', $url, '"/>';
			}
		}
	}

	/*When ajax is used to submit the form's data, validation errors need to be manually sent back to the
	form using json.*/

	protected function renderJS() {
		$this->renderJSFiles();

		echo '<script type="text/javascript">';
		$this->view->renderJS();
		foreach ( $this->_elements as $element ) {
			$element->renderJS();
		}

		$id        = $this->_attributes["id"];
		$form_slug = str_replace( 'buddyforms_form_', '', $id );

		/*When the form is submitted, disable all submit buttons to prevent duplicate submissions.*/
		echo <<<JS
        jQuery(document).ready(function() {

			var bf_submit_type = "";
		    jQuery(document).on("click", '.bf-submit', function (evt) {
		        bf_submit_type = evt.target.name;
		    });

            jQuery("#$id").bind("submit", function() {
            	jQuery(this).find("#status").val(bf_submit_type);
                jQuery(this).find("input[type=submit]").attr("disabled", "disabled");
            });
JS;

		/*jQuery is used to set the focus of the form's initial element.*/
		if ( ! in_array( "focus", $this->prevent ) ) {
			echo 'jQuery("#', $id, ' :input:visible:enabled:first").focus();';
		}

		$this->view->jQueryDocumentReady();
		foreach ( $this->_elements as $element ) {
			$element->jQueryDocumentReady();
		}

		/*For ajax, an anonymous onsubmit javascript function is bound to the form using jQuery.  jQuery's
		serialize function is used to grab each element's name/value pair.*/
		if ( ! empty( $this->ajax ) ) {
			echo <<<JS
            jQuery("#$id").bind("submit", function() {
JS;

			/*Clear any existing validation errors.*/
			$this->errorView->clear();

			echo <<<JS

				if (!jQuery("#$id").valid()) {
			        return false;
			    }

				jQuery("#buddyforms_form_hero_$form_slug .form_wrapper form").LoadingOverlay("show", {
				fade  : [2000, 1000]
				});


                var FormData = jQuery("#$id").serialize();

                jQuery.ajax({
                    url: ajaxurl,
                    type: "{$this->_attributes["method"]}",
                    dataType: 'json',
                    data: {"action": "buddyforms_ajax_process_edit_post", "data": FormData},
                    error: function(xhr, status, error){
						  
                        
                        jQuery( '#form_message_$form_slug' ).addClass( 'bf-alert error' );
                        jQuery( '#form_message_$form_slug' ).html( xhr.responseText );
                                
						  
	                    console.log(xhr.responseText);
	                    
						jQuery("#$id").find("input[type=submit]").removeAttr("disabled");
                        bf_form_errors();
                        jQuery("#buddyforms_form_hero_$form_slug .form_wrapper form").LoadingOverlay("hide");
                        
                        
                    },
                    success: function(response) {

                    // console.log(response)

                    jQuery.each(response, function (i, val) {

                        switch (i) {
                            case 'form_notice':
                           		jQuery( '#form_message_$form_slug' ).addClass( 'bf-alert success' );
                                jQuery( '#form_message_$form_slug' ).html( val );
                                break;
                            case 'form_remove':

							    jQuery("#buddyforms_form_hero_$form_slug .form_wrapper").fadeOut("normal", function() {

							        jQuery( '#buddyforms_form_hero_$form_slug .form_wrapper' ).remove();
							    });
                                break;
                            case 'form_actions':
                                jQuery( '#buddyforms_form_$form_slug .form-actions' ).html(val);
                                break;
                            default:
                                jQuery( 'input[name="' + i + '"]').val(val);
                        }
                        jQuery('#recaptcha_reload').trigger('click');

                    });
					if(response != undefined && typeof response == "object" && response.errors) {
JS;

			$this->errorView->applyAjaxErrorResponse();

			echo <<<JS


	                } else {
JS;
			/*A callback function can be specified to handle any post submission events.*/
			if ( ! empty( $this->ajaxCallback ) ) {
				echo $this->ajaxCallback, "(response);";
			}
			/*After the form has finished submitting, re-enable all submit buttons to allow additional submissions.*/
			echo <<<JS
                        }
                        jQuery("#$id").find("input[type=submit]").removeAttr("disabled");
                        bf_form_errors();
                        jQuery("#buddyforms_form_hero_$form_slug .form_wrapper form").LoadingOverlay("hide");

                        // scrollto message after submit
						 jQuery('html, body').animate({
			            	 scrollTop: (jQuery("#buddyforms_form_hero_$form_slug"))
		                 }, 2000);

                    }
                });
                return false;
            });
JS;
		}

		echo '}); </script>';
	}

	protected function renderJSFiles() {
		$urls = array();
		foreach ( $this->_elements as $element ) {
			$elementUrls = $element->getJSFiles();
			if ( is_array( $elementUrls ) ) {
				$urls = array_merge( $urls, $elementUrls );
			}
		}

		/*This section prevents duplicate js files from being loaded.*/
		if ( ! empty( $urls ) ) {
			$urls = array_values( array_unique( $urls ) );
			foreach ( $urls as $url ) {
				echo '<script type="text/javascript" src="', $url, '"></script>';
			}
		}
	}

	protected function save() {
		$wp_session                                       = WP_Session::get_instance();
		$session_dada                                     = maybe_serialize( $this );
		$wp_session[ $this->_attributes["id"] . "_form" ] = $session_dada;
	}

	/**
	 * @param $formId
	 * @param null $values
	 * @param null $opts
	 *
	 * @return Form|null
	 */
	public static function open( $formId, $values = null, $opts = null ) {
		$default = Array();
		if ( $opts ) {
			foreach ( $opts as $key => $val ) {
				if ( $key == 'ajax' ) {
					$default['ajax']         = 1;
					$default['ajaxCallback'] = $opts['ajax'];
				} else if ( $key == 'view' ) {
					$viewName        = 'View_' . $val;
					$default[ $key ] = new $viewName;
				} else {
					$default[ $key ] = $val;
				}
			}
		}
		self::$form = new Form ( $formId );
		self::$form->configure( $default );
		if ( ! empty ( $values ) ) {
			self::$form->setValues( $values );
		}
		self::$form->render( 'open' );

		return self::$form;
	}

	/**
	 * @param $type
	 * @param $props
	 *
	 * @return mixed
	 */
	public static function __callStatic( $type, $props ) {
		if ( $type == 'close' ) {
			if ( ! isset ( $props[0] ) ) {
				$props[0] = 1;
			}

			return self::$form->_close( $props[0] );
		}

		return self::_call( self::$form, $type, $props );
	}

	/*The save method serialized the form's instance and saves it in the session.*/

	/**
	 * @param $form
	 * @param $type
	 * @param $props
	 *
	 * @return mixed
	 */
	private static function _call( $form, $type, $props ) {
		$elementClassName = "Element_$type";
		for ( $i = 0; $i <= 3; $i ++ ) {
			if ( ! isset ( $props[ $i ] ) ) {
				$props[ $i ] = null;
			}
		}

		$element = new $elementClassName ( $props[0], $props[1], $props[2], $props[3] );
		$form->AddElement( $element );
		$form->applyValues();
		$form->view->renderElement( $element );

		return $form;
	}

	/*Valldation errors are saved in the session after the form submission, and will be displayed to the user
	when redirected back to the form.*/

	/**
	 * @return array
	 */
	public function __sleep() {
		return array( "_attributes", "_elements", "errorView" );
	}

	/**
	 * @return mixed
	 */
	public function getAjax() {
		return $this->ajax;
	}

	/*An associative array is used to pre-populate form elements.  The keys of this array correspond with
	the element names.*/

	/**
	 * @return array
	 */
	public function getElements() {
		return $this->_elements;
	}

	/**
	 * @return ErrorView_Standard
	 */
	public function getErrorView() {
		return $this->errorView;
	}

	/**
	 * @return string
	 */
	public function getPrefix() {
		return $this->_prefix;
	}

	/**
	 * @return array
	 */
	public function getPrevent() {
		return $this->prevent;
	}

	/**
	 * @return string
	 */
	public function getResourcesPath() {
		return $this->resourcesPath;
	}

	/**
	 * @return array
	 */
	public function getErrors() {
		$wp_session = WP_Session::get_instance();

		$errors = array();
		$id     = $this->_attributes["id"];

		if ( isset( $wp_session[ $id . "_errors" ] ) ) {
			$errors = json_decode( $wp_session[ $id . "_errors" ] );
			if ( ! is_array( $errors ) ) {
				$errors[] = $errors;
			}
		}

		return $errors;
	}

	/**
	 * @param $type
	 * @param $props
	 *
	 * @return mixed|void
	 */
	public function __call( $type, $props ) {
		if ( $type == 'close' ) {
			return $this->_close( $props[0] );
		}

		return $this->_call( $this, $type, $props );
	}

	/**
	 * @param int $buttons
	 *
	 * @return bool|void
	 */
	public function _close( $buttons = 1 ) {
		$this->renderCSS();
		$this->renderJS();
		$this->save();
		if ( ! $buttons ) {
			return $this->view->renderFormClose();
		}
		echo '<div class="row"><div class="col-md-4"></div><div class="col-md-6">';
		$this->Button( "Submit" );
		if ( $buttons != Form::$SUBMIT ) {
			$this->Button( "Remove", "button", array(
				"class"       => "btn-danger",
				"data-toggle" => "modal",
				"data-target" => "#rmConfirm"
			) );
		}

		$this->Button( "Cancel", "button", array( "onclick" => "history.go(-1);" ) );
		echo '</div></div>';
		$this->view->renderFormClose();

		return true;
	}
}

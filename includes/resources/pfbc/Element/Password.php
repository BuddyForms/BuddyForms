<?php

/**
 * Class Element_Password
 */
class Element_Password extends Element_Textbox {
	/**
	 * @var array
	 */
	protected $_attributes = array( "type" => "password" );

	public function render() {
		?>
		<fieldset>
			<p>
				<label for="<?php echo $this->_attributes["id"]; ?>"><?php _e('New Password', 'buddyforms'); ?></label>
				<input name="<?php echo $this->_attributes["name"]; ?>" id="<?php echo $this->_attributes["id"]; ?>" class="required" type="password"/>
			</p>
			<p>
				<label for="<?php echo $this->_attributes["id"]; ?>"><?php _e('Password Confirm', 'buddyforms'); ?></label>
				<input name="<?php echo $this->_attributes["name"]; ?>_confirm" id="<?php echo $this->_attributes["id"]; ?>2" class="required" type="password"/>
			</p>
			<p><div><span id="password-strength"></span></div></p>
		</fieldset>
<?php


	}
}

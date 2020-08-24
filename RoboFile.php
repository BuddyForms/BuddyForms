<?php

include '.tk/RoboFileBase.php';

class RoboFile extends RoboFileBase {

	public function directoriesStructure() {
		return array( 'assets', 'includes', 'languages', 'templates', 'vendor-scope' );
	}

	public function fileStructure() {
		return array( 'BuddyForms.php', 'composer.json', 'license.txt', 'loco.xml', 'readme.txt', 'vendor-scope/buddyforms/composer.json' );
	}

	public function cleanDirectories() {
		return array( 'assets', 'includes/resources/freemius', 'vendor-scope' );
	}

	public function pluginMainFile() {
		return 'BuddyForms';
	}

	public function pluginFreemiusId() {
		return 391;
	}
}
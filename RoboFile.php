<?php

include '.tk/RoboFileBase.php';

class RoboFile extends RoboFileBase {

	public function directoriesStructure() {
		return array( 'assets', 'includes', 'languages', 'templates', 'vendor-scope' );
	}

	public function fileStructure() {
		return array( 'BuddyForms.php', 'composer.json', 'license.txt', 'loco.xml', 'readme.txt', 'vendor-scope/buddyforms/composer.json' );
	}

	/**
	 * @return array List of relative paths from the root folder of the plugin
	 */
	public function cleanPhpDirectories() {
		return array( 'assets', 'includes/resources/freemius', 'vendor-scope' );
	}

	public function pluginMainFile() {
		return 'BuddyForms';
	}

	public function pluginFreemiusId() {
		return 391;
	}

	public function minifyImagesDirectories() {
		return array();
	}

	public function minifyAssetsDirectories() {
		return array( 'assets' );
	}

	/**
	 * @return array Pair list of sass source directory and css target directory
	 */
	public function sassSourceTarget() {
		return array( array( 'scss/source' => 'assets/css' ) );
	}

	/**
	 * @return string Relative paths from the root folder of the plugin
	 */
	public function sassLibraryDirectory() {
		return 'scss/library';
	}
}

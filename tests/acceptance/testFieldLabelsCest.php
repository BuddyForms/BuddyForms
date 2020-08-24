<?php

class testFieldLabelsCest {
	// tests
	public function testFieldSlug( AcceptanceTester $I ) {
		$I->wantTo('Check the field have the correct slug');

		$I->loginAs( 'admin', 'admin' );
		$I->amOnAdminPage( '/' );
		$I->see( 'Dashboard' );

		$I->expect('the form exist');
		$I->goToBuddyFormsPage();
		$I->see( 'Test all fields' );

		$I->expect('the Field Subject not have slug');
		$I->click( 'a.bf_edit_field.row-title[href="#accordion_subject_89621a28ea"]' );
		$I->click( 'a[href="#advanced-subject-89621a28ea"]' );
		$I->canSee('Slug');
	}
}

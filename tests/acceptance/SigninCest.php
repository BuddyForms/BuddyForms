<?php

class SigninCest {
	// tests
	public function testSignInSuccessfully( AcceptanceTester $I ) {
		$I->loginAs( 'admin', 'admin' );
		$I->amOnAdminPage( '/' );
		$I->see( 'Dashboard' );
	}
}
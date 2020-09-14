<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;
    use \Codeception\Lib\Actor\Shared\Friend;

   /**
    * Define custom actions here
    */
   public function login() {
		$I = $this;
		$I->amOnPage( '/wp-login.php' );
		$I->fillField( '#user_login', 'admin' );
		$I->fillField( '#user_pass', 'admin' );
		$I->click( 'Log In' );
		$I->see( 'Dashboard' );
	}

	public function checkBuddyFormsIsActive() {
		$I = $this;
		$I->amGoingTo( 'Check if BuddyForms is enabled and have forms in the list' );
		$I->amOnPage( '/wp-admin/edit.php?post_type=buddyforms' );
		$I->see( 'BuddyForms' );
		$I->see( 'If you like BuddyForms please leave us' );
	}

	public function goToBuddyFormsPage( $page = 'forms' ) {
		$I = $this;
		$I->amGoingTo( 'go to a BuddyForms Page: ' . $page );
		switch ( $page ) {
			case 'forms':
				$I->amOnAdminPage( '/edit.php?post_type=buddyforms' );
				$I->see( 'BuddyForms' );
				$I->see( 'If you like BuddyForms please leave us' );
				break;
			case 'submissions':

				break;
		}
	}
}

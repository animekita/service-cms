<?php  defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Imported from trunk rev 431

/**
 * Modifications introduced by
 * @author Casper S. Jensen <sema@semaprojects.net>
 */

/**
 * Original file by, unmodified functions identified as such.
 * @package Users
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

Loader::library('sso_api');
Loader::model('userinfo');

class User extends Concrete5_Model_User {

		public $uID = '';
		public $uName = '';
		public $uGroups = array();
		public $superUser = false;
		public $uTimezone = NULL;
		protected $uDefaultLanguage = null;

		/**
		 * @param int $uID
		 * @param boolean $login
		 * @return User
		 */
		public static function getByUserID($uID, $login = false, $cacheItemsOnLogin = true) {
			$db = Loader::db();
			$v = array($uID);
			$q = "SELECT uID, uName, uIsActive, uLastOnline, uTimezone, uDefaultLanguage FROM Users WHERE uID = ?";
			$r = $db->query($q, $v);

			return User::getUser($r, $login, $cacheItemsOnLogin);
		}

		public static function getByUsername($uName, $login = false, $cacheItemsOnLogin = true) {
			$db = Loader::db();
			$v = array($uName);
		    $q = "SELECT uID, uName, uIsActive, uLastOnline, uTimezone, uDefaultLanguage FROM Users WHERE uName = ?";
			$r = $db->query($q, $v);

			return User::getUser($r, $login, $cacheItemsOnLogin);
		}

		public static function getUser($query_result, $login = false, $cacheItemsOnLogin = true) {
			if ($query_result) {
				$row = $query_result->fetchRow();
				$nu = new User();
				$nu->uID = $row['uID'];
				$nu->uName = $row['uName'];
				$nu->uIsActive = $row['uIsActive'];
				$nu->uDefaultLanguage = $row['uDefaultLanguage'];
				$nu->uLastLogin = $row['uLastLogin'];
		        $nu->uTimezone = $row['uTimezone'];
				$nu->uGroups = $nu->_getUserGroups(true);
				$nu->superUser = ($nu->getUserID() == USER_SUPER_ID);
				if ($login) {
					User::regenerateSession();
					$_SESSION['uID'] = $row['uID'];
					$_SESSION['uName'] = $row['uName'];
					$_SESSION['uBlockTypesSet'] = false;
					$_SESSION['uGroups'] = $nu->uGroups;
					$_SESSION['uLastOnline'] = $row['uLastOnline'];
		            $_SESSION['uTimezone'] = $row['uTimezone'];
					$_SESSION['uDefaultLanguage'] = $row['uDefaultLanguage'];
					if ($cacheItemsOnLogin) { 
						Loader::helper('concrete/interface')->cacheInterfaceItems();
					}
					$nu->recordLogin();
				}
			}
			return $nu;
		}

		protected static function regenerateSession() {
			unset($_SESSION['dashboardMenus']);
			session_regenerate_id(true);
		}

		// Unmodified function
		/**
		 * @param int $uID
		 * @return User
		 */
		public function loginByUserID($uID) {
			return User::getByUserID($uID, true);
		}

		public function loginByUsername($uName) {
			return User::getByUsername($uName, true);
		}

		protected static function createUserFromSession() {
			$si_sso = new SelvbetjeningIntegrationSSO();

			$user_info = $si_sso->get_session_info();
			$user_data = array('uName' => $user_info['username'],
							   'uEmail' => $user_info['email'],
							   'uPassword' => '');
			$ui = Userinfo::register($user_data);

			return $ui;
		}

		protected static function isLoggedInNative() {
			return $_SESSION['uID'] > 0 && $_SESSION['uName'] != '';
		}

	public static function isLoggedIn() {
		
		$si_sso = new SelvbetjeningIntegrationSSO();

		try {
			$authenticated = $si_sso->is_authenticated();
		} catch (Exception $e) {
			$authenticated = false;
		}

		if ($authenticated !== false) {
			if (!isset($_SESSION['uName_InitialUser'])) {
				$_SESSION['uName_InitialUser'] = $_SESSION['uName'];
			}

			if (User::isLoggedInNative()) {
				if ($authenticated == $_SESSION['uName_InitialUser']) {
					return true;
				} else {
					$u = new User();
					$u->logout();
					return false;
				}
			}

			$user = User::loginByUsername($authenticated);
			if (!$user->uID > 0 || $user->uName == '') {
				// user does not exist in the system, create him
				User::createUserFromSession();
				$user = User::loginByUserID($authenticated);
			}

			return User::isLoggedInNative();
		}

		if (User::isLoggedInNative()) {
			$u = new User();
			$u->logout();
		}

		return false;

	}

		public function __construct() {
			$args = func_get_args();
			$db = Loader::db();

			if (isset($args[1])) {
				// first, we check to see if the username and password match the admin username and password
				// $username = uName normally, but if not it's email address

				$si_sso = new SelvbetjeningIntegrationSSO();

				$username = $args[0];
				$password = $args[1];

				if (!$args[2]) {
					$_SESSION['uGroups'] = false;
				}

				try {
					$authenticated = $si_sso->authenticate($username, $password);
				} catch (AuthWrongCredentialsException $e) {
					$this->loadError(USER_INVALID);
				}
				catch (AuthUserInactiveException $e) {
					$this->loadError(USER_INACTIVE);
				}
				catch (Exception $e) {
					// would be better with a more explicit error message
					$this->loadError(USER_INVALID);
				}

				if (!$authenticated) {
					return $this;
				}

				$v = array($username);
				$q = "select uID, uName, uIsActive, uIsValidated, uTimezone, uDefaultLanguage from Users where uName = ?";

				$r = $db->query($q, $v);

				if (!$r) {
					// user does not exist in the system, create him
					User::createUserFromSession();
					$r = $db->query($q, $v);
				}

				if ($r) {
					$row = $r->fetchRow();
					if ($row['uID'] && $row['uIsValidated'] === '0' && defined('USER_VALIDATE_EMAIL_REQUIRED') && USER_VALIDATE_EMAIL_REQUIRED == TRUE) {
						$this->loadError(USER_NON_VALIDATED);
					} else if ($row['uID'] && $row['uIsActive']) {
						$this->uID = $row['uID'];
						$this->uName = $row['uName'];
						$this->uIsActive = $row['uIsActive'];
		                $this->uTimezone = $row['uTimezone'];
						$this->uDefaultLanguage = $row['uDefaultLanguage'];
						$this->uGroups = $this->_getUserGroups($args[2]);
						if ($row['uID'] == USER_SUPER_ID) {
							$this->superUser = true;
						} else {
							$this->superUser = false;
						}
						$this->recordLogin();
						if (!$args[2]) {
							User::regenerateSession();
							$_SESSION['uID'] = $row['uID'];
							$_SESSION['uName'] = $row['uName'];
							$_SESSION['superUser'] = $this->superUser;
							$_SESSION['uBlockTypesSet'] = false;
							$_SESSION['uGroups'] = $this->uGroups;
		                    $_SESSION['uTimezone'] = $this->uTimezone;
							$_SESSION['uDefaultLanguage'] = $this->uDefaultLanguage;
							Loader::helper('concrete/interface')->cacheInterfaceItems();
						}
					} else if ($row['uID'] && !$row['uIsActive']) {
						$this->loadError(USER_INACTIVE);
					} else {
						$this->loadError(USER_INVALID);
					}
					$r->free();
				} else {
					$this->loadError(USER_INVALID);
				}
			} else {
				// then we just get session info
				$this->uID = $_SESSION['uID'];
				$this->uName = $_SESSION['uName'];
				$this->uTimezone = $_SESSION['uTimezone'];
				if (isset($_SESSION['uDefaultLanguage'])) {
					$this->uDefaultLanguage = $_SESSION['uDefaultLanguage'];
				}
				$this->superUser = ($_SESSION['uID'] == USER_SUPER_ID) ? true : false;
				$this->uGroups = $this->_getUserGroups();
				if (!isset($args[2])) {
					$_SESSION['uGroups'] = $this->uGroups;
				}
			}

			return $this;
		}




}

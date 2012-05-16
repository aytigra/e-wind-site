<?php
//
// PowerAuth v1.0
// Robust password protection for your web site
//
// Created on 9/8/10
// Copyright Dane Iracleous
//

/***************************************************************************************************************/
///////////////////////////////////////// INSTRUCTIONS FOR USE //////////////////////////////////////////////////
//
//  1. Save this file, PowerAuth.php on your web server.
//
//  2. To password-protect a page, define the password and include this file like so:
//     <?php $PowerAuth_password = "MyPassword"; include "/path/to/PowerAuth.php"; ?/> (remove "/")
//
//  3. Other variables may be defined as well:
//
//     $PowerAuth_maxPasswordAttempts  : number of attempts to allow before locking user out
//     $PowerAuth_lockoutTime          : number of seconds to wait if locked out
//     $PowerAuth_lockoutTimeIncrement : number of seconds to increase lockout time in each subsequent lockout
//
//  4. To log a user out, call upon the page with a GET parameter, "logout", with any value
//
//  5. To apply CSS styles, modify the loginBox class and its children.
//     The default stylesheet is at the bottom of this file.
//
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/***************************************************************************************************************/

class CAuth
{
	protected $password;
	protected $maxTries;
	protected $lockTimeSeconds;
	protected $lockTimeIncrement;
	protected $signedOut;

	public function __construct($password, $maxTries, $lockTimeSeconds, $lockTimeIncrement)
	{
		$this->password = $password;
		$this->maxTries = $maxTries;
		$this->lockTimeSeconds = $lockTimeSeconds;
		$this->lockTimeIncrement = $lockTimeIncrement;

		//Init the following variables if they do not yet have values

		if(!isset($_SESSION['tries'])) //number of password entry attempts accumulated by user
			$_SESSION['tries'] = 0;

		if(!isset($_SESSION['authorized'])) //whether or not user is authorized
			$_SESSION['authorized'] = false;

		if(!isset($_SESSION['locked'])) //whether or not user is locked out
			$_SESSION['locked'] = false;

		if(!isset($_SESSION['numberOfLocks'])) //number of lock outs accumulated by user
			$_SESSION['numberOfLocks'] = 0;
	}

	//checks password against user-entered password and authorize user if there is a match
	public function login($password, $remember)
	{
		if(!$this->is_locked() && $this->password == $password)
		{
			$this->authorize($remember); //password is correct and theere is no lock - grant access
			return true;
		}
		else
		{
			$_SESSION['tries']++;
			if($_SESSION['tries']==$this->maxTries) //the total tries by this user exceeds the max allowed
				$this->lock(); //lock this user out

			return false;
		}
	}

	//log user out - no questions asked
	public function logout()
	{
		$this->deauthorize();
		return true;
	}

	//determine whether or not user is authorized
	public function is_authorized()
	{
		return ($_SESSION['authorized'] || isset($_COOKIE["PowerAuth"]) && !isset($this->signedOut));
	}

	//get the number of password attempts by user so far
	public function get_tries()
	{
		return $_SESSION['tries'];
	}

	//lock out the user to prevent additional password attempts
	public function lock()
	{
		$_SESSION['lockedTime'] = time(); //save the exact time that the user was locked out so we can produce a countdown later
		$_SESSION['locked'] = true;
		$_SESSION['numberOfLocks']++; //increment the number of times the user has been locked out
	}

	//determine whether or not user is locked out
	public function is_locked()
	{
		if($_SESSION['locked'] == true)
		{
			if($this->getRemainingLockTime()==0)
				$this->reset_lock();
		}

		return $_SESSION['locked'];
	}

	//get the number of seconds left before resetting the lock out and allowing user to log in again
	public function getRemainingLockTime()
	{
		$secondsElapsed = time() - $_SESSION['lockedTime'];

		//the seconds left in the lock is the total lock time set minus the seconds elapsed plus the number of locks the user has accumulated times increment time
		$secondsLeft = ($this->lockTimeSeconds - $secondsElapsed) + (($_SESSION['numberOfLocks']-1) * $this->lockTimeIncrement);

		if($secondsLeft < 0) //if the seconds left is less than 0, set it to 0
			$secondsLeft = 0;

		return $secondsLeft;
	}

	//reset the lock out
	public function reset_lock()
	{
		$_SESSION['locked'] = false;
		$_SESSION['tries'] = 0;
	}

	//grant access to user
	private function authorize($remember)
	{
		$_SESSION['authorized'] = true;
		$_SESSION['tries'] = 0;
		$_SESSION['numberOfLocks'] = 0;
		$_SESSION['locked'] = false;

		if($remember)
			setcookie("PowerAuth"); //create a cookie to remember authorization
	}

	//revoke access
	private function deauthorize()
	{
		$_SESSION['authorized'] = false;
		setcookie("PowerAuth", "", time()-3600); //set cookie to a past time to make it expired
		$this->signedOut = true;
	}
}

//start the PHP session
session_start();

//check that there are enough parameters to create the authorization object
if(!isset($PowerAuth_password))
{
	echo "PowerAuth Error: You must declare a PHP variable '$PowerAuth_password' before the include statement.";
	exit();
}

//if max password attempts var is not defined, set its value to the default
if(!isset($PowerAuth_maxPasswordAttempts))
	$PowerAuth_maxPasswordAttempts = 6;

//if lockout time var is not defined, set its value to the default
if(!isset($PowerAuth_lockoutTime))
	$PowerAuth_lockoutTime = 30;

//if lockout time increment var is not defined, set its value to the default
if(!isset($PowerAuth_lockoutTimeIncrement))
	$PowerAuth_lockoutTimeIncrement = 15;

$auth = new CAuth($PowerAuth_password, $PowerAuth_maxPasswordAttempts, $PowerAuth_lockoutTime, $PowerAuth_lockoutTimeIncrement); //create the authorization object

if(isset($_GET['logout'])) //user is logging out (note: send a GET parameter "logout" to successfully log out the user)
{
	$logout = $auth->logout();
}
else if(isset($_POST['login'])) //user is logging in
{
	if(isset($_POST['rememberme']))
		$remember = true;
	else
		$remember = false;

	$login = $auth->login($_POST['password'], $remember);
}

if(!$auth->is_authorized()) //user is not authorized - display the login form or lockout text
{
	//////////////////////////////////////////////////////////
	//!!!!!!!!!!!!!! CSS Stylesheet Begin !!!!!!!!!!!!!!!!!!//
	//////////////////////////////////////////////////////////

	//exit PHP and enter CSS
	?>
	<style type="text/css">
	.loginBox
	{
		padding:20px;
		background-color:#EEEEEE;
		color:#000000;
		font-family:Arial, Helvetica, sans-serif;
		border-color:#999999;
		border-width:1px;
		border-style:solid;
		width:400px;
		margin-left:auto;
		margin-right:auto;
		text-align:center;
	}
	.loginBox .accessDenied
	{
		color:#FF0000;
	}
	.loginBox .attemptsDisplay
	{
		color:#999999;
	}
	.loginBox .lockoutMessage
	{

	}
	.loginBox .lockoutMessage #timer
	{
		color:#FF0000;
		font-weight:bold;
	}
	</style>
	<?php
	//re-enter PHP

	echo "<div class='loginBox'>";

	if(!$auth->is_locked() && isset($login) && $login==false) //user has entered an incorrect password
		echo "<span class='accessDenied'>Incorrect password. Access denied.</span><br/><br/>";

	if(!$auth->is_locked() && $auth->get_tries()>0) //show the number of attempts used
		echo "<span class='attemptsDisplay'>".$auth->get_tries()."/".$PowerAuth_maxPasswordAttempts." attempts used.</span><br/><br/>";
	else if(isset($_GET['logout']))
		echo "<span class='accessDenied'>You have been logged out.</span><br/><br/>";
	else if(!$auth->is_locked())
		echo "<span class='attemptsDisplay'>This page is protected.</span><br/><br/>";

	if($auth->is_locked()) //user has entered an incorrect password too many times and is locked out
	{
		echo "<span class='lockoutMessage'>You have been locked out for entering an incorrect password ".$PowerAuth_maxPasswordAttempts." times. You must wait <span id='timer'>".$auth->getRemainingLockTime()."</span> seconds before attempting to log in again.</span>";

		//exit PHP and enter Javascript for countdown feature
		?>
		<script type="text/javascript">
			function countdown()
			{
				var e = document.getElementById("timer");
				var time = parseInt(e.innerHTML);
				if(time==0)
					window.location = "<?php echo $_SERVER['PHP_SELF']; ?>";
				else
				{
					e.innerHTML = parseInt(time-1);
					setTimeout("countdown()", 1000);
				}
			}
			setTimeout("countdown()",1000);
		</script>
		<?php
		//re-enter PHP
	}
	else //show the login form
	{
		echo "<form action='".AD_LOGIN.'?'.$_SERVER['QUERY_STRING']."' method='post'>";
		echo "Password: <input type='password' name='password'/>";
		echo "<br/><br/>";
		echo "<input type='checkbox' name='rememberme'/> Stay Signed In";
		echo "<br/><br/>";
		echo "<input type='submit' name='login' value='Log In'/>";
		echo "</form>";
	}
	echo "</div>";
	exit();
}
?>

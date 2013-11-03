<?php
session_start();
ob_start();

//Root address for entire site
	$root = "http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/";

//Database connection
	//Create database connection.
	$connDBA = mysql_connect("localhost", "root", "Oliver99");
	
	//Select database to use
	$dbSelect = mysql_select_db("biomed-ed", $connDBA);
	

//Messages
	//Alerts
	function alert ($alertConent = NULL){
		echo "<p><div align=\"center\"><div align=\"center\" class=\"announcement\">$alertConent</div></div></p><br />";
	}

	//Response for errors
	function errorMessage($errorContent = NULL) {
		echo "<p><div align=\"center\"><div align=\"center\" class=\"error\">$errorContent</div></div></p><br />";
	}

	//Response for secuess
	function successMessage($successContent) {
		echo "<p><div align=\"center\"><div align=\"center\" class=\"success\">$successContent</div></div></p><br />";
	}
	
	//A div centrally located div
	function centerDiv($divContent) {
		echo "<p><div align=\"center\">" . $divContent . "</div></p><br />";
	}	

//Call site title
	function title($title) {
		global $connDBA;
		$strippedTitle = stripslashes($title);
		$siteNameGrabber = mysql_fetch_array(mysql_query("SELECT * FROM siteprofiles", $connDBA));
		$siteName = stripslashes($siteNameGrabber['siteName']);
		$value = "<title>{$siteName} | {$strippedTitle}</title>";
		echo $value;
	}
	
//Universal information
	//Include a stylesheet and basic javascripts
	function headers() {
		$requestURL = $_SERVER['REQUEST_URI'];
		if (strstr($requestURL, "enable_javascript.php")) {
		} else {
			echo "<noscript><meta http-equiv=\"refresh\" content=\"0; url=http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/enable_javascript.php\"></noscript>";
		}
		$requestURL = $_SERVER['REQUEST_URI'];
		if (strstr($requestURL, "enable_javascript.php")) {
			echo "<script type=\"text/javascript\">window.location = \"index.php\"</script>
";
		}
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "gateway.php?file=styles/common/universal.css\" /><link type=\"image/x-icon\" rel=\"shortcut icon\" href=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/images/icon.ico\" /><script src=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/javascripts/common/hoverEffect.js\" type=\"text/javascript\"></script>";
		
		global $connDBA;
		$siteStyleGrabber = mysql_fetch_array(mysql_query("SELECT * FROM siteprofiles", $connDBA));
		$siteStyle = $siteStyleGrabber['style'];
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/styles/themes/" . $siteStyle . "\" />";
	}
	
	//Include the body class
	function bodyClass() {
		echo " class=\"theme course-1 dir-ltr lang-en_utf8\"";
	}

	//Include a tooltip	
	function tooltip() {
		echo "<script src=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/javascripts/common/tooltip.js\" type=\"text/javascript\"></script>";
	}
	
	//Include user login status
	function loginStatus() {
		$authenticationURL = "http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/";
	
		if (isset ($_SESSION['MM_Username'])) {
			global $connDBA;
			$userName = $_SESSION['MM_Username'];
			$nameGrabber = mysql_query ("SELECT * FROM users WHERE userName = '{$userName}'", $connDBA);
			$name = mysql_fetch_array($nameGrabber);
			$firstName = $name['firstName'];
			$lastName = $name['lastName'];
			
			echo "You are logged in as " . $firstName . " " . $lastName . " <a href=\"" . $authenticationURL . "logout.php\">(Logout)</a>";
		} else {
			echo "You are not logged in. <a href=\"" . $authenticationURL . "login.php\">(Login)</a>";
		}
	}
	
	//Include the logo
	function logo() {
		global $connDBA;
		$imagePaddingGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);	
		$imagePaddingArray = mysql_fetch_array($imagePaddingGrabber);
		$imagePaddingTop = $imagePaddingArray['paddingTop'];
		$imagePaddingBottom = $imagePaddingArray['paddingBottom'];
		$imagePaddingLeft = $imagePaddingArray['paddingLeft'];
		$imagePaddingRight = $imagePaddingArray['paddingRight'];
		$imageWidth = $imagePaddingArray['width'];
		$imageHeight = $imagePaddingArray['height'];
	
	echo "<div style=\"padding-top:" . $imagePaddingTop . "px; padding-bottom:" . $imagePaddingBottom . "px; padding-left:" .  $imagePaddingLeft . "px; padding-right:" . $imagePaddingRight . "px;\">";
		if (isset ($_SESSION['MM_UserGroup'])) {
			switch($_SESSION['MM_UserGroup']) {
				case "Student": echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/student/index.php\">"; break;
				case "Instructor": echo "<a href=\"http://\"" . $_SERVER['HTTP_HOST'] . "/biomed-ed/instructor/index.php\">"; break;
				case "Organization Administrator": echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/administrator/index.php\">"; break;
				case "Site Administrator": echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/site_administrator/index.php\">"; break;
				case "Advertiser": echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/advertiser/index.php\">"; break;
			}
		} else {
			echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/index.php\">";
		}
		
	echo "<img src=\"" . "http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/images/banner.png\"";
	if ($imagePaddingArray['auto'] !== "on") {
			echo " width=\"" . $imageWidth . "\" height=\"" . $imageHeight . "\"";
		} 
		
	echo " alt=\"" . $imagePaddingArray['siteName'] . "\" onmouseover=\"MM_effectAppearFade(this, 1000, 80, 100, false)\"></a></div>";
	}

	//Include a navigation bar
	function navigation($URL) {
		$include = "http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/" . $URL;
		echo "<div id=\"navbar_bg\"><div class=\"navbar clearfix\"><div class=\"breadcrumb\">";
		require_once($include);
		echo "</div></div></div>";
	}
	
	//Include all top-page items
	function topPage($URL) {
		global $connDBA;
		$siteAssist = mysql_fetch_array(mysql_query("SELECT * FROM siteprofiles", $connDBA));
		
		if ($siteAssist['assist'] == "no") {
			echo "<div id=\"page\">
			<div id=\"header_bg\">
			<div id=\"header\" class=\"clearfix\"><h1 class=\"headermain\">";
			echo $siteAssist['siteName'];
			echo "</h1><div class=\"headermenu\"><div class=\"logininfo\">";
			loginStatus();
			echo "</div></div></div><div id=\"banner_bg\"><div id=\"banner\">";
			logo();
			echo "</div></div>";
			navigation($URL);
			echo "</div>";
			echo "<div id=\"content\"><div class=\"box generalbox generalboxcontent boxaligncenter boxwidthwide\">";
		} else {
			echo "<div id=\"page\">
			<div id=\"header_bg\">
			<div id=\"header\" class=\"clearfix\"><h1 class=\"headermain\">";
			logo();
			echo "</h1><div class=\"headermenu\"><div class=\"logininfo\">";
			loginStatus();
			echo"</div></div></div>";
			navigation($URL);
			echo "<div id=\"content\"><div class=\"box generalbox generalboxcontent boxaligncenter boxwidthwide\">";
		}		
	}
	
	//Include a footer
	function footer($URL) {
		echo "</div></div>";
		$include = "http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/" . $URL;
		$footer = "http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/includes/footer.php";
		echo "<div id=\"footer\"><div>&nbsp;</div><div class=\"breadcrumb\">";
		require_once ($include);
		echo "</div><div align=\"right\">";
		require_once ($footer);
		echo "</div></div></div>";
	}
	
//Login a user
	function login() {
		global $connDBA;
		global $root;

		if (!function_exists("GetSQLValueString")) {
		function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
		{
		  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
		
		  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
		
		  switch ($theType) {
			case "text":
			  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			  break;    
			case "long":
			case "int":
			  $theValue = ($theValue != "") ? intval($theValue) : "NULL";
			  break;
			case "double":
			  $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
			  break;
			case "date":
			  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			  break;
			case "defined":
			  $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			  break;
		  }
		  return $theValue;
		}
		}
		// *** Validate request to login to this site.
		if (!isset($_SESSION)) {
		  session_start();
		}
		
		$loginFormAction = $_SERVER['PHP_SELF'];
		if (isset($_GET['accesscheck'])) {
		  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
		}
		
		if (isset($_POST['username'])) {
		  $loginUsername=$_POST['username'];
		  $password=$_POST['password'];
		  $MM_fldUserAuthorization = "role";
		  $MM_redirectLoginSuccess = "index.php?switch";
		  $MM_redirectLoginFailed = "index.php?alert";
		  $MM_redirecttoReferrer = false;
		  mysql_select_db($database_connDBA, $connDBA);
			
		  $LoginRS__query=sprintf("SELECT userName, passWord, role FROM users WHERE userName=%s AND passWord=%s",
		  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
		   
		  $LoginRS = mysql_query($LoginRS__query, $connDBA) or die(mysql_error());
		  $loginFoundUser = mysql_num_rows($LoginRS);
		  if ($loginFoundUser) {
			
			$loginStrGroup  = mysql_result($LoginRS,0,'role');
			
			//declare two session variables and assign them
			$_SESSION['MM_Username'] = $loginUsername;
			$_SESSION['MM_UserGroup'] = $loginStrGroup;	      
		
			if (isset($_SESSION['PrevUrl']) && false) {
			  $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
			}
			header("Location: " . $MM_redirectLoginSuccess );
		  }
		  else {
			header("Location: ". $MM_redirectLoginFailed );
		  }
		}
	}
	
//Maintain login status
	function loginCheck($role) {
		$MM_authorizedUsers = $role;
		$MM_donotCheckaccess = "false";
		
		// *** Restrict Access To Page: Grant or deny access to this page
		function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
		  // For security, start by assuming the visitor is NOT authorized. 
		  $isValid = False; 
		
		  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
		  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
		  if (!empty($UserName)) { 
			// Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
			// Parse the strings into arrays. 
			$arrUsers = Explode(",", $strUsers); 
			$arrGroups = Explode(",", $strGroups); 
			if (in_array($UserName, $arrUsers)) { 
			  $isValid = true; 
			} 
			// Or, you may restrict access to only certain users based on their username. 
			if (in_array($UserGroup, $arrGroups)) { 
			  $isValid = true; 
			} 
			if (($strUsers == "") && false) { 
			  $isValid = true; 
			} 
		  } 
		  return $isValid; 
		}
		
		$MM_restrictGoTo = "http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/login.php";
		if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
		  $MM_qsChar = "?";
		  $MM_referrer = $_SERVER['PHP_SELF'];
		  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
		  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
		  $MM_referrer .= "?" . $QUERY_STRING;
		  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
		  header("Location: ". $MM_restrictGoTo); 
		  exit;
		}
	}
	
//Meta information
	function meta() {
		global $connDBA;
		$meta = mysql_fetch_array(mysql_query ("SELECT * FROM siteprofiles", $connDBA));
	
		echo "<meta name=\"author\" content=\"" . stripslashes($meta['author']) . "\" />
		<meta http-equiv=\"content-language\" content=\"" . stripslashes($meta['language']) . "\" />
		<meta name=\"copyright\" content=\"" . stripslashes($meta['copyright']) . "\" />
		<meta name=\"description\" content=\"" . stripslashes($meta['description']) . "\" />
		<meta name=\"keywords\" content=\"" . stripslashes($meta['meta']) . "\" />";

	}
	
//Include the tiny_mce simple widget
	function tinyMCESimple () {
		echo "<script type=\"text/javascript\" src=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/tiny_mce/tiny_mce.js\"></script><script type=\"text/javascript\" src=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/javascripts/common/tiny_mce_simple.js\"></script>";
	}
	
//Include the tiny_mce advanced widget
	function tinyMCEAdvanced () {
		echo "<script type=\"text/javascript\" src=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/tiny_mce/tiny_mce.js\"></script><script type=\"text/javascript\" src=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/javascripts/common/tiny_mce_advanced.js\"></script>";
	}
	
//Include a form validator
	function validate () {
		echo "<link rel=\"stylesheet\" href=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/styles/validation/validatorStyle.css\" type=\"text/css\">";
		echo "<link rel=\"stylesheet\" href=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/styles/validation/validateTextarea.css\" type=\"text/css\">";
		echo "<script src=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/javascripts/validation/validatorCore.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/javascripts/validation/validatorOptions.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/javascripts/validation/runValidator.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/javascripts/validation/validateTextarea.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/javascripts/validation/formErrors.js\" type=\"text/javascript\"></script>";
	}
	
//Insert a form errors box
	function formErrors () {
		echo "<div id=\"errorBox\" style=\"display:none;\">Some fields are incomplete, please scroll up to correct them.</div>";
	}
	
//Submit a form and toggle the tinyMCE to save its content
	function submit($id, $value) {
		echo "<input type=\"submit\" name=\"" . $id . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"tinyMCE.triggerSave();\" />";
	}
	
//If the user is editing the lesson, display a different series of numbering
	function step ($number, $text, $sessionNumber, $sessionText) {
		if (isset ($_SESSION['review'])) {
			echo "<img src=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/images/numbering/" .$sessionNumber . ".gif\" alt=\"" . $sessionNumber . ".\" width=\"22\" height=\"22\" /> " . $sessionText;
		} else {
			echo "<img src=\"http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/images/numbering/" .$number . ".gif\" alt=\"" . $number . ".\" width=\"22\" height=\"22\" /> " . $text;
		}
	}
	
//Generate a random string
	function randomValue($length = 8, $seeds = 'alphanum') {
		$seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
		$seedings['numeric'] = '0123456789';
		$seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
		$seedings['hexidec'] = '0123456789abcdef';
		
		if (isset($seedings[$seeds])) {
			$seeds = $seedings[$seeds];
		}
		
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);
		
		$string = '';
		$seeds_count = strlen($seeds);
		
		for ($i = 0; $length > $i; $i++) {
			$string .= $seeds{mt_rand(0, $seeds_count - 1)};
		}
		
		return $string;
	}
	
//A function to limit the length of the directions
	function commentTrim ($length, $value) {
	   $stripValue = array("<p>", "</p>", "<h1>", "<h2>", "<h3>", "<h4>", "<h5>", "<h6>", "</h1>", "</h2>", "</h3>", "</h4>", "</h5>", "</h6>", "<br />");
	   $comments = str_replace($stripValue, " ", $value);
	   $maxLength = $length;
	   $countValue = html_entity_decode($comments);
	   if (strlen($countValue) <= $maxLength) {
		  return stripslashes($comments);
	   }
	
	   $shortenedValue = substr($countValue, 0, $maxLength - 3) . "...";
	   return $shortenedValue;
	}
?>
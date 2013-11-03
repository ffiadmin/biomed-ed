<?php
session_start();
ob_start();

/* Begin core functions */
	//Root address for entire site
	$root = "http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/";
	$strippedRoot = str_replace("http://" . $_SERVER['HTTP_HOST'], "", $root);

	//Database connection
	$connDBA = mysql_connect("localhost", "root", "Oliver99");
	$dbSelect = mysql_select_db("biomed-ed", $connDBA);
	
	//Define time zone
	$timeZoneGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
	$timeZone = mysql_fetch_array($timeZoneGrabber);
	date_default_timezone_set($timeZone['timeZone']);
/* End core functions */	

/* Begin messages functions */
	//Alerts
	function alert($errorContent = NULL) {
		echo "<p><div align=\"center\"><div align=\"center\" class=\"alert\">$errorContent</div></div></p><br />";
	}
	
	//Response for errors
	function errorMessage($errorContent = NULL) {
		echo "<p><div align=\"center\"><div align=\"center\" class=\"error\">$errorContent</div></div></p><br />";
	}

	//Response for secuess
	function successMessage($successContent) {
		echo "<p><div align=\"center\"><div align=\"center\" class=\"success\">$successContent</div></div></p><br />";
	}
	
	//A centrally located div
	function centerDiv($divContent) {
		echo "<p><div align=\"center\">" . $divContent . "</div></p><br />";
	}
/* End messages functions */

/* Begin site layout functions */		
	//Include the start of a page
	function headers($title, $role = false, $functions = false, $toolTip = false, $additionalParameters = false, $publicNavigation = false, $meta = false, $description = false, $additionalKeywords = false) {
		global $connDBA;
		global $root;
		
	//Maintain login status
		if ($role == true) {
			$MM_authorizedUsers = $role;
			$MM_donotCheckaccess = "false";
			
			function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
			  $isValid = False; 
			  
			  if (!empty($UserName)) {
				$arrUsers = Explode(",", $strUsers); 
				$arrGroups = Explode(",", $strGroups); 
				if (in_array($UserName, $arrUsers)) { 
				  $isValid = true; 
				} 
				
				if (in_array($UserGroup, $arrGroups)) { 
				  $isValid = true; 
				} 
				if (($strUsers == "") && false) { 
				  $isValid = true; 
				} 
			  } 
			  return $isValid; 
			}
			
			$MM_restrictGoTo = "" . $root . "login.php";
			if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) { 
			  setcookie("userStatus", "", time()-1000000000);  
			  unset($_SESSION['MM_Username']);
			  unset($_SESSION['MM_Usergroup']);
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
	
	//Grab all site info	
		$siteInfo = mysql_fetch_array(mysql_query("SELECT * FROM siteprofiles", $connDBA));
		
	//Include the doctype	
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\"><head>";
	
	//Include the title	
		echo "<title>" . $siteInfo['siteName'] .  " | " . $title . "</title>";
	
	//Include necessary scripts
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "styles/common/universal.css\" /><link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "styles/themes/" . $siteInfo['style'] . "\" /><link type=\"";
	
	//Include the shortcut icon	
		switch ($siteInfo['iconType']) {
			case "ico" : echo "image/x-icon"; break;
			case "jpg" : echo "image/jpeg"; break;
			case "gif" : echo "image/gif"; break;
		}
		
		echo "\" rel=\"shortcut icon\" href=\"" . $root . "images/icon." . $siteInfo['iconType'] . "\" />";
		
	//Include additional functions
		if ($functions == true) {
			$functionsArray = explode(",", $functions);
			
			foreach ($functionsArray as $functions) {
				$functions();
			}
		}
		
	//Include a <noscript> redirect	
		$requestURL = $_SERVER['REQUEST_URI'];
		
		if (!strstr($requestURL, "enable_javascript.php")) {
			echo "<noscript><meta http-equiv=\"refresh\" content=\"0; url=" . $root . "enable_javascript.php\"></noscript>";
		} else {
			echo "<script type=\"text/javascript\">window.location = \"index.php\"</script>";
		}
		
	//Include meta information
		if ($meta == true) {
			echo "<meta name=\"author\" content=\"" . stripslashes($siteInfo['author']) . "\" />
			<meta http-equiv=\"content-language\" content=\"" . stripslashes($siteInfo['language']) . "\" />
			<meta name=\"copyright\" content=\"" . stripslashes($siteInfo['copyright']) . "\" />";
			
			if ($description == "") {
				echo "<meta name=\"description\" content=\"" . stripslashes($siteInfo['description']) . "\" />";
			} else {
				echo "<meta name=\"description\" content=\"" . stripslashes(strip_tags($description)) . "\" />";
			}
			
			if ($additionalKeywords == "") {
				echo "<meta name=\"keywords\" content=\"" . stripslashes($siteInfo['meta']) . "\" />";
			} else {
				echo "<meta name=\"keywords\" content=\"" . stripslashes($siteInfo['meta']) . ", " . $additionalKeywords . "\" />";
			}
				
			echo "<meta name=\"generator\" content=\"Ensigma Pro\" />
			<meta name=\"robots\" content=\"index,follow\">";
		}
		
	//Close the header
		echo "</head><body" . $additionalParameters . ">";
		
	//Include a tooltip
		if ($toolTip == true) {
			echo "<script src=\"" . $root . "javascripts/common/tooltip.js\" type=\"text/javascript\"></script>";
		}
		
	//Begin the body HTML
		echo "<div id=\"page\"><div id=\"header_bg\"><div id=\"header\" class=\"clearfix\"><h1 class=\"headermain\">" . $siteInfo['siteName'] . "</h1><div class=\"headermenu\"><div class=\"logininfo\">";
		
	//Include the user login status
		if (isset ($_SESSION['MM_Username'])) {
			$userName = $_SESSION['MM_Username'];
			$nameGrabber = mysql_query ("SELECT * FROM users WHERE userName = '{$userName}'", $connDBA);
			$name = mysql_fetch_array($nameGrabber);
			$firstName = $name['firstName'];
			$lastName = $name['lastName'];
			
			switch($_SESSION['MM_UserGroup']) {
				case "Student" : $profileURL = "<a href=\"" . $root . "student/user/index.php\">"; break;
				case "Instructorial Assisstant" : $profileURL = "<a href=\"" . $root . "instructorial_assisstant/users/index.php\">"; break;
				case "Instructor" : $profileURL = "<a href=\"" . $root . "instructor/users/profile.php?id=" . $name['id'] . "\">"; break;
				case "Administrative Assisstant" : $profileURL = "<a href=\"" . $root . "administrative_assisstant/users/profile.php?id=" . $name['id'] . "\">"; break;
				case "Organization Administrator" : $profileURL = "<a href=\"" . $root . "organization_administrator/users/profile.php?id=" . $name['id'] . "\">"; break;
				case "Site Manager" : $profileURL = "<a href=\"" . $root . "site_manager/users/profile.php?id=" . $name['id'] . "\">"; break;
				case "Site Administrator" : $profileURL = "<a href=\"" . $root . "site_administrator/users/profile.php?id=" . $name['id'] . "\">"; break;
			}
			
			echo "You are logged in as " . $profileURL . $firstName . " " . $lastName . "</a> <a href=\"" . $root . "logout.php\">(Logout)</a>";
		} else {
			echo "You are not logged in. <a href=\"" . $root . "login.php\">(Login)</a>";
		}
	
	//Continue HTML	
		echo "</div></div></div><div id=\"banner_bg\"><div id=\"banner\">";
		
	//Include the logo
		echo "<div style=\"padding-top:" . $siteInfo['paddingTop'] . "px; padding-bottom:" . $siteInfo['paddingBottom'] . "px; padding-left:" .  $siteInfo['paddingLeft'] . "px; padding-right:" . $siteInfo['paddingRight'] . "px;\">";
		if (isset ($_SESSION['MM_UserGroup'])) {
			echo "<a href=\"" . $root . "site_administrator/index.php\">";
		} else {
			echo "<a href=\"" . $root . "index.php\">";
		}
		
		echo "<img src=\"" . "" . $root . "images/banner.png\"";
		if ($siteInfo['auto'] !== "on") {
			echo " width=\"" . $siteInfo['width'] . "\" height=\"" . $siteInfo['height'] . "\"";
		} 
		
		echo " alt=\"" . $siteInfo['siteName'] . "\" title=\"" . $siteInfo['siteName'] . "\"></a></div>";
		
	//Continue HTML
		echo "</div></div>";
	
	//Include the navigation bar
		$requestURL = $_SERVER['REQUEST_URI'];
		echo "<div id=\"navbar_bg\"><div class=\"navbar clearfix\"><div class=\"breadcrumb\"><div class=\"menu\"><ul>";
		
		if ($publicNavigation == false) {
			switch($_SESSION['MM_UserGroup']) {
				case "Student" : $URL = "Student"; break;
				case "Instructorial Assisstant" : $URL = "Instructorial Assisstant"; break;
				case "Instructor" :$URL = "Instructor"; break;
				case "Administrative Assisstant" : $URL = "Administrative Assisstant"; break;
				case "Organization Administrator" :  $URL = "Organization Administrator"; break;
				case "Site Manager" : $URL = "Site Manager"; break;
				case "Site Administrator" : $URL = "Site Administrator"; break;
			}
		} else {
			$URL = "Public";
		}
		
		switch ($URL) {
		//If this is the public website navigation bar
			case "Public" :
				$pageData = mysql_query("SELECT * FROM pages ORDER BY position ASC", $connDBA);	
				$lastPageCheck = mysql_fetch_array(mysql_query("SELECT * FROM pages ORDER BY position DESC LIMIT 1", $connDBA));
				
				if (isset ($_GET['page'])) {
					$currentPage = $_GET['page'];
				}
				
				while ($pageInfo = mysql_fetch_array($pageData)) {
					if (isset ($currentPage)) {
						if ($pageInfo['visible'] == "on") {
							if ($currentPage == $pageInfo['id']) {
								echo "<li><a class=\"topCurrentPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							} else {
								echo "<li><a class=\"topPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							}
							
							if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
								echo "</li><span class=\"arrow sep\">&#x25BA;</span>";
							} else {
								echo "</li>";
							}
						}
					} else {
						if ($pageInfo['visible'] == "on") {
							if ($pageInfo['position'] == "1") {
								echo "<li><a class=\"topCurrentPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							} else {
								echo "<li><a class=\"topPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>"; 
							}
							
							if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
								echo "</li><span class=\"arrow sep\">&#x25BA;</span>";
							} else {
								echo "</li>";
							}
						}
					}
				}
				break;
				
		//If this is the site administrator navigation bar
			case "Site Administrator" : 
				echo "<li><a class=\"";
				if (!strstr($requestURL, "site_administrator/users") && !strstr($requestURL, "site_administrator/organizations") && !strstr($requestURL, "site_administrator/communication") && !strstr($requestURL, "/modules") && !strstr($requestURL, "site_administrator/statistics") && !strstr($requestURL, "site_administrator/cms")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/index.php";
				echo "\">Home</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "site_administrator/users")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/users/index.php";
				echo "\">Users</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "site_administrator/organizations")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/organizations/index.php";
				echo "\">Organizations</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "site_administrator/communication")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/communication/index.php";
				echo "\">Communication</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "/modules")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/modules/index.php";
				echo "\">Modules</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "site_administrator/statistics")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/statistics/index.php";
				echo "\">Statistics</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "site_administrator/cms")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/cms/index.php";
				echo "\">Public Website</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"topPageNav\" href=\"";
				echo $root . "logout.php"; 
				echo "\">Logout</a></li>";
				break;
		}
		
		echo "</ul></div></div></div></div>";
	
	//Continue HTML	
		echo "</div>";
		echo "<div id=\"content\"><div class=\"box generalboxcontent boxaligncenter\">";
	}
	
	//Include a footer
	function footer($publicNavigation = false) {
		global $connDBA;
		global $root;
		
	//Include the navigation bar
		$requestURL = $_SERVER['REQUEST_URI'];
		echo "<br /></div></div><div id=\"footer\"><div>&nbsp;</div><div class=\"breadcrumb\">";
		
		if ($publicNavigation == false) {
			switch($_SESSION['MM_UserGroup']) {
				case "Student" : $URL = "Student"; break;
				case "Instructorial Assisstant" : $URL = "Instructorial Assisstant"; break;
				case "Instructor" :$URL = "Instructor"; break;
				case "Administrative Assisstant" : $URL = "Administrative Assisstant"; break;
				case "Organization Administrator" :  $URL = "Organization Administrator"; break;
				case "Site Manager" : $URL = "Site Manager"; break;
				case "Site Administrator" : $URL = "Site Administrator"; break;
			}
		} else {
			$URL = "Public";
		}
		
		switch ($URL) {
		//If this is the public website footer bar
			case "Public" :
				$pageData = mysql_query("SELECT * FROM pages ORDER BY position ASC", $connDBA);	
				$lastPageCheck = mysql_fetch_array(mysql_query("SELECT * FROM pages ORDER BY position DESC LIMIT 1", $connDBA));
				
				if (isset ($_GET['page'])) {
					$currentPage = $_GET['page'];
				}
			
				while ($pageInfo = mysql_fetch_array($pageData)) {
					if (isset ($currentPage)) {
						if ($pageInfo['visible'] != "") {
							if ($currentPage == $pageInfo['id']) {
								echo "<a class=\"bottomCurrentPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							} else {
								echo "<a class=\"bottomPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							}
							
							if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
								echo "<span class=\"arrow sep\">&bull;</span>";
							}
						}
					} else {
						if ($pageInfo['visible'] != "") {
							if ($pageInfo['position'] == "1") {
								echo "<a class=\"bottomCurrentPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							} else {
								echo "<a class=\"bottomPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							}
							
							if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
								echo "<span class=\"arrow sep\">&bull;</span>";
							}
						}
					}
				}
				break;
			
		//If this is the site administrator footer bar
			case "Site Administrator" : 
				echo "<a class=\"";
				if (!strstr($requestURL, "site_administrator/users") && !strstr($requestURL, "site_administrator/organizations") && !strstr($requestURL, "site_administrator/communication") && !strstr($requestURL, "/modules") && !strstr($requestURL, "site_administrator/statistics") && !strstr($requestURL, "site_administrator/cms")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/index.php";
				echo "\">Home</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "site_administrator/users")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/users/index.php";
				echo "\">Users</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "site_administrator/organizations")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/organizations/index.php";
				echo "\">Organizations</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "site_administrator/communication")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/communication/index.php";
				echo "\">Communication</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "/modules")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/modules/index.php";
				echo "\">Modules</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "site_administrator/statistics")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/statistics/index.php";
				echo "\">Statistics</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "site_administrator/cms")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/cms/index.php";
				echo "\">Public Website</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"bottomPageNav\" href=\"";
				echo $root . "logout.php"; 
				echo "\">Logout</a>";
				break;
		}
	
	//Include the footer text	
		echo "</div><div class=\"footer\">";
		
		$footerGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);	
		$footer= mysql_fetch_array($footerGrabber);
		
		echo stripslashes($footer['siteFooter']) . "</div></div></div>";
	
	//Close the HTML
		echo "</body></html>";
	
	//Log stats and activity	
		stats("true");
		activity("true");
	}
/* End site layout functions */
	
/* Begin login management functions */
	//Login a user
	function login() {
		global $connDBA;
		global $root;
		
		if (isset ($_SESSION['MM_Username'])) {
			$requestedURL = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			$homePageCheck = str_replace($root, "", $requestedURL);
			
			if ($homePageCheck !== "index.php") {
				$userRole = $_SESSION['MM_UserGroup'];
				
				switch ($userRole) {
					case "Student" : header ("Location: student/index.php"); exit; break;
					case "Instructorial Assisstant" : header("Location: instructorial_assisstant/index.php"); exit; break;
					case "Instructor" : header ("Location: instructor/index.php"); exit; break;
					case "Administrative Assisstant" : header("Location: administrative_assisstant/index.php"); exit; break;
					case "Organization Administrator" :  header ("Location: organization_administrator/index.php"); exit; break;
					case "Site Manager" : header ("Location: site_manager/index.php"); exit; break;
					case "Site Administrator" : header ("Location: site_administrator/index.php"); exit; break;
				}
			}
		} else {
			if (!function_exists("GetSQLValueString")) {
				function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
		  			$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
					$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
		
					switch ($theType) {
					  case "text" : $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL"; break;    
					  case "long":
					  case "int": $theValue = ($theValue != "") ? intval($theValue) : "NULL"; break;
					  case "double": $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL"; break;
					  case "date": $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL"; break;
					  case "defined": $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue; break;
					}
					
					return $theValue;
				}
			}
		
			$loginFormAction = $_SERVER['PHP_SELF'];
			
			if (isset($_GET['accesscheck'])) {
				$_SESSION['PrevUrl'] = $_GET['accesscheck'];
			}
			
			if (isset($_POST['username'])) {
				$loginUsername=$_POST['username'];
				$password=$_POST['password'];
				$MM_fldUserAuthorization = "role";
				
				$userRoleGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$loginUsername}' AND `passWord` = '{$password}'");
				
				if ($userRole = mysql_fetch_array($userRoleGrabber)) {
					$success = "";
					$failure = "";
					
					if (isset($_GET['accesscheck'])) {
						$success .= "http://" . $_SERVER['HTTP_HOST'] . urldecode($_GET['accesscheck']);
					} else {
						switch ($userRole['role']) {
							case "Student" :  $success .= "student/index.php"; break;
							case "Instructorial Assisstant" :  $success .= "instructorial_assisstant/index.php"; break;
							case "Instructor" :  $success .= "instructor/index.php"; break;
							case "Administrative Assisstant" :  $success .= "administrative_assisstant/index.php"; break;
							case "Organization Administrator" :  $success .= "organization_administrator/index.php"; break;
							case "Site Manager" :  $success .= "site_manager/index.php"; break;
							case "Site Administrator" :  $success .= "site_administrator/index.php"; break;
						}
					}
				} else {
					$success = "";
					$failure = "login.php?alert";
				}
			  
				$MM_redirectLoginSuccess = $success;
				$MM_redirectLoginFailed = $failure;
				$MM_redirecttoReferrer = false;
				  
				$LoginRS__query=sprintf("SELECT userName, passWord, role FROM users WHERE userName=%s AND passWord=%s",
				GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
				 
				$LoginRS = mysql_query($LoginRS__query, $connDBA) or die(mysql_error());
				$loginFoundUser = mysql_num_rows($LoginRS);
				
				if ($loginFoundUser) {
					$loginStrGroup  = mysql_result($LoginRS,0,'role');
					
					$_SESSION['MM_Username'] = $loginUsername;
					$_SESSION['MM_UserGroup'] = $loginStrGroup;	
					
					$userIDGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$loginUsername}' AND `passWord` = '{$password}' LIMIT 1");
					$userID = mysql_fetch_array($userIDGrabber);
					setcookie("userStatus", $userID['sysID'], time()+1000000000); 
					
					$cookie = $userID['sysID'];
					mysql_query("UPDATE `users` SET `active` = '1' WHERE `sysID` = '{$cookie}'", $connDBA);
					
			  
				  if (isset($_SESSION['PrevUrl']) && false) {
					  $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
				  }
				  
				  if (!isset($_GET['accesscheck'])) {
					  header("Location: " . $root . $MM_redirectLoginSuccess);
					  exit;
				  } else {
					  header ("Location: " . $success);
					  exit;
				  }
				} else {
				  header("Location: " . $root . $MM_redirectLoginFailed);
				  exit;
				}
			}
		}
	}
/* End login management functions */

/* Begin constructor functions */
	//Page title and introductory text
	function title($title, $text = false, $break = true) {
		echo "<h2>" . $title . "</h2>";
		
		if ($text == true) {
			echo $text;
		}
		
		if ($break == true) {
			echo "<p>&nbsp;</p>";
		}
	}
	
	//Messages
	function message($trigger, $triggerValue, $type, $text) {
		global $messageBreakLimit;	
			
		if (isset($_GET[$trigger]) && $_GET[$trigger] == $triggerValue) {
			if ($type == "success") {
				successMessage($text);
			} elseif ($type == "error") {
				errorMessage($text);
			}
		} else {
			if (!isset($messageBreakLimit)) {
				echo "<br />";
			}
			
			$messageBreakLimit = "true";
		}
	}
	
	//Links
	function URL($text, $URL, $class = false, $target = false, $toolTip = false, $delete = false, $newWindow = false, $width = false, $height = false, $additionalParameters = false) {
		if ($newWindow == false || $width == false || $height == false) {
			$return = "<a href=\"" . $URL . "\"";
			
			if ($target == true) {
				$return .= " target=\"" . $target . "\"";
			}
			
			if ($class == true) {
				$return .= " class=\"" . $class . "\"";
			}
			
			if ($toolTip == true) {
				 $return .= " onmouseover=\"Tip('" . prepare($toolTip, true, false) . "')\" onmouseout=\"UnTip()\"";
			}
			
			if ($delete == true) {
				 $return .= " onclick=\" return confirm('This action cannot be undone. Continue?');" . $additionalParameters . "\"";
			} elseif ($additionalParameters == true) {
				 $return .= " onclick=\"" . $additionalParameters . "\"";
			}
			
			$return .= ">" . prepare($text) . "</a>";
		} else {
			$return = "<a href=\"javascript:void\" onclick=\"window.open('" . $URL . "','Window','status=yes,scrollbars=yes,resizable=yes,width=" . $height . ",height=" . $width . "')\"";
			
			if ($toolTip == true) {
				 $return .= " onmouseover=\"Tip('" . prepare($toolTip, true, false) . "')\" onmouseout=\"UnTip()\"";
			}
			
			$return .= ">" . $text . "</a>";
		}
		
		return $return;
	}
	
	//Form
	function form($name, $method = "post", $validate = true, $containsFile = false, $action = false, $additionalParameters = false) {
		echo "<form name=\"" . $name . "\" method=\"" . $method . "\" id=\"validate\"";
		
		if ($containsFile == true) {
			echo " enctype=\"multipart/form-data\"";
		}
		
		echo " action=\"";
		
		if ($action == false) {
			$getParameters = $_GET;
			
			if (sizeof($getParameters) >= 1) {
				$parameters = "?";
				
				while(list($parameter, $value) = each($getParameters)) {
					$parameters .= $parameter . "=" . $value . "&";
				}
			}
			
			if (isset($parameters)) {
				echo $_SERVER['PHP_SELF'] . rtrim($parameters, "&");
			} else {
				echo $_SERVER['PHP_SELF'];
			}
		} else {
			echo $action;
		}
		
		echo "\"";
		
		if ($validate == true) {
			echo " onsubmit=\"return errorsOnSubmit(this);" . $additionalParameters . "\"";
		} else {
			if ($additionalParameters == true) {
				echo " onsubmit=\"" . $additionalParameters . "\"";
			}
		}
		
		echo ">";
	}
	
	function closeForm($advancedClose = true, $errors = true) {
		global $root;
		
		if ($errors == true) {
			echo "<div id=\"errorBox\" style=\"display:none;\">Some fields are incomplete, please scroll up to correct them.</div><div id=\"progress\" style=\"display:none;\"><p><span class=\"require\">Uploading in progress... </span><img src=\"" . $root . "images/common/loading.gif\" alt=\"Uploading\" width=\"16\" height=\"16\" /></p></div>";
		}
		
		if ($advancedClose == true) {
			echo "</div>";
		}
		
		echo "</form>";
	}
	
	//Form layout
	function catDivider($content, $class, $first = false, $last = false) {
		if ($first == false) {
			echo "</div>";
		}
		
		echo "<div class=\"catDivider " . $class . "\">" . $content . "</div>";
		
		if ($last == false) {
			echo "<div class=\"stepContent\">";
		}
	}
	
	//Form step
	function directions($text, $required = false, $help = false) {
		global $root;
		
		echo "<p>" . $text;
		
		if ($required == true) {
			echo "<span class=\"require\">*</span>";
		}
		
		echo ": ";
		
		if ($help == true) {
			echo "<img src=\"" . $root . "images/admin_icons/help.png\" alt=\"Help\" width=\"17\" height=\"17\" onmouseover=\"Tip('" . $help . "')\" onmouseout=\"UnTip()\" />";
		}
		
		echo "</p>";
	}
	
	//Button
	function button($name, $id, $value, $type, $URL = false, $additionalParameters = false) {		
		switch ($type) {
			case "submit" : 
				echo "<input type=\"submit\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"tinyMCE.triggerSave();" . $additionalParameters . "\">"; break;
			case "reset" : 
				echo "<input type=\"reset\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"return confirm('Are you sure you wish to reset all of the content in this form? Click OK to continue');$.validationEngine.closePrompt('#validate',true);" . $additionalParameters . "\">"; break;
			case "cancel" : 
				echo "<input type=\"reset\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"window.location='" . $URL . "';" . $additionalParameters . "\">"; break;
			case "history" : 
				echo "<input type=\"button\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"history.go(-1);" . $additionalParameters . "\">"; break;
			case "button" : 
				echo "<input type=\"button\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\"" . $additionalParameters . "\">"; break;
		}
	}
	
	//Checkbox
	function checkbox($name, $id, $label = false, $checkboxValue = false, $validate = true, $minValues = false, $manualSelect = false, $editorTrigger = false, $arrayValue = false, $matchingValue = false, $additionalParameters = false) {
		echo "<label><input type=\"checkbox\" name=\"" . $name . "\" id=\"" . $id . "\"";
		
		if ($validate == true && $minValues == true) {
			echo " class=\"validate[required,minCheckbox[" . $minValues . "]]\"";
		}
		
		global $$editorTrigger;
		
		if ($manualSelect == true && ($editorTrigger == false || !isset($$editorTrigger))) {
			echo " checked=\"checked\"";
		} else {
			if ($editorTrigger == true && isset($$editorTrigger)) {
				$value = $$editorTrigger;
				
				if (isset($$editorTrigger)) {
					if ($value[$arrayValue] == $matchingValue) {
						echo " checked=\"checked\"";
					}
				}
			}
		}
		
		if ($checkboxValue == true) {
			echo " value=\"" . $checkboxValue  . "\"";
		}
		
		echo $additionalParameters . "/>" . $label . "</label>";
	}
	
	//Dropdown menu
	function dropDown($name, $id, $values, $valuesID, $multiple = false, $validate = true, $validateAddition = false, $manualSelect = false, $editorTrigger = false, $arrayValue = false, $additionalParameters = false) {
		$valuesArray = explode(",", $values);
		$valuesIDArray = explode(",", $valuesID);
		$valuesLimit = sizeof($valuesArray) - 1;
		
		if (sizeof($valuesArray) != sizeof($valuesIDArray)) {
			die(errorMessage("The values and IDs of the " . $name . " dropdown menu to not match"));
		} else {
			echo "<select name=\"" . $name . "\" id=\"" . $id . "\"";
			
			
			if ($multiple == false) {
				if ($validate == true) {
					echo " class=\"validate[required" . $validateAddition . "]\"";
				}
			} else {
				if ($validate == true) {
					echo " multiple=\"multiple\" class=\"multiple validate[required" . $validateAddition . "]\"";
				} else {
					echo " multiple=\"multiple\" class=\"multiple\"";
				}
			}
			
			echo $additionalParameters . ">";
			
			for ($count = 0; $count <= $valuesLimit; $count ++) {
				global $$editorTrigger;
				
				echo "<option value=\"" . $valuesIDArray[$count] . "\"";
				
				if (($manualSelect == true || $manualSelect == "0") && ($editorTrigger == false || !isset($$editorTrigger))) {
					if ($manualSelect == $valuesIDArray[$count]) {
						echo " selected=\"selected\"";
					}
				} else {
					if ($editorTrigger == true && isset($$editorTrigger)) {
						$value = $$editorTrigger;
						
						if (isset($$editorTrigger)) {
							if ($value[$arrayValue] == $valuesIDArray[$count]) {
								echo " selected=\"selected\"";
							}
						}
					}
				}
				
				echo ">" . $valuesArray[$count] . "</option>";
			}
			
			echo "</select>";
		}			
	}
	
	//File upload
	function fileUpload($name, $id, $size = false, $validate = true, $validateAddition = false, $editorTrigger = false, $arrayValue = false, $fileURL = false, $uploadNote = false, $additionalParameters = false) {
		global $$editorTrigger;
		
		if ($editorTrigger == true && isset($$editorTrigger)) {
			$value = $$editorTrigger;
			
			if (isset($$editorTrigger)) {
				echo "Current file: <a href=\"" . $fileURL . "/" . $value[$arrayValue] . "\" target=\"_blank\">" . $value[$arrayValue] . "</a><br />";
			}
		}
		
		echo "<input type=\"file\" name=\"" . $name . "\" id=\"" . $id . "\" size=\"";
		
		if ($size == false) {
			echo "50";
		} else {
			echo $size;
		}
		
		echo "\"";
		
		if ($validate == true) {
			echo " class=\"validate[required" . $validateAddition . "]\"";
		}
		
		echo ">";
		
		if ($uploadNote == true && $editorTrigger == true && isset($$editorTrigger)) {
			echo "<br /><strong>Note:</strong> Uploading a new file will replace the existing one.";
		}
	}
	
	//Hidden
	function hidden($name, $id, $value) {
		echo "<input type=\"hidden\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" />";
	}
	
	//Radio button
	function radioButton($name, $id, $buttonLabels, $buttonValues, $inLine = true, $validate = true, $validateAddition = false, $manualSelect = false, $editorTrigger = false, $arrayValue = false, $additionalParameters = false) {
		$labelsArray = explode(",", $buttonLabels);
		$valuesArray = explode(",", $buttonValues);
		$valuesLimit = sizeof($labelsArray) - 1;
		
		for ($count = 0; $count <= $valuesLimit; $count ++) {
			global $$editorTrigger;
			
			echo "<label><input type=\"radio\" name=\"" . $name . "\" id=\"" . $id . "_" . $count . "\" value=\"" . $valuesArray[$count] . "\"";
			
			if (($manualSelect == true || $manualSelect == "0") && ($editorTrigger == false || !isset($$editorTrigger))) {
				if ($valuesArray[$count] == $manualSelect) {
					echo " checked=\"checked\"";
				}
			} else {
				
				if ($editorTrigger == true && isset($$editorTrigger)) {
					$value = $$editorTrigger;
					
					if (isset($$editorTrigger)) {
						if ($valuesArray[$count] == $value [$arrayValue]) {
							echo " checked=\"checked\"";
						}
					}
				}
			}
			
			if ($validate == true) {
				echo " class=\"validate[required" . $validateAddition . "] radio\"";
			}
			
			echo $additionalParameters . ">" . $labelsArray[$count] . "</label>";
			
			if ($count != $valuesLimit) {
				if ($inLine != true) {
					echo "<br />";
				}
			}
		}
	}
	
	//Textarea
	function textArea($name, $id, $size, $validate = true, $validateAddition = false, $manualValue = false, $editorTrigger = false, $arrayValue = false, $additionalParameters = false) {
		echo "<textarea name=\"" . $name . "\" id=\"" . $id . "\" style=\"";
		
		if ($size == "large") {
			echo "width:640px; height:320px;";
		} elseif ($size == "small") {
			echo "width:450px;";
		}
		
		echo "\"";
		
		if ($validate == true) {
			echo " class=\"validate[required" . $validateAddition . "]\"";
		}
		
		echo $additionalParameters . ">";
		
		global $$editorTrigger;
		
		if ($manualValue == true && ($editorTrigger == false || !isset($$editorTrigger))) {
			echo $manualValue;
		} else {
			if ($editorTrigger == true && isset($$editorTrigger)) {
				$value = $$editorTrigger;
				
				if (isset($$editorTrigger)) {
					echo $value[$arrayValue];
				}
			}
		}
		
		echo "</textarea>";
	}
	
	//Text Fields
	function textField($name, $id, $size = false, $limit = false, $password = false, $validate = true, $validateAddition = false, $manualValue = false, $editorTrigger = false, $arrayValue = false, $additionalParameters = false) {
		echo "<input type=\"";
		
		if ($password == false) {
			echo "text";
		} else {
			echo "password";
		}
			
		echo "\"";
		
		if ($limit == true) {
			echo " maxlength=\"" . $limit . "\"";
		}
		
		echo " name=\"" . $name . "\" id=\"" . $id . "\" size=\"";
		
		if ($size == false) {
			echo "50";
		} else {
			echo $size;
		}
		
		echo "\" autocomplete=\"off\"";
		
		if ($validate == true) {
			echo " class=\"validate[required" . $validateAddition . "]\"";
		}
		
		global $$editorTrigger;
		
		if ($manualValue == true && ($editorTrigger == false || !isset($$editorTrigger))) {
			echo "value=\"" . $manualValue . "\"";
		} else {
			if ($editorTrigger == true && isset($$editorTrigger)) {
				$value = $$editorTrigger;
				
				if (isset($$editorTrigger)) {
					echo "value=\"" . $value[$arrayValue] . "\"";
				}
			}
		}
		
		echo $additionalParameters . " />";
	}
	
	//Sideboxes
	function sideBox($title, $type, $text, $allowRoles = false, $editID = false) {
		//Include the title
		echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\">" . $title;
		
		//Display the content
		$premitted = false;
		
		if (isset($_SESSION['MM_UserGroup']) && $allowRoles == true) {
			foreach (explode(",", $allowRoles) as $role) {
				if ($_SESSION['MM_UserGroup'] == $role) {
					$premitted = true;
				}
			}
		}
		
		switch ($type) {
			case "Custom Content" :				
				if (!isset($_SESSION['MM_UserGroup'])) {
					echo "</div></div><div class=\"content\">" . $text . "</div>";
				} elseif (isset($_SESSION['MM_UserGroup']) && $premitted == true) {
					echo "&nbsp;" . URL("", "site_administrator/cms/manage_sidebar.php?id=" . $editID, "smallEdit") . "</div></div><div class=\"content\">" . $text . "</div>";
				} else {
					echo "</div></div><div class=\"content\">" . $text . "</div>";
				} break;
			case "Login" :
				$roles = explode(",", $allowRoles);
			
				if (!isset($_SESSION['MM_UserGroup'])) {
					echo "</div></div><div class=\"content\">";
					form("login");
					echo "<p>User name: ";
					textField("username", "username", "30");
					echo "<br />Password: ";
					textField("password", "password", "30", false, true);
					echo"</p><p>";
					button("submit", "submit", "Login", "submit");
					echo "</p>";
					closeForm(false, false);
					echo "</div>";
				} elseif (isset($_SESSION['MM_UserGroup']) && $premitted == true) {
					echo "&nbsp;" . URL("", "site_administrator/cms/manage_sidebar.php?id=" . $editID, "smallEdit") . "</div></div>";
				} else {
					echo "</div></div>";
				} break;
				
			case "Register" :
				$roles = explode(",", $allowRoles);
			
				if (!isset($_SESSION['MM_UserGroup'])) {
					echo "</div></div><div class=\"content\">" . $sideBar['content'];
					form("login");
					echo "<div align=\"center\">";
					button("register", "register", "Register", "cancel", "register.php");
					echo "</div></div>";
				} elseif (isset($_SESSION['MM_UserGroup']) && $premitted == true) {
					echo "&nbsp;" . URL("", "site_administrator/cms/manage_sidebar.php?id=" . $editID, "smallEdit") . "</div></div>";
				} else {
					echo "</div></div>";
				} break;
		}
		
		//Close the HTML
		echo "</div><br />";
	}
	
	//Live option
	function option($id, $state, $checkboxTrigger, $type) {
		if ($type == "visible") {			
			if ($state == "") {
				$class = " hidden";
			} else {
				$class = "";
			}
		} else {
			$type = "checked";
			
			if ($state == "") {
				$class = " unchecked";
			} else {
				$class = "";
			}
		}
		
		echo "<div align=\"center\">";
		form("avaliability");
		hidden("action", "action", "setAvaliability");
		hidden("id", "id", $id);
		echo URL("", "#option" . $id, $type . $class);
		echo "<div class=\"contentHide\">";
		checkbox("option", "option" . $id, false, false, false, $checkboxTrigger, $checkboxTrigger, "visible", "on", " onclick=\"Spry.Utils.submitForm(this.form);\"");
		echo "</div>";
		closeForm(false, false);
		echo "</div>";
	}
	
	//Reordering menu
	function reorderMenu ($id, $state, $menuTrigger, $table) {
		global $connDBA;
		
		$itemCountGrabber = mysql_query("SELECT * FROM {$table}", $connDBA);
		$itemCount = mysql_num_rows($itemCountGrabber);
		$values = "";
		
		for ($count = 1; $count <= $itemCount; $count++) {			
			if ($count < $itemCount) {
				$values .= $count . ",";
			} else {
				$values .= $count;
			}
		}
		
		form("reorder");
		hidden("id", "id", $id);
		hidden("currentPosition", "currentPosition", $state);
		hidden("action", "action", "modifyPosition");
		dropDown("position", "position", $values, $values, false, false, false, $state, false, false, " onchange=\"this.form.submit();\"");
		closeForm(false, false);
	}
	
	//Lesson content
	function lesson($name, $table, $displayTitle = true, $displayNavigation = true) {
		global $monitor, $connDBA, $root;
		
	//Grab all of the lesson data
		if (isset($_GET['page'])) {
			if (exist($monitor['lessonTable']) == true) {
				$page = $_GET['page'];
				$lesson = exist($monitor['lessonTable'], "position", $page);
				if ($lesson = exist($monitor['lessonTable'], "position", $page)) {
					$lastPageGrabber = mysql_query("SELECT * FROM `{$monitor['lessonTable']}` ORDER BY `position` DESC LIMIT 1", $connDBA);
					$lastPageCheck = mysql_fetch_array($lastPageGrabber);
					$lastPage = $lastPageCheck['position'];
				} else {
					redirect($_SERVER['PHP_SELF'] . "?page=1");
				}
			} else {
				$_SESSION['step'] = "lessonContent";
				redirect($_SERVER['PHP_SELF']);
			}
		} else {
			redirect($_SERVER['PHP_SELF'] . "?page=1");
		}
		
	//Display the navigation
		$navigation = "<div class=\"layoutControl\">";
		
		if ($lesson['position'] > 1) {
			$previousPage = $lesson['position'] - 1;
			$previousPageGrabber = mysql_query("SELECT * FROM `{$monitor['lessonTable']}` WHERE `position` = '{$previousPage}'", $connDBA);
			$previousPage = mysql_fetch_array($previousPageGrabber);
			$page = $lesson['position'] - 1;
			
			$navigation .= "<div class=\"halfLeft\"><div class=\"previousPage\"><a href=\"" . $_SERVER['PHP_SELF'] . "?page=" . $page . "\">&lt;&lt; Previous Page<br /><span class=\"pageTitle\">" . $previousPage['title'] . "</span></a></div></div>";
		}
		
		if ($lesson['position'] < $lastPage) {
			$nextPage = $lesson['position'] + 1;
			$nextPageGrabber = mysql_query("SELECT * FROM `{$monitor['lessonTable']}` WHERE `position` = '{$nextPage}'", $connDBA);
			$nextPage = mysql_fetch_array($nextPageGrabber);
			$page = $lesson['position'] + 1;
			
			$navigation .= "<div class=\"halfRight\"><div class=\"nextPage\"><a href=\"" . $_SERVER['PHP_SELF'] . "?page=" . $page . "\">Next Page &gt;&gt;<br /><span class=\"pageTitle\">" . $nextPage['title'] . "</span></a></div></div>";
		}
		
		$navigation .= "</div>";
		
		if ($displayNavigation == true) {
			echo $navigation;
		}
		
	//Display the title
		if ($displayTitle == true) {
			echo "<h2>" . $lesson['title'] . "</h2>";
		}
		
	//Display the content
		echo "<br /><br />";
		
		if ($lesson['type'] == "Custom Content") {
			echo $lesson['content'];
		}
		
		if ($lesson['type'] == "Embedded Content") {
			echo $lesson['content'];
			echo "<br />";
			
			echo "<div align=\"center\">";
			
			$location = str_replace(" ", "", $name);
			$file = $root . "gateway.php/modules/" . $location . "/lesson/" . $lesson['attachment'];
			$fileType = extension($file);
			
			switch ($fileType) {
			//If it is a PDF
				case "pdf" : echo "<iframe src=\"" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
			//If it is a Word Document
				case "doc" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "images/programIcons/word2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				case "docx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "images/programIcons/word2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is a PowerPoint Presentation
				case "ppt" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "images/programIcons/powerPoint2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				case "pptx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "images/programIcons/powerPoint2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is an Excel Spreadsheet
				case "xls" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "images/programIcons/excel2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				case "xlsx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "images/programIcons/excel2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is a Standard Text Document
				case "txt" : echo "<iframe src=\"" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
				case "rtf" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "images/programIcons/text.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is a WAV audio file
				case "wav" : echo "<object width=\"640\" height=\"16\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"controller\" value=\"true\"><embed src=\"" . $file . "\" width=\"640\" height=\"16\" autoplay=\"true\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object>"; break;
			//If it is an MP3 audio file
				case "mp3" : echo "<object id=\"player\" width=\"640\" height=\"30\" data=\"" . $root . "player/player.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $root . "player/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":\"" . $file . "\", \"plugins\":{\"controls\":{\"autoHide\":false}}}' /></object>"; break;
			//If it is an AVI video file
				case "avi" : echo "<object id=\"MediaPlayer\" width=\"640\" height=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"true\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"1\" showdisplay=\"0\" autostart=\"1\"></embed></object>"; break;
			//If it is an WMV video file
				case "wmv" : echo "<object id=\"MediaPlayer\" width=\"640\" height=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"true\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"1\" showdisplay=\"0\" autostart=\"1\"></embed></object>"; break;
			//If it is an FLV file
				case "flv" : echo "<object id=\"player\" width=\"640\" height=\"480\" data=\"" . $root . "player/player.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $root . "player/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":\"" . $file . "\"}' /></object>"; break;
			//If it is an MOV video file
				case "mov" : echo "<object width=\"640\" height=\"480\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"controller\" value=\"true\"><embed src=\"" . $file . "\" width=\"640\" height=\"480\" autoplay=\"true\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object>"; break;
			//If it is an MP4 video file			
				case "mp4" : echo "<object id=\"player\" width=\"640\" height=\"480\" data=\"" . $root . "player/player.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $root . "/player/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":\"" . $file . "\"}' /></object>"; break;
			//If it is a SWF video file
				case "swf" : echo "<object width=\"640\" height=\"480\" data=\"" . $file . "\" type=\"application/x-shockwave-flash\">
<param name=\"src\" value=\"" . $file . "\" /></object>"; break;
			}
			
			echo "</div>";
		}
		
	//Display the navigation
		if ($displayNavigation == true) {
			echo "<br /><br />" . $navigation;
		}
	}
	
	//Max file size
	function maxFileSize() {
		echo "Max file size: " .  ini_get('upload_max_filesize');
	}
	
	//Statistics charting
	function chart($type, $source, $width = false, $height = false) {
		if ($width == false) {
			$width = "600";
		}
		
		if ($height == false) {
			$height = "350";
		}
		
		echo "<div align=\"center\"><embed type=\"application/x-shockwave-flash\" src=\"statistics/charts/" . $type . ".swf\" id=\"chart\" name=\"chart\" quality=\"high\" allowscriptaccess=\"always\" flashvars=\"chartWidth=" . $width . "&chartHeight=" . $height . "&debugMode=0&DOMId=overallstats&registerWithJS=0&scaleMode=noScale&lang=EN&dataURL=statistics/data/index.php?type=" . $source . "\" wmode=\"transparent\" width=\"" . $width . "\" height=\"" . $height . "\"></div>";

	}
/* End constructor functions */

/* Begin processor functions */
	//Check item existance
	function exist($table, $column = false, $value = false) {
		global $connDBA;
		
		if ($column == true) {
			$additionalCheck = " WHERE `{$column}` = '{$value}'";
		} else {
			$additionalCheck = "";
		}
		
		$itemCheckGrabber = mysql_query("SELECT * FROM {$table}{$additionalCheck}", $connDBA);
		$itemCheck = mysql_num_rows($itemCheckGrabber);
		
		if ($itemCheck >= 1) {
			$itemGrabber = mysql_query("SELECT * FROM {$table}{$additionalCheck}", $connDBA);
			$item = mysql_fetch_array($itemGrabber);
			
			return $item;
		} else {
			return false;
		}
	}
	
	//Reorder items
	function reorder($table, $redirect) {
		global $connDBA;
		
		if (isset($_POST['action']) && $_POST['action'] == "modifyPosition" && isset($_POST['id']) && isset($_POST['position']) && isset($_POST['currentPosition'])) {
			$id = $_POST['id'];
			$newPosition = $_POST['position'];
			$currentPosition = $_POST['currentPosition'];
			
			$itemCheck = mysql_query("SELECT * FROM {$table} WHERE position = {$currentPosition}", $connDBA);
			
			if (!$itemCheck) {
				header ("Location: " . $redirect);
				exit;
			}
		  
			if ($currentPosition > $newPosition) {
				mysql_query("UPDATE {$table} SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'", $connDBA);
				mysql_query ("UPDATE {$table} SET position = '{$newPosition}' WHERE id = '{$id}'", $connDBA);
				
				header ("Location: " . $redirect);
				exit;
			} elseif ($currentPosition < $newPosition) {
				mysql_query("UPDATE {$table} SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'", $connDBA);
				mysql_query("UPDATE {$table} SET position = '{$newPosition}' WHERE id = '{$id}'", $connDBA);
				
				header ("Location: " . $redirect);
				exit;
			} else {
				header ("Location: " . $redirect);
				exit;
			}
		}
	}
	
	//Set avaliability
	function avaliability($table, $redirect) {
		global $connDBA;
		
		if (isset($_POST['id']) && $_POST['action'] == "setAvaliability") {			
			$id = $_POST['id'];
			
			if (!$_POST['option']) {
				$option = "";
			} else {
				$option = $_POST['option'];
			}
			
			mysql_query("UPDATE {$table} SET `visible` = '{$option}' WHERE id = '{$id}'", $connDBA);
			
			header ("Location: " . $redirect);
			exit;
		}
	}
	
	//Delete an item
	function delete($table, $redirect, $reorder = true, $file = false, $directory = false, $extraTables = false) {
		global $connDBA;
		
		if (isset ($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['id'])) {
			$deleteItem = $_GET['id'];
		
			$itemCheck = mysql_query("SELECT * FROM {$table} WHERE position = {$deleteItem}", $connDBA);
			
			if (!$itemCheck) {
				header ("Location: " . $redirect);
				exit;
			}
			
			if ($reorder = true) {
				$itemPositionGrabber = mysql_query("SELECT * FROM pages WHERE position = {$pageLift}", $connDBA);
				$itemPositionFetch = mysql_fetch_array($itemPositionGrabber);
				$itemPosition = $itemPositionFetch['position'];
				
				mysql_query("UPDATE {$table} SET position = position-1 WHERE position > '{$itemPosition}'", $connDBA);
				mysql_query("DELETE FROM {$table} WHERE id = {$deleteItem}", $connDBA);
			} else {
				mysql_query("DELETE FROM {$table} WHERE id = {$deleteItem}", $connDBA);
			}
			
			if ($file == true) {
				unlink($file);
			}
			
			if ($directory == true) {
				deleteAll($directory);
			}
			
			if ($extraTables == true) {
				$tables = explode(",", $extraTables);
				
				foreach ($tables as $table) {
					mysql_query("DROP TABLE `{$table}`");
				}
			}
			
			header ("Location: " . $redirect);
			exit;
		}
	}
	
	//Redirect to page
	function redirect($URL) {
		header("Location: " . $URL);
		exit;
	}
/* End processor functions */

/* Begin page scripting functions */
	//Include the tiny_mce simple widget
	function tinyMCESimple () {
		global $connDBA;
		global $root;
		
		echo "<script type=\"text/javascript\" src=\"" . $root . "tiny_mce/tiny_mce.js\"></script><script type=\"text/javascript\" src=\"" . $root . "tiny_mce/plugins/atd-tinymce/editor_plugin.js\"></script><script type=\"text/javascript\" src=\"" . $root . "javascripts/common/tiny_mce_simple.php\"></script>";
	}
	
	//Include the tiny_mce advanced widget
	function tinyMCEAdvanced () {
		global $connDBA;
		global $root;
		
		echo "<script type=\"text/javascript\" src=\"" . $root . "tiny_mce/tiny_mce.js\"></script><script type=\"text/javascript\" src=\"" . $root . "tiny_mce/plugins/atd-tinymce/editor_plugin.js\"></script>
<script type=\"text/javascript\" src=\"" . $root . "javascripts/common/tiny_mce_advanced.php\"></script><script type=\"text/javascript\" src=\"" . $root . "tiny_mce/plugins/tinybrowser/tb_tinymce.js.php\"></script>";
	}
	
	//Include a form validator
	function validate () {
		global $connDBA;
		global $root;
		
		echo "<link rel=\"stylesheet\" href=\"" . $root . "styles/validation/validatorStyle.css\" type=\"text/css\">";
		echo "<script src=\"" . $root . "javascripts/validation/validatorCore.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/validation/validatorOptions.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/validation/runValidator.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/validation/formErrors.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a life updater script
	function liveSubmit() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/liveSubmit/submitterCore.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/liveSubmit/runSubmitter.js\" type=\"text/javascript\"></script>";
	}
	
	//Include the custom checkbox script
	function customVisible() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/customCheckbox/checkboxCore.js\" type=\"text/javascript\"></script><script src=\"" . $root . "javascripts/customCheckbox/runVisible.js\" type=\"text/javascript\"></script>";
	}
	
	function customCheckbox() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/customCheckbox/checkboxCore.js\" type=\"text/javascript\"></script><script src=\"" . $root . "javascripts/customCheckbox/runCheckbox.js\" type=\"text/javascript\"></script>";
	}
	
	//Include live error script
	function liveError() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/liveError/errorCore.js\" type=\"text/javascript\"></script><script src=\"" . $root . "javascripts/liveError/runNameError.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a show or hide script
	function showHide() {
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/common/showHide.js\" type=\"text/javascript\"></script>";
	}
	
	//Include an enable/disable script
	function enableDisable() {
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/common/enableDisable.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a navigation menu style
	function navigationMenu() {
		global $root;
		
		echo "<link rel=\"stylesheet\" href=\"" . $root . "styles/common/navigationMenu.css\" type=\"text/css\">";
	}
	
	//Include a new object script
	function newObject() {
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/common/newObject.js\" type=\"text/javascript\"></script>";
	}
/* End page scripting functions */
	
/* Begin form visual functions */		
	//Insert an error window, which will report errors live
	function errorWindow($type, $message, $phpGet = false, $phpError = false, $liveError = false) {
		global $connDBA;
		global $root;
		
		if ($type == "database") {
			if ($liveError == true) {
				if (isset($_GET[$phpGet]) && $_GET[$phpGet] == $phpError) {
						echo "<div align=\"center\" id=\"errorWindow\">" . errorMessage($message) . "</div>";
				} else {
					echo "<div align=\"center\" id=\"errorWindow\"><p>&nbsp;</p></div>";
				}
			} else {
				if ($_GET[$phpGet] == $phpError) {
						echo errorMessage($message);
				} else {
					echo "<p>&nbsp;</p>";
				}
			}
		}
		
		if ($type == "extension") {
			echo "<div align=\"center\"><div id=\"errorWindow\" class=\"error\" style=\"display:none;\">" .$message . "</div></div>";
		}
	}
	
	//If the user is editing the lesson, display a different series of numbering
	function step ($content, $class, $sessionClass, $first = false, $last = false) {
		global $connDBA;
		global $root;
		
		if (isset ($_SESSION['review'])) {
			catDivider($content, $sessionClass, $first, $last);
		} else {
			catDivider($content, $class, $first, $last);
		}
	}
	
	//Submit a form and toggle the tinyMCE to save its content
	function submit($id, $value) {
		global $connDBA;
		global $root;
		
		echo "<input type=\"submit\" name=\"" . $id . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"tinyMCE.triggerSave();\" />";
	}
	
	//Insert a form errors box, which will report any form errors on submit
	function formErrors () {
		global $connDBA;
		global $root;
		
		echo "<div id=\"errorBox\" style=\"display:none;\">Some fields are incomplete, please scroll up to correct them.</div><div id=\"progress\" style=\"display:none;\"><p><span class=\"require\">Uploading in progress... </span><img src=\"" . $root . "images/common/loading.gif\" alt=\"Uploading\" width=\"16\" height=\"16\" /></p></div>";
	}
/* End form visual functions */
	
/* Begin system functions */
	//Generate a random string
	function randomValue($length = 8, $seeds = 'alphanum') {
		global $connDBA;
		global $root;
		
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
		global $connDBA;
		global $root;
		
	   $commentsStrip = preg_replace("/<img[^>]+\>/i", "(image)", $value);
	   $comments = strip_tags($commentsStrip);
	   $maxLength = $length;
	   $countValue = html_entity_decode($comments);
	   if (strlen($countValue) <= $maxLength) {
		  return stripslashes($comments);
	   }
	
	   $shortenedValue = substr($countValue, 0, $maxLength - 3) . "...";
	   return $shortenedValue;
	}
	
	//A function to prepare to display values from a database
	function prepare($item, $htmlEncode = false, $stripSlashes = true) {
		if ($stripSlashes == true) {
			if ($htmlEncode == true) {
				return htmlentities(stripslashes($item));
			} else {
				return stripslashes($item);
			}
		} else {
			if ($htmlEncode == true) {
				return htmlentities($item);
			} else {
				return $item;
			}
		}
	}
	
	//A function to check the extension of a file
	function extension ($targetFile) {
		$entension = explode(".", $targetFile);
		$value = count($entension)-1;
		$entension = $entension[$value];
		$output = strtolower($entension);
		
		if($output == "php" || $output == "php3" || $output == "php4" || $output == "php5" || $output == "tpl" || $output == "php-dist" || $output == "phtml" || $output == "htaccess" || $output == "htpassword") {
			die(errorMessage("Your file is a potential threat to this system, in which case, it was not uploaded"));
			return false;
			exit;
		} else {
			return $output;
		}
	}
	
	//A function to delete a folder and all of its contents
	function deleteAll($directory, $empty = false) {
		if(substr($directory,-1) == "/") {
			$directory = substr($directory,0,-1);
		}
	
		if(!file_exists($directory) || !is_dir($directory)) {
			return false;
		} elseif(!is_readable($directory)) {
			return false;
		} else {
			$directoryHandle = opendir($directory);
			
			while ($contents = readdir($directoryHandle)) {
				if($contents != '.' && $contents != '..') {
					$path = $directory . "/" . $contents;
					
					if(is_dir($path)) {
						deleteAll($path);
					} else {
						unlink($path);
					}
				}
			}
			
			closedir($directoryHandle);
	
			if($empty == false) {
				if(!rmdir($directory)) {
					return false;
				}
			}
			
			return true;
		}
	}
	
	//A function to grab grab the previous item in the database
	function lastItem($table) {
		global $connDBA;
		
		$lastItemGrabber = mysql_query("SELECT * FROM {$table} ORDER BY position DESC", $connDBA);
		$lastItem = mysql_fetch_array($lastItemGrabber);
		return $lastItem['position'] + 1;
	}
	
	//A function to return the mime type of a file
	function getMimeType($filename, $debug = false) {
		if ( function_exists( 'finfo_open' ) && function_exists( 'finfo_file' ) && function_exists( 'finfo_close' ) ) {
			$fileinfo = finfo_open( FILEINFO_MIME );
			$mime_type = finfo_file( $fileinfo, $filename );
			finfo_close( $fileinfo );
			
			if ( ! empty( $mime_type ) ) {
				if ( true === $debug )
					return array( 'mime_type' => $mime_type, 'method' => 'fileinfo' );
				return $mime_type;
			}
		}
		if ( function_exists( 'mime_content_type' ) ) {
			$mime_type = mime_content_type( $filename );
			
			if ( ! empty( $mime_type ) ) {
				if ( true === $debug )
					return array( 'mime_type' => $mime_type, 'method' => 'mime_content_type' );
				return $mime_type;
			}
		}
		
		$mime_types = array(
			'ai'      => 'application/postscript',
			'aif'     => 'audio/x-aiff',
			'aifc'    => 'audio/x-aiff',
			'aiff'    => 'audio/x-aiff',
			'asc'     => 'text/plain',
			'asf'     => 'video/x-ms-asf',
			'asx'     => 'video/x-ms-asf',
			'au'      => 'audio/basic',
			'avi'     => 'video/x-msvideo',
			'bcpio'   => 'application/x-bcpio',
			'bin'     => 'application/octet-stream',
			'bmp'     => 'image/bmp',
			'bz2'     => 'application/x-bzip2',
			'cdf'     => 'application/x-netcdf',
			'chrt'    => 'application/x-kchart',
			'class'   => 'application/octet-stream',
			'cpio'    => 'application/x-cpio',
			'cpt'     => 'application/mac-compactpro',
			'csh'     => 'application/x-csh',
			'css'     => 'text/css',
			'dcr'     => 'application/x-director',
			'dir'     => 'application/x-director',
			'djv'     => 'image/vnd.djvu',
			'djvu'    => 'image/vnd.djvu',
			'dll'     => 'application/octet-stream',
			'dms'     => 'application/octet-stream',
			'dvi'     => 'application/x-dvi',
			'dxr'     => 'application/x-director',
			'eps'     => 'application/postscript',
			'etx'     => 'text/x-setext',
			'exe'     => 'application/octet-stream',
			'ez'      => 'application/andrew-inset',
			'flv'     => 'video/x-flv',
			'gif'     => 'image/gif',
			'gtar'    => 'application/x-gtar',
			'gz'      => 'application/x-gzip',
			'hdf'     => 'application/x-hdf',
			'hqx'     => 'application/mac-binhex40',
			'htm'     => 'text/html',
			'html'    => 'text/html',
			'ice'     => 'x-conference/x-cooltalk',
			'ief'     => 'image/ief',
			'iges'    => 'model/iges',
			'igs'     => 'model/iges',
			'img'     => 'application/octet-stream',
			'iso'     => 'application/octet-stream',
			'jad'     => 'text/vnd.sun.j2me.app-descriptor',
			'jar'     => 'application/x-java-archive',
			'jnlp'    => 'application/x-java-jnlp-file',
			'jpe'     => 'image/jpeg',
			'jpeg'    => 'image/jpeg',
			'jpg'     => 'image/jpeg',
			'js'      => 'application/x-javascript',
			'kar'     => 'audio/midi',
			'kil'     => 'application/x-killustrator',
			'kpr'     => 'application/x-kpresenter',
			'kpt'     => 'application/x-kpresenter',
			'ksp'     => 'application/x-kspread',
			'kwd'     => 'application/x-kword',
			'kwt'     => 'application/x-kword',
			'latex'   => 'application/x-latex',
			'lha'     => 'application/octet-stream',
			'lzh'     => 'application/octet-stream',
			'm3u'     => 'audio/x-mpegurl',
			'man'     => 'application/x-troff-man',
			'me'      => 'application/x-troff-me',
			'mesh'    => 'model/mesh',
			'mid'     => 'audio/midi',
			'midi'    => 'audio/midi',
			'mif'     => 'application/vnd.mif',
			'mov'     => 'video/quicktime',
			'movie'   => 'video/x-sgi-movie',
			'mp2'     => 'audio/mpeg',
			'mp3'     => 'audio/mpeg',
			'mp4'     => 'video/mp4',
			'mpe'     => 'video/mpeg',
			'mpeg'    => 'video/mpeg',
			'mpg'     => 'video/mpeg',
			'mpga'    => 'audio/mpeg',
			'ms'      => 'application/x-troff-ms',
			'msh'     => 'model/mesh',
			'mxu'     => 'video/vnd.mpegurl',
			'nc'      => 'application/x-netcdf',
			'odb'     => 'application/vnd.oasis.opendocument.database',
			'odc'     => 'application/vnd.oasis.opendocument.chart',
			'odf'     => 'application/vnd.oasis.opendocument.formula',
			'odg'     => 'application/vnd.oasis.opendocument.graphics',
			'odi'     => 'application/vnd.oasis.opendocument.image',
			'odm'     => 'application/vnd.oasis.opendocument.text-master',
			'odp'     => 'application/vnd.oasis.opendocument.presentation',
			'ods'     => 'application/vnd.oasis.opendocument.spreadsheet',
			'odt'     => 'application/vnd.oasis.opendocument.text',
			'ogg'     => 'application/ogg',
			'otg'     => 'application/vnd.oasis.opendocument.graphics-template',
			'oth'     => 'application/vnd.oasis.opendocument.text-web',
			'otp'     => 'application/vnd.oasis.opendocument.presentation-template',
			'ots'     => 'application/vnd.oasis.opendocument.spreadsheet-template',
			'ott'     => 'application/vnd.oasis.opendocument.text-template',
			'pbm'     => 'image/x-portable-bitmap',
			'pdb'     => 'chemical/x-pdb',
			'pdf'     => 'application/pdf',
			'pgm'     => 'image/x-portable-graymap',
			'pgn'     => 'application/x-chess-pgn',
			'png'     => 'image/png',
			'pnm'     => 'image/x-portable-anymap',
			'ppm'     => 'image/x-portable-pixmap',
			'ps'      => 'application/postscript',
			'qt'      => 'video/quicktime',
			'ra'      => 'audio/x-realaudio',
			'ram'     => 'audio/x-pn-realaudio',
			'ras'     => 'image/x-cmu-raster',
			'rgb'     => 'image/x-rgb',
			'rm'      => 'audio/x-pn-realaudio',
			'roff'    => 'application/x-troff',
			'rpm'     => 'application/x-rpm',
			'rtf'     => 'text/rtf',
			'rtx'     => 'text/richtext',
			'sgm'     => 'text/sgml',
			'sgml'    => 'text/sgml',
			'sh'      => 'application/x-sh',
			'shar'    => 'application/x-shar',
			'silo'    => 'model/mesh',
			'sis'     => 'application/vnd.symbian.install',
			'sit'     => 'application/x-stuffit',
			'skd'     => 'application/x-koan',
			'skm'     => 'application/x-koan',
			'skp'     => 'application/x-koan',
			'skt'     => 'application/x-koan',
			'smi'     => 'application/smil',
			'smil'    => 'application/smil',
			'snd'     => 'audio/basic',
			'so'      => 'application/octet-stream',
			'spl'     => 'application/x-futuresplash',
			'src'     => 'application/x-wais-source',
			'stc'     => 'application/vnd.sun.xml.calc.template',
			'std'     => 'application/vnd.sun.xml.draw.template',
			'sti'     => 'application/vnd.sun.xml.impress.template',
			'stw'     => 'application/vnd.sun.xml.writer.template',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc'  => 'application/x-sv4crc',
			'swf'     => 'application/x-shockwave-flash',
			'sxc'     => 'application/vnd.sun.xml.calc',
			'sxd'     => 'application/vnd.sun.xml.draw',
			'sxg'     => 'application/vnd.sun.xml.writer.global',
			'sxi'     => 'application/vnd.sun.xml.impress',
			'sxm'     => 'application/vnd.sun.xml.math',
			'sxw'     => 'application/vnd.sun.xml.writer',
			't'       => 'application/x-troff',
			'tar'     => 'application/x-tar',
			'tcl'     => 'application/x-tcl',
			'tex'     => 'application/x-tex',
			'texi'    => 'application/x-texinfo',
			'texinfo' => 'application/x-texinfo',
			'tgz'     => 'application/x-gzip',
			'tif'     => 'image/tiff',
			'tiff'    => 'image/tiff',
			'torrent' => 'application/x-bittorrent',
			'tr'      => 'application/x-troff',
			'tsv'     => 'text/tab-separated-values',
			'txt'     => 'text/plain',
			'ustar'   => 'application/x-ustar',
			'vcd'     => 'application/x-cdlink',
			'vrml'    => 'model/vrml',
			'wav'     => 'audio/x-wav',
			'wax'     => 'audio/x-ms-wax',
			'wbmp'    => 'image/vnd.wap.wbmp',
			'wbxml'   => 'application/vnd.wap.wbxml',
			'wm'      => 'video/x-ms-wm',
			'wma'     => 'audio/x-ms-wma',
			'wml'     => 'text/vnd.wap.wml',
			'wmlc'    => 'application/vnd.wap.wmlc',
			'wmls'    => 'text/vnd.wap.wmlscript',
			'wmlsc'   => 'application/vnd.wap.wmlscriptc',
			'wmv'     => 'video/x-ms-wmv',
			'wmx'     => 'video/x-ms-wmx',
			'wrl'     => 'model/vrml',
			'wvx'     => 'video/x-ms-wvx',
			'xbm'     => 'image/x-xbitmap',
			'xht'     => 'application/xhtml+xml',
			'xhtml'   => 'application/xhtml+xml',
			'xml'     => 'text/xml',
			'xpm'     => 'image/x-xpixmap',
			'xsl'     => 'text/xml',
			'xwd'     => 'image/x-xwindowdump',
			'xyz'     => 'chemical/x-xyz',
			'zip'     => 'application/zip',
			'doc'     => 'application/msword',
			'dot'     => 'application/msword',
			'docx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'dotx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'docm'    => 'application/vnd.ms-word.document.macroEnabled.12',
			'dotm'    => 'application/vnd.ms-word.template.macroEnabled.12',
			'xls'     => 'application/vnd.ms-excel',
			'xlt'     => 'application/vnd.ms-excel',
			'xla'     => 'application/vnd.ms-excel',
			'xlsx'    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xltx'    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'xlsm'    => 'application/vnd.ms-excel.sheet.macroEnabled.12',
			'xltm'    => 'application/vnd.ms-excel.template.macroEnabled.12',
			'xlam'    => 'application/vnd.ms-excel.addin.macroEnabled.12',
			'xlsb'    => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'ppt'     => 'application/vnd.ms-powerpoint',
			'pot'     => 'application/vnd.ms-powerpoint',
			'pps'     => 'application/vnd.ms-powerpoint',
			'ppa'     => 'application/vnd.ms-powerpoint',
			'pptx'    => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'potx'    => 'application/vnd.openxmlformats-officedocument.presentationml.template',
			'ppsx'    => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'ppam'    => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'pptm'    => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'potm'    => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'ppsm'    => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12'
		);
		
		$ext = strtolower( array_pop( explode( '.', $filename ) ) );
		
		if ( ! empty( $mime_types[$ext] ) ) {
			if ( true === $debug )
				return array( 'mime_type' => $mime_types[$ext], 'method' => 'from_array' );
			return $mime_types[$ext];
		}
		
		if ( true === $debug )
			return array( 'mime_type' => 'application/octet-stream', 'method' => 'last_resort' );
		return 'application/octet-stream';
	}
	
	//A function to montior access to the module generator
	function monitor($title, $functions = false) {
		global $connDBA, $strippedRoot;
		
		$titlePrefix = "Module Setup Wizard : ";
		
		if ($_SERVER['PHP_SELF'] != $strippedRoot . "site_administrator/modules/module_wizard/index.php") {
			if (isset ($_SESSION['step'])) {
				switch ($_SESSION['step']) {
					case "lessonSettings" : $redirect = "lesson_settings.php"; break;
					case "lessonContent" : $redirect = "lesson_content.php"; break;
					case "lessonVerify" : $redirect = "lesson_verify.php"; break;
					case "testCheck" : $redirect = "test_check.php"; break;
					case "testSettings" : $redirect = "test_settings.php"; break;
					case "testContent" : $redirect = "test_content.php"; break;
					case "testVerify" : $redirect = "test_verify.php"; break;
				}
			} elseif (isset ($_SESSION['review'])) {
				$id = $_SESSION['currentModule'];
				$testCheckGrabber = mysql_query("SELECT * FROM `moduledata` WHERE `id` = '{$id}'", $connDBA);
				$testCheckArray = mysql_fetch_array($testCheckGrabber);
				
				if ($testCheckArray['test'] == "0") {
					header ("Location: " . $root . "site_administrator/modules/module_wizard/test_check.php");
					exit;
				}
			} else {
				header ("Location: " . $root . "site_administrator/modules/module_wizard/index.php");
				exit;
			}
		}
		
		headers($titlePrefix . $title, "Site Administrator", $functions, true);
		$parentTable = "moduledata";
		$prefix = "";
		
		if (isset($_SESSION['currentModule'])) {
			$lessonTable = $prefix . "modulelesson" . "_" . $_SESSION['currentModule'];
			$testTable = $prefix . "moduletest" . "_" . $_SESSION['currentModule'];
			$directory = "../../../modules/" . $_SESSION['currentModule'] . "/";
			$gatewayPath = "../../../gateway.php/modules/" . $_SESSION['currentModule'] . "/";
			$redirect = "../module_wizard/test_content.php";
			$type = "Module";
			
			if (isset($_SESSION['currentModule'])) {
				$currentModule = $_SESSION['currentModule'];
				$currentTable = $_SESSION['currentModule'];
			} else {
				$currentModule = "";
				$currentTable = "";
			}
			
			$monitor = array("parentTable" => $parentTable, "lessonTable" => $lessonTable, "testTable" => $testTable, "prefix" => $prefix, "directory" => $directory, "gatewayPath" => $gatewayPath, "currentModule" => $currentModule, "currentTable" => $currentTable, "title" => $titlePrefix, "redirect" => $redirect, "type" => $type);
		
			return $monitor;
		} else {
			$directory = "../../../modules/";
			
			$monitor = array("parentTable" => $parentTable, "title" => $titlePrefix, "prefix" => $prefix, "directory" => $directory);
			
			return $monitor;
		}
	}
	
	//A function to keep track of steps in a module
	function navigation($title, $text) {
		global $connDBA, $monitor;
		
		echo "<div class=\"layoutControl\"><div class=\"contentLeft\">";
		title($monitor['title'] . $title, $text);
		echo "</div><div class=\"dataRight\" style=\"padding-top:15px;\">";
		
		if (isset($_SESSION['currentModule'])) {
			$id = $_SESSION['currentModule'];
			$moduleDataTestGrabber = mysql_query("SELECT * FROM `{$monitor['parentTable']}` WHERE id = '{$id}'", $connDBA);
			$moduleDataTest = mysql_fetch_array($moduleDataTestGrabber);
		}
		
		echo "<ul id=\"navigationmenu\"><li class=\"toplast\"><a href=\"#\"><span>Navigation</span></a><ul><li>";
		
		if ($moduleDataTestGrabber && !empty($moduleDataTest['name'])) {
			echo "<li>" . URL("Lesson Settings", "lesson_settings.php", "complete") . "</li>";
		} else {
			echo "<li>" . URL("Lesson Settings", "lesson_settings.php", "incomplete") . "</li>";
		}
		
		if ($moduleDataTestGrabber && !empty($moduleDataTest['name'])) {
			echo "<li>" . URL("Lesson Content", "lesson_content.php", "complete") . "</li>";
		}
				
		if (exist($monitor['lessonTable']) == true) {
			echo "<li>" . URL("Verify Lesson", "lesson_verify.php", "complete") . "</li>";
		}
		
		if ($moduleDataTestGrabber && exist($monitor['lessonTable']) == true && $moduleDataTest['test'] == "0") {
			echo "<li>" . URL("Add Test", "test_check.php", "incomplete") . "</li>";
		} elseif ($moduleDataTestGrabber && exist($monitor['lessonTable']) == true && $moduleDataTest['test'] == "1") {
			if ($moduleDataTestGrabber && $moduleDataTest['test'] == "1") {
				echo "<li>" . URL("Test Settings", "test_settings.php", "complete") . "</li>";
			}
			
			if ($moduleDataTestGrabber && !empty($moduleDataTest['testName'])) {
				echo "<li>" . URL("Test Content", "test_content.php", "complete") . "</li>";
			}
			
			if (exist($monitor['testTable']) == true) {
				echo "<li>" . URL("Verify Test", "test_verify.php", "complete") . "</li>";
			}
			
			if ($moduleDataTestGrabber && !empty($moduleDataTest['name']) && exist($monitor['lessonTable']) == true && $moduleDataTest['test'] == "1" && !empty($moduleDataTest['testName']) && exist($monitor['testTable']) == true) {
				echo "<li>" . URL("Complete", "complete.php", "complete") . "</li>";
			}
		}
		
		echo "</ul></li></ul></div></div>";

	}
	
	//A function to regulate the how questions are inserted and updated
	function insertQuery($type, $moduleQuery, $bankQuery = false, $feedbackQuery = false) {
		global $connDBA, $monitor;
		
		switch ($type) {
			case "Module" :
				mysql_query("INSERT INTO `{$monitor['testTable']}` (
							`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
							) VALUES (
							{$moduleQuery}
							)", $connDBA);
				break;
				
		}
	}
	
	function updateQuery($type, $moduleQuery, $bankQuery = false, $feedbackQuery = false) {
		global $connDBA, $monitor;
		
		$update = $_GET['id'];
		
		switch ($type) {
			case "Module" :
				mysql_query("UPDATE `{$monitor['testTable']}` SET {$moduleQuery} WHERE `id` = '{$update}'", $connDBA);
				break;
				
		}
		
	}
	
	//Live check if data exists
	function checkName($label, $table, $column) {
		global $connDBA;
		
		if (isset($_GET['checkName'])) {
			$inputNameSpaces = $_GET['checkName'];
			$inputNameNoSpaces = str_replace(" ", "", $_GET['checkName']);
			$checkName = mysql_query("SELECT * FROM `{$table}` WHERE `{$column}` = '{$inputNameSpaces}'", $connDBA);
			
			if ($name = mysql_fetch_array($checkName)) {					
				if (isset($_SESSION['currentModule'])) {
					if (strtolower($name['name']) != strtolower($_SESSION['currentModule'])) {
						echo "<div class=\"error\" id=\"errorWindow\">A " . $label . " with this name already exists</div>";
					} else {
						echo "<p>&nbsp;</p>";
					}
				} else {
					echo "<div class=\"error\" id=\"errorWindow\">A " . $label . " with this name already exists</div>";
				}
			} else {
				echo "<p>&nbsp;</p>";
			}
			
			echo "<script type=\"text/javascript\">validateName()</script>";
			die();
		}
	}
/* End system functions */
	
/* Begin statistics tracker */
	//Set the activity meter
	function activity($setActivity = "false") {
		global $root;
		global $connDBA;
		
		if ($setActivity == "true" && isset($_SESSION['MM_Username'])) {
			$userName = $_SESSION['MM_Username'];
			$activityTimestamp = time();
			mysql_query("UPDATE `users` SET `active` = '{$activityTimestamp}' WHERE `userName` = '{$userName}' LIMIT 1", $connDBA);
		}
	}
	
	//Overall statistics
	function stats($doAction = "false") {
		global $root;
		global $connDBA;
		
		if ($doAction == "true") {
			$date = date("M-d-Y");
			$statisticsCheck = mysql_query("SELECT * FROM `overallstatistics` WHERE `date` = '{$date}' LIMIT 1", $connDBA);
			if ($result = mysql_fetch_array($statisticsCheck)) {
				$newHit = $result['hits']+1;
				mysql_query("UPDATE `overallstatistics` SET `hits` = '{$newHit}' WHERE `date` = '{$date}' LIMIT 1", $connDBA);
			} else {
				mysql_query("INSERT INTO `overallstatistics` (
							`id`, `date`, `hits`
							) VALUES (
							NULL, '{$date}', '1'
							)");
			}
		}
	}
/* End statistics tracker */
?>
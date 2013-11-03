<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
	if (isset ($_SESSION['step']) && !isset ($_SESSION['review'])) {
		switch ($_SESSION['step']) {
			case "lessonSettings" : header ("Location: lesson_settings.php"); exit; break;
			case "lessonContent" : header ("Location: lesson_content.php"); exit; break;
			case "lessonVerify" : header ("Location: lesson_verify.php"); exit; break;
			case "testCheck" : header ("Location: test_check.php"); exit; break;
			//case "testSettings" : header ("Location: test_settings.php"); exit; break;
			case "testContent" : header ("Location: test_content.php"); exit; break;
			case "testVerify" : header ("Location: test_verify.php"); exit; break;
		}
	} elseif (isset ($_SESSION['review'])) {
	//Check to see if a test is set to be created, otherwise allow access to this page
		$name = $_SESSION['currentModule'];
		$testCheckGrabber = mysql_query("SELECT * FROM moduleData WHERE `name` = '{$name}'", $connDBA);
		$testCheckArray = mysql_fetch_array($testCheckGrabber);
		
		if ($testCheckArray['test'] == "0") {
			header ("Location: test_check.php");
			exit;
		}
	} else {
		header ("Location: ../index.php");
		exit;
	}
?>
<?php
//Detect whether or not the module is being edited
	if (isset($_SESSION['currentModule'])) {
		$name = $_SESSION['currentModule'];
		$testDataGrabber = mysql_query("SELECT * FROM moduledata WHERE name = '{$name}'", $connDBA);
		$testData = mysql_fetch_array($testDataGrabber);
	}
	
//Select the module name, to fill in for the test name field
	$currentModule = $_SESSION['currentModule'];
	$testNameGrabber = mysql_query ("SELECT * FROM moduledata WHERE name = '{$currentModule}'", $connDBA);
	$testName = mysql_fetch_array($testNameGrabber);
?>
<?php
//Process the form
	if (isset($_POST['submit'])) {
	//Use the session to find where to insert the test data
		$currentModule = $_SESSION['currentModule'];
		
	//Check to see if the timer is set and if the time does not equal zero
		if (isset($_POST['timer']) && isset($_POST['timeHours']) && isset($_POST['timeMinutes'])) {
			if ($_POST['timer'] == "on" && $_POST['timeHours'] == "0" && $_POST['timeMinutes'] == "00") {
				$timeValue = serialize("1");
				$timerValue = "0";
			} else {
			//Convert the time values to an array	
				$timeHours = $_POST['timeHours'];
				$timeMinutes = $_POST['timeMinutes'];
				$timeValue = serialize(array($timeHours, $timeMinutes));
				$timerValue = "on";
			}
		} else {
			$timeValue = serialize("1");
			$timerValue = "0";
		}
		
	//Convert the attempts to a numerical value
		switch ($_POST['attempts']) {
			case "unlimited" : $covertedAttempts = "999"; break;
			case "one" : $covertedAttempts = "1"; break;
			case "two" : $covertedAttempts = "2"; break;
			case "three" : $covertedAttempts = "3"; break;
			case "four" : $covertedAttempts = "4"; break;
			case "five" : $covertedAttempts = "5"; break;
			case "six" : $covertedAttempts = "6"; break;
			case "seven" : $covertedAttempts = "7"; break;
			case "eight" : $covertedAttempts = "8"; break;
			case "nine" : $covertedAttempts = "9"; break;
			case "ten" : $covertedAttempts = "10"; break;
		}
		
		$testName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['testName']));
		$directions = mysql_real_escape_string($_POST['directions']);
		$score = $_POST['score'];
		$attempts = $covertedAttempts;
		$forceCompletion = $_POST['forceCompletion'];
		$completionMethod = $_POST['completionMethod'];
		$delay = $_POST['delay'];
		$gradingMethod = $_POST['gradingMethod'];
		$penalties = $_POST['penalties'];
		$time = $timeValue;
		$timer = $timerValue;
		$randomizeAll = $_POST['randomizeAll'];
		$randomizeQuestions = $_POST['randomizeQuestions'];
		$display = serialize($_POST['display']);
		
		$addToModuleQuery = "UPDATE moduledata SET 
							testName = '{$testName}',
							directions = '{$directions}',
							score = '{$score}',
							attempts = '{$attempts}',
							forceCompletion = '{$forceCompletion}',
							completionMethod = '{$completionMethod}',
							delay = '{$delay}',
							gradingMethod = '{$gradingMethod}',
							penalties = '{$penalties}',
							time = '{$time}',
							timer = '{$timer}',
							randomizeAll = '{$randomizeAll}',
							randomizeQuestions = '{$randomizeQuestions}',
							display = '{$display}'
							WHERE name = '{$currentModule}'";
		
		//Execute command on database			
			mysql_query($addToModuleQuery, $connDBA);
			
		//Update the session to manage the steps
			$_SESSION['step'] = "testContent";	
			
		if (isset ($_SESSION['review'])) {
			header ("Location: modify.php?updated=testSettings");
			exit;
		} else {
			header ("Location: test_content.php");
			exit;	
		}
	}
?>
<?php
//Update a session to go to previous steps
	if (isset ($_GET['goTo']) && $_GET['goTo'] == "previous") {
		$_SESSION['step'] = "lessonVerify";
		header ("Location: lesson_verify.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Test Settings"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../javascripts/common/enableDisable.js" type="text/javascript"></script>
<script src="../../../javascripts/common/showHide.js" type="text/javascript"></script>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>      
    <h2>Module Setup Wizard : Test Settings</h2>
    <p>Setup the test's initial settings, such as the name, directions, and score.</p>
<p>&nbsp;</p>
    <form name="testSettings" action="test_settings.php" method="post" id="validate" onsubmit="return errorsOnSubmit(this);">
    <div class="catDivider">
    <?php
		step("8", "Test Information", "1" , "Test Information")
	?>
    </div>
    <div class="stepContent">
    <blockquote>
      <p>Test Name: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The name of this test')" onmouseout="UnTip()" /></p>
      <blockquote>
        <p>
          <label>
          <input name="testName" type="text" id="testName" size="50" class="validate[required,custom[onlyLetter]]"<?php
			//If the module is being edited
				if (isset($_SESSION['review'])) {
					if ($testName['testName'] == "T") {
						echo " value=\"" . stripslashes($testName['name']) . "\"";
					} else {
						echo " value=\"" . stripslashes($testName['testName']) . "\"";
					}
				} else {
					if ($testName['testName'] == "T") {
						echo " value=\"" . stripslashes($testName['name']) . "\"";
					} else {
						echo " value=\"" . stripslashes($testName['testName']) . "\"";
					}
				}
			?> />
          </label>
        </p>
      </blockquote>
      <p>Directions<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The directions of this test')" onmouseout="UnTip()" /></p>
      <blockquote>
        <p><span id="directionsCheck">
          <label>
          <textarea name="directions" id="directions" cols="45" rows="5" style="font-family:Arial, Helvetica, sans-serif; width:450px;"><?php
			//If the module is being edited
				if ($testName['testName'] !== "T") {
					echo stripslashes($testData['directions']);
				}
			?></textarea>
          </label>
        <span class="textareaRequiredMsg"></span></span></p>
      </blockquote>
      <p>&nbsp;</p>
    </blockquote>
    </div>
    <div class="catDivider">
    <?php
		step("9", "Test settings", "2" , "Test Settings")
	?>
    </div>
    <div class="stepContent">
    <blockquote>
      <p>Passing Score: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The minimum score a student must obtain to pass')" onmouseout="UnTip()" /></p>
      <blockquote>
        <p>
        <?php
		//Dispay the score drop-down menu
			if ($testData['testName'] == "T") {
				echo "<select name=\"score\">";
				for ($count = 0; $count <= 100; $count++) {
					echo "<option value=\"" . $count . "\"";
					if ($count == 80) {
						echo " selected=\"selected\"";
					}
					echo ">" . $count . "</option>";
				}
				echo "</select>";
			} else {
				echo "<select name=\"score\">";
				for ($count = 0; $count <= 100; $count++) {
					echo "<option value=\"" . $count . "\"";
					if ($testData['score'] == $count) {
						echo " selected=\"selected\"";
					}
					echo ">" . $count . "</option>";
				}
				echo "</select>";
			}
		?>  
        %</p>
      </blockquote>
      <p>Number of Attempts: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The number of attempts a student may make on this test')" onmouseout="UnTip()" /></p>
      <blockquote>
        <p>
        <select name="attempts" onchange="toggleNumericalDiv(this.value);">
        	<option value="unlimited"<?php if ($testData['testName'] !== "T") {if ($testData ['attempts'] == "999") {echo " selected=\"selected\"";}} ?>>Unlimited</option>
            <option value="one"<?php if ($testData['testName'] !== "T") {if ($testData ['attempts'] == "1") {echo " selected=\"selected\"";}} else {echo " selected=\"selected\"";} ?>>1</option>
            <option value="two"<?php if ($testData['testName'] !== "T") {if ($testData ['attempts'] == "2") {echo " selected=\"selected\"";}} ?>>2</option>
            <option value="three"<?php if ($testData['testName'] !== "T") {if ($testData ['attempts'] == "3") {echo " selected=\"selected\"";}} ?>>3</option>
            <option value="four"<?php if ($testData['testName'] !== "T") {if ($testData ['attempts'] == "4") {echo " selected=\"selected\"";}} ?>>4</option>
            <option value="five"<?php if ($testData['testName'] !== "T") {if ($testData ['attempts'] == "5") {echo " selected=\"selected\"";}} ?>>5</option>
            <option value="six"<?php if ($testData['testName'] !== "T") {if ($testData ['attempts'] == "6") {echo " selected=\"selected\"";}} ?>>6</option>
            <option value="seven"<?php if ($testData['testName'] !== "T") {if ($testData ['attempts'] == "7") {echo " selected=\"selected\"";}} ?>>7</option>
            <option value="eight"<?php if ($testData['testName'] !== "T") {if ($testData ['attempts'] == "8") {echo " selected=\"selected\"";}} ?>>8</option>
            <option value="nine"<?php if ($testData['testName'] !== "T") {if ($testData ['attempts'] == "9") {echo " selected=\"selected\"";}} ?>>9</option>
            <option value="ten"<?php if ($testData['testName'] !== "T") {if ($testData ['attempts'] == "10") {echo " selected=\"selected\"";}} ?>>10</option>
        </select> 
        </p>
      </blockquote>
      <div id="contentHide"
        <?php
		//Display or hide the advanced settings div, based on current settings
			if (isset($_SESSION['review'])) {
				if ($testData['attempts'] == "1") {
					echo " class=\"contentHide\"";
				} else {
					echo " class=\"contentShow\"";
				}
			} elseif (!isset($_SESSION['review'])) {
				if ($testData['attempts'] == "0") {
					echo " class=\"contentHide\"";
				} elseif ($testData['attempts'] == "1") {
					echo " class=\"contentHide\"";
				} else {
					echo " class=\"contentShow\"";
				}
			}
		?>
      >
      <p>Delay Between Attempts: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('If students are allowed to make multiple attempts on this test, <br />then this will set the amount of time they must wait in order to retry')" onmouseout="UnTip()" /></p>
      <blockquote>
        <p>
          <select name="delay" id="delay">
            <option value="0"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "0") {echo " selected=\"selected\"";}} else {echo " selected=\"selected\"";} ?>>None</option>
            <option value="1800"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "1800") {echo " selected=\"selected\"";}} ?>>30 minutes</option>
            <option value="3600"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "3600") {echo " selected=\"selected\"";}} ?>>60 minutes</option>
            <option value="7200"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "7200") {echo " selected=\"selected\"";}} ?>>2 hours</option>
            <option value="10800"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "10800") {echo " selected=\"selected\"";}} ?>>3 hours</option>
            <option value="14400"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "14400") {echo " selected=\"selected\"";}} ?>>4 hours</option>
            <option value="18000"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "18000") {echo " selected=\"selected\"";}} ?>>5 hours</option>
            <option value="21600"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "21600") {echo " selected=\"selected\"";}} ?>>6 hours</option>
            <option value="25200"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "25200") {echo " selected=\"selected\"";}} ?>>7 hours</option>
            <option value="28800"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "28800") {echo " selected=\"selected\"";}} ?>>8 hours</option>
            <option value="32400"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "32400") {echo " selected=\"selected\"";}} ?>>9 hours</option>
            <option value="36000"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "36000") {echo " selected=\"selected\"";}} ?>>10 hours</option>
            <option value="39600"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "39600") {echo " selected=\"selected\"";}} ?>>11 hours</option>
            <option value="43200"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "43200") {echo " selected=\"selected\"";}} ?>>12 hours</option>
            <option value="46800"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "46800") {echo " selected=\"selected\"";}} ?>>13 hours</option>
            <option value="50400"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "50400") {echo " selected=\"selected\"";}} ?>>14 hours</option>
            <option value="54000"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "54000") {echo " selected=\"selected\"";}} ?>>15 hours</option>
            <option value="57600"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "576000") {echo " selected=\"selected\"";}} ?>>16 hours</option>
            <option value="61200"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "61200") {echo " selected=\"selected\"";}} ?>>17 hours</option>
            <option value="64800"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "64800") {echo " selected=\"selected\"";}} ?>>18 hours</option>
            <option value="68400"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "68400") {echo " selected=\"selected\"";}} ?>>19 hours</option>
            <option value="72000"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "72000") {echo " selected=\"selected\"";}} ?>>20 hours</option>
            <option value="75600"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "75600") {echo " selected=\"selected\"";}} ?>>21 hours</option>
            <option value="79200"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "79200") {echo " selected=\"selected\"";}} ?>>22 hours</option>
            <option value="82800"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "82800") {echo " selected=\"selected\"";}} ?>>23 hours</option>
            <option value="86400"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "86400") {echo " selected=\"selected\"";}} ?>>24 hours</option>
            <option value="172800"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "172800") {echo " selected=\"selected\"";}} ?>>2 days</option>
            <option value="259200"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "259200") {echo " selected=\"selected\"";}} ?>>3 days</option>
            <option value="345600"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "345600") {echo " selected=\"selected\"";}} ?>>4 days</option>
            <option value="432000"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "432000") {echo " selected=\"selected\"";}} ?>>5 days</option>
            <option value="518400"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "518400") {echo " selected=\"selected\"";}} ?>>6 days</option>
            <option value="604800"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['delay'] == "604800") {echo " selected=\"selected\"";}} ?>>7 days</option>
          </select>
        </p>
      </blockquote>
      <p>Grading Method: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('If students are allowed to make multiple attempts on this test, <br />then this will detirmine how the test will be scored')" onmouseout="UnTip()" /></p>
      <blockquote>
        <p>
          <select name="gradingMethod" id="gradingMethod">
            <option value="Highest Grade"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['gradingMethod'] == "Highest Grade") {echo " selected=\"selected\"";}} else {echo " selected=\"selected\"";} ?>>Highest Grade</option>
            <option value="Average Grade"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['gradingMethod'] == "Average Grade") {echo " selected=\"selected\"";}} ?>>Average Grade</option>
            <option value="First Attempt"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['gradingMethod'] == "First Attempt") {echo " selected=\"selected\"";}} ?>>First Attempt</option>
            <option value="Last Attempt"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['gradingMethod'] == "Last Attempt") {echo " selected=\"selected\"";}} ?>>Last Attempt</option>
            </select>
        </p>
      </blockquote>
      <p>Show Penalties: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('If students are allowed to make multiple attempts on this test, <br />then this will detirmine whether or not previous attempts and scores will display in the gradebook')" onmouseout="UnTip()" /></p>
      <blockquote>
        <p>
          <select name="penalties" id="penalties">
            <option value="1"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['penalties'] == "1") {echo " selected=\"selected\"";}} else {echo " selected=\"selected\"";} ?>>Yes</option>
            <option value="0"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['penalties'] == "0") {echo " selected=\"selected\"";}} ?>>No</option>
          </select>
        </p>
      </blockquote>
      </div>
      <p>Timer: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Sets a timer, which will only allow the test to be open for a set duration')" onmouseout="UnTip()" /></p>
      <?php
	  //Split the time value located in the database into hours and minutes
		if ($testData['testName'] !== "T") {
			$time = unserialize($testData['time']);
			if ($testData['time'] !== 's:1:"1";') {
				$testH = $time['0'];
				$testM = $time['1'];
			}
		}
	  ?>
      <blockquote>
        <p>
          Hours:          
          <label>
          <select name="timeHours" id="timeHours"<?php if ($testData['testName'] !== "T") {if ($testData['timer'] !== "on") {echo " disabled=\"diabled\"";}} else {echo " disabled=\"diabled\"";}?>>
            <option value="0"<?php if ($testData['time'] !== 's:1:"1";') {if ($testH == "0") {echo " selected=\"selected\"";}} else {echo " selected=\"selected\"";}?>>0</option>
            <option value="1"<?php if ($testData['time'] !== 's:1:"1";') {if ($testH == "1") {echo " selected=\"selected\"";}}?>>1</option>
            <option value="2"<?php if ($testData['time'] !== 's:1:"1";') {if ($testH == "2") {echo " selected=\"selected\"";}}?>>2</option>
            <option value="3"<?php if ($testData['time'] !== 's:1:"1";') {if ($testH == "3") {echo " selected=\"selected\"";}}?>>3</option>
            <option value="4"<?php if ($testData['time'] !== 's:1:"1";') {if ($testH == "4") {echo " selected=\"selected\"";}}?>>4</option>
            <option value="5"<?php if ($testData['time'] !== 's:1:"1";') {if ($testH == "5") {echo " selected=\"selected\"";}}?>>5</option>
          </select>
          </label>
        Minutes: 
        <label>
        <select name="timeMinutes" id="timeMinutes"<?php if ($testData['testName'] !== "T") {if ($testData['timer'] !== "on") {echo " disabled=\"diabled\"";}} else {echo " disabled=\"diabled\"";}?>>
          <option value="00"<?php if ($testData['time'] !== 's:1:"1";') {if ($testM == "00") {echo " selected=\"selected\"";}} else {echo " selected=\"selected\"";}?>>00</option>
          <option value="05"<?php if ($testData['time'] !== 's:1:"1";') {if ($testM == "05") {echo " selected=\"selected\"";}}?>>05</option>
          <option value="10"<?php if ($testData['time'] !== 's:1:"1";') {if ($testM == "10") {echo " selected=\"selected\"";}}?>>10</option>
          <option value="15"<?php if ($testData['time'] !== 's:1:"1";') {if ($testM == "15") {echo " selected=\"selected\"";}}?>>15</option>
          <option value="20"<?php if ($testData['time'] !== 's:1:"1";') {if ($testM == "20") {echo " selected=\"selected\"";}}?>>20</option>
          <option value="25"<?php if ($testData['time'] !== 's:1:"1";') {if ($testM == "25") {echo " selected=\"selected\"";}}?>>25</option>
          <option value="30"<?php if ($testData['time'] !== 's:1:"1";') {if ($testM == "30") {echo " selected=\"selected\"";}}?>>30</option>
          <option value="35"<?php if ($testData['time'] !== 's:1:"1";') {if ($testM == "35") {echo " selected=\"selected\"";}}?>>35</option>
          <option value="40"<?php if ($testData['time'] !== 's:1:"1";') {if ($testM == "40") {echo " selected=\"selected\"";}}?>>40</option>
          <option value="45"<?php if ($testData['time'] !== 's:1:"1";') {if ($testM == "45") {echo " selected=\"selected\"";}}?>>45</option>
          <option value="50"<?php if ($testData['time'] !== 's:1:"1";') {if ($testM == "50") {echo " selected=\"selected\"";}}?>>50</option>
          <option value="55"<?php if ($testData['time'] !== 's:1:"1";') {if ($testM == "55") {echo " selected=\"selected\"";}}?>>55</option>
        </select>
        </label>
        <label>
        <input name="timer" type="checkbox" id="timer" onclick="flvFTFO1('testSettings','timeHours,t','timeMinutes,t')"<?php if ($testData['testName'] !== "T") {if ($testData['timer'] == "on") {echo " checked=\"checked\"";}}?> />
        Enable</label>
</p>
      </blockquote>
      <p>Force Completion: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The test must be completed the first time it is opened, otherwise penalties will be applied')" onmouseout="UnTip()" /></p>
      <blockquote>
        <p>
        Penalties: 
        <label>
        <select name="completionMethod" id="completionMethod"<?php if ($testData['testName'] !== "T") {if ($testData['forceCompletion'] !== "on") {echo " disabled=\"diabled\"";}} else {echo " disabled=\"diabled\"";}?>>
          <option value="0"<?php if ($testData['testName'] !== "T") {if ($testData['completionMethod'] == "0") {echo " selected=\"selected\"";}} else {echo " selected=\"selected\"";}?>>The test will close</option>
          <option value="1"<?php if ($testData['testName'] !== "T") {if ($testData['completionMethod'] == "1") {echo " selected=\"selected\"";}}?>>All answers will reset</option>
          <option value="10"<?php if ($testData['testName'] !== "T") {if ($testData['completionMethod'] == "10") {echo " selected=\"selected\"";}}?>>Grade decreases 10%</option>
          <option value="20"<?php if ($testData['testName'] !== "T") {if ($testData['completionMethod'] == "20") {echo " selected=\"selected\"";}}?>>Grade decreases 20%</option>
          <option value="30"<?php if ($testData['testName'] !== "T") {if ($testData['completionMethod'] == "30") {echo " selected=\"selected\"";}}?>>Grade decreases 30%</option>
          <option value="40"<?php if ($testData['testName'] !== "T") {if ($testData['completionMethod'] == "40") {echo " selected=\"selected\"";}}?>>Grade decreases 40%</option>
        </select>
        </label>
        <label>
        <input name="forceCompletion" type="checkbox" id="forceCompletion" onclick="flvFTFO1('testSettings','completionMethod,t')"<?php if ($testData['testName'] !== "T") {if ($testData['forceCompletion'] == "on") {echo " checked=\"checked\"";}}?> /> 
        Enable</label>
</p>
      </blockquote>
      <p>Randomize Questions: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Set whether questions will display in a pre-defined order, <br/ >or if they will randomize')" onmouseout="UnTip()" /></p>
      <blockquote>
        <p>
          <select name="randomizeAll" id="randomizeAll">
            <option value="Sequential Order"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['randomizeAll'] == "Sequential Order") {echo " selected=\"selected\"";}} else {echo " selected=\"selected\"";} ?>>Sequential Order</option>
            <option value="Randomize"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['randomizeAll'] == "Randomize") {echo " selected=\"selected\"";}} ?>>Randomize</option>
          </select>
        </p>
      </blockquote>
      <p>Randomize within Questions: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('If this test includes multiple choice questions, &lt;br/ &gt;then set whether or not the items will display in order or randomized')" onmouseout="UnTip()" /></p>
      <blockquote>
        <p>
          <select name="randomizeQuestions" id="randomizeQuestions">
            <option value="Sequential Order"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['randomizeQuestions'] == "Sequential Order") {echo " selected=\"selected\"";}} else {echo " selected=\"selected\"";} ?>>Sequential Order</option>
            <option value="Randomize"<?php if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {if ($testData['randomizeQuestions'] == "Randomize") {echo " selected=\"selected\"";}} ?>>Randomize</option>
          </select>
        </p>
      </blockquote>
      <p>After the test is taken: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Select what information will be displayed when the test is completed:<br/><br/><strong>Score:</strong> Overall score of the test<br/><strong>Selected Answers:</strong> The answer(s) the user user selected in the test<br/><strong>Correct Answers:</strong> The correct answer(s) for each problem<br/><strong>Feedback:</strong> The comments the user will recieve based off their answer</li>')" onmouseout="UnTip()" /></p>
      <blockquote>
      <?php
	  //Decompile the serialized array to see what boxes needs to be checked
	  		$values = unserialize($testData['display']);
			
			if (isset ($values['0'])) {
				switch ($values['0']) {
					case "1" : $firstValue = "1"; break;
					case "2" : $firstValue = "2"; break;
					case "3" : $firstValue = "3"; break;
					case "4" : $firstValue = "4"; break;
				}
			}
			
			if (isset ($values['1'])) {
				switch ($values['1']) {
					case "2" : $secondValue = "2"; break;
					case "3" : $secondValue = "3"; break;
					case "4" : $secondValue = "4"; break;
				}
			}
			
			if (isset ($values['2'])) {
				switch ($values['2']) {
					case "3" : $thirdValue = "3"; break;
					case "4" : $thirdValue = "4"; break;
				}
			}
			
			if (isset ($values['3'])) {
				switch ($values['3']) {
					case "4" : $fourthValue = "4"; break;
				}
			}
	  ?>
        <p>
          <label><input name="display[]" type="checkbox" id="display[]" value="1"<?php
		  //Display a check mark, if the viewer is editing
		  		if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {
					if (isset ($firstValue)) {
						switch ($firstValue) {
							case "1" : echo " checked=\"checked\""; break;
						}
					}
				} else {
					echo "checked=\"checked\"";
				}
		  ?> />Score</label>
          <br />
          <label><input type="checkbox" name="display[]" id="display[]" value="2"<?php
		  //Display a check mark, if the viewer is editing
		  		if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {
					if (isset ($firstValue)) {
						switch ($firstValue) {
							case "2" : echo " checked=\"checked\""; break;
						}
					}
					
					if (isset ($secondValue)) {
						switch ($secondValue) {
							case "2" : echo " checked=\"checked\""; break;
						}
					}
				}
		  ?> />Selected Answers</label>
          <br />
          <label><input type="checkbox" name="display[]" id="display[]" value="3"<?php
		  //Display a check mark, if the viewer is editing
		  		if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {
					if (isset ($firstValue)) {
						switch ($firstValue) {
							case "3" : echo " checked=\"checked\""; break;
						}
					}
					
					if (isset ($secondValue)) {
						switch ($secondValue) {
							case "3" : echo " checked=\"checked\""; break;
						}
					}
					
					if (isset ($thirdValue)) {
						switch ($thirdValue) {
							case "3" : echo " checked=\"checked\""; break;
						}
					}
				}
		  ?> />Correct Answers</label>
          <br />
          <label><input type="checkbox" name="display[]" id="display[]" value="4"<?php
		  //Display a check mark, if the viewer is editing
		  		if (isset($_SESSION['review']) || isset ($_SESSION['testSettings'])) {
					if (isset ($firstValue)) {
						switch ($firstValue) {
							case "4" : echo " checked=\"checked\""; break;
						}
					}
					
					if (isset ($secondValue)) {
						switch ($secondValue) {
							case "4" : echo " checked=\"checked\""; break;
						}
					}
					
					if (isset ($thirdValue)) {
						switch ($thirdValue) {
							case "4" : echo " checked=\"checked\""; break;
						}
					}
					
					if (isset ($fourthValue)) {
						switch ($fourthValue) {
							case "4" : echo " checked=\"checked\""; break;
						}
					}
				}
		  ?> />Feedback</label>
        </p>
      </blockquote>
    </blockquote>
    </div>
    <div class="catDivider">
    <?php
		step("10", "Submit", "3" , "Submit")
	?>
    </div>
    <div class="stepContent">
    <blockquote>
      <?php
	  //Selectively display the buttons
			if (isset ($_SESSION['review'])) {
				submit("submit", "Modify Settings");
				echo "<input type=\"button\" name=\"cancel\" id=\"cancel\" value=\"Cancel\" onclick=\"MM_goToURL('parent','modify.php');\" />";
			} else {
				echo " <input name=\"back\" type=\"button\" id=\"back\" onclick=\"MM_goToURL('parent','test_settings.php?goTo=previous');\" value=\"&lt;&lt; Previous Step\" />";
				submit("submit", "Next Step &gt;&gt;");
			}
	  ?>
      <?php formErrors(); ?>
      </blockquote>
    </div>
</form>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>

<script type="text/javascript">
<!--
var sprytextarea1 = new Spry.Widget.ValidationTextarea("directionsCheck", {validateOn:["change"]});
//-->
</script>
</body>
</html>
<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
	if (isset ($_SESSION['step'])) {
		switch ($_SESSION['step']) {
			case "lessonSettings" : header ("Location: lesson_settings.php"); exit; break;
			case "lessonContent" : header ("Location: lesson_content.php"); exit; break;
			case "lessonVerify" : header ("Location: lesson_verify.php"); exit; break;
			case "testCheck" : header ("Location: test_check.php"); exit; break;
			case "testSettings" : header ("Location: test_settings.php"); exit; break;
			//case "testContent" : header ("Location: test_content.php"); exit; break;
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
//If the page is updating an item
	if (isset ($_GET['question']) && isset ($_GET['id'])) {
		$update = $_GET['id'];
		$currentModule = $_SESSION['currentModule'];
		$currentTable = strtolower(str_replace(" ","", $currentModule));
		$testDataGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "Multiple Choice") {
				$testData = $testDataCheck;
			} else {
				header ("Location: ../test_content.php");
				exit;
			}
		} else {
			header ("Location: ../test_content.php");
			exit;
		}
	} elseif (isset ($_GET['question']) || isset ($_GET['id'])) {
		header ("Location: ../test_content.php");
		exit;
	}
//Process the form
	if (isset ($_POST['submit']) && isset ($_POST['question']) && isset ($_POST['points']) && isset ($_POST['choice']) && isset ($_POST['answer'])) {
	//If the page is updating an item
		if (isset ($update)) {
			$currentModule = $_SESSION['currentModule'];
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			
		//Detirmine what kind of user interface this will have, either checkboxes or bullets
			if (sizeof($_POST['choice']) == "1") {
				$interface = "radio";
			} elseif (sizeof($_POST['choice']) > "1") {
				$interface = "checkbox";
			} elseif (sizeof($_POST['choice']) == "0") {
				header ("Location: multiple_choice.php");
				exit;
			}
			
		//Translate the partial credit to a numerical value
			if ($_POST['partialCredit'] == "yes") {
				$credit = "1";
			} else {
				$credit = "0";
			}
			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$choiceType = $interface;
			$partialCredit = $credit;
			$questionValue = serialize($_POST['choice']);
			$answerValue = mysql_real_escape_string(serialize($_POST['answer']));
			$partialCorrect = mysql_real_escape_string($_POST['feedBackPartial']);
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		
			$updateMatchingQuery = "UPDATE moduletest_{$currentTable} SET `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `partialCredit` = '{$partialCredit}', `choiceType` = '{$choiceType}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialCorrect` = '{$partialCorrect}' WHERE id = '{$update}'";
							
			$updateMatching = mysql_query($updateMatchingQuery, $connDBA);
			header ("Location: ../test_content.php?updated=choice");
			exit;
	//If the page is inserting an item		
		} else {
			$currentModule = $_SESSION['currentModule'];
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			
		//Get the last test question, and add one to the value for the next test
			$lastQuestionGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} ORDER BY position DESC", $connDBA);
			$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
			$lastQuestion = $lastQuestionFetch['position']+1;
			
		//Detirmine what kind of user interface this will have, either checkboxes or bullets
			if (sizeof($_POST['choice']) == "1") {
				$interface = "radio";
			} elseif (sizeof($_POST['choice']) > "1") {
				$interface = "checkbox";
			} elseif (sizeof($_POST['choice']) == "0") {
				header ("Location: multiple_choice.php");
				exit;
			}
			
		//Translate the partial credit to a numerical value
			if ($_POST['partialCredit'] == "yes") {
				$credit = "1";
			} else {
				$credit = "0";
			}
			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$choiceType = $interface;
			$partialCredit = $credit;
			$questionValue = serialize($_POST['choice']);
			$answerValue = mysql_real_escape_string(serialize($_POST['answer']));
			$partialCorrect = mysql_real_escape_string($_POST['feedBackPartial']);
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			
		
			$insertChoiceQuery = "INSERT INTO moduletest_{$currentTable} (
							`id`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `totalFiles`, `choiceType`, `case`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialCorrect`
							) VALUES (
							NULL, '{$lastQuestion}', 'Multiple Choice', '{$points}', '{$extraCredit}', '{$partialCredit}', '0', '{$choiceType}', '1', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$partialCorrect}'
							)";
							
			$insertChoice = mysql_query($insertChoiceQuery, $connDBA);
			header ("Location: ../test_content.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Insert Multiple Choice Question"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/showHide.js" type="text/javascript"></script>
<script src="../../../../javascripts/insert/newMultipleChoice.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Module Setup Wizard : Insert Multiple Choice Question</h2>
    <p>Creates a set bulleted responses.</p>
    <p>&nbsp;</p>
	<form action="multiple_choice.php<?php
		if (isset ($update)) {
			echo "?question=" . $testData['position'] . "&id=" . $testData['id'];
		}
    ?>" method="post" name="choice" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider"><img src="../../../../images/numbering/1.gif" alt="1." width="22" height="22" /> Question</div>
      <div class="stepContent">
      <blockquote>
        <p>Question Directions:</p>
        <blockquote>
          <p><span id="directionsCheck">
            <textarea id="question" name="question" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['question']);
			}
		  ?></textarea>
          <span class="textareaRequiredMsg"></span></span></p>
        </blockquote>
        <p>&nbsp;</p>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/2.gif" alt="2." width="22" height="22" /> Question Settings</div>
      <div class="stepContent">
      <blockquote>
        <p>Question Points:
          <input name="points" type="text" id="points" size="5"  autocomplete="off" maxlength="5"  class="validate[required,custom[onlyNumber]]"<?php
		  	if (isset ($update)) {
				echo " value=\"" . $testData['points'] . "\"";
			}
		  ?> />              
          <label>
          <input type="checkbox" name="extraCredit" id="extraCredit"<?php
			if (isset ($update)) {
				if ($testData['extraCredit'] == "on") {
					echo " checked=\"checked\"";
				}
			}
		  ?> />
            Extra Credit </label>
        </p>
          <p>Allow Partial Credit:
            <label>
            <select name="partialCredit" id="partialCredit" onchange="toggleAlphaDiv(this.value);">
              <option value="yes"<?php if (isset ($update)) {if ($testData['partialCredit'] == "1") {echo " selected=\"selected\"";}} ?>>Yes</option>
              <option value="no"<?php if (isset ($update)) {if ($testData['partialCredit'] == "0") {echo " selected=\"selected\"";}} else {echo " selected=\"selected\"";}?>>No</option>
            </select>
            </label>
          </p>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/3.gif" alt="3." width="22" height="22" /> Question Content</div>
      <div class="stepContent">
      <blockquote>
		<?php
			//Grab all of the answers and values if the question is being edited
				if (isset ($update)) {	
					$valueGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE id = '{$update}'", $connDBA);	
					$value = mysql_fetch_array($valueGrabber);
					$answers = unserialize($value['answerValue']);
					echo "<table width=\"450\" border=\"0\"><tr><td width=\"40\">";

				//Echo each checkbox item	
					echo "<table width=\"10\" name=\"choices\" id=\"choices\">";
					$start = sizeof (unserialize($value['answerValue']));
					for ($i = 1; $i <= $start; $i++) {
						echo "<tr><td><div style=\"padding:2px;\"><input type=\"checkbox\" name=\"choice[]\" id=\"c" . $i . "\" value=\"";
						echo $i;
						echo "\" class=\"validate[minCheckbox[1]]\"";
						$questions = unserialize($value['questionValue']);
						while (list($questionKey, $questionArray) = each($questions)) {
                    		if ($i == $questionArray) {
								echo " checked=\"checked\"";
							}
						}
						echo " /></div></td></tr>";
					}
					echo "</table>";
					
					echo "</td><td width=\"400\">";
					
				//Echo each value
					echo "<table width=\"50%\" name=\"answers\" id=\"answers\">";
					while (list($answerKey, $answerArray) = each($answers)) {
						$id = $answerKey+1;
                    	echo "<tr><td><input type=\"text\" name=\"answer[]\" autocomplete=\"off\" id=\"a" . $id . "\" value=\""; echo stripslashes($answerArray);  echo "\" class=\"validate[required]\" size=\"50\" /></td></tr>";
					}
					echo "</table>";
					
					echo "</td></tr></table>";
			//Echo empty fields if the page is not editing a question
				} else {					
					echo "<table width=\"450\" border=\"0\"><tr><td width=\"40\"><tr><td><table width=\"10\" name=\"choices\" id=\"choices\"><tr><td><div style=\"padding:2px;\"><input type=\"checkbox\" name=\"choice[]\" id=\"c1\" value=\"1\" class=\"validate[minCheckbox[1]]\" /></div></td></tr><tr><td><div style=\"padding:3px;\"><input type=\"checkbox\" name=\"choice[]\" id=\"c2\" value=\"2\" class=\"validate[minCheckbox[1]]\" /></div></td></tr></table></td><td width=\"400\"><table width=\"50%\" name=\"answers\" id=\"answers\"><tr><td><input type=\"text\" name=\"answer[]\" autocomplete=\"off\" id=\"a1\" size=\"50\" class=\"validate[required]\" /></td></tr><tr><td><input type=\"text\" name=\"answer[]\" autocomplete=\"off\" id=\"a2\" size=\"50\" class=\"validate[required]\" /></td></tr></table></td></tr></table>";
				}
			?>
             <p><input value="Add Another Option" type="button" onclick="appendRow('choices', '<div style=\'padding:2px;\'><input type=\'checkbox\' name=\'choice[]\' id=\'c', '\' value=\'', '\' class=\'validate[minCheckbox[1]]\' /></div>');appendRow('answers', '<input type=\'text\' name=\'answer[]\' autocomplete=\'off\' id=\'a', '\' size=\'50\' class=\'validate[required]\' /><!--','//-->')" />
              <input value="Remove Last Option" type="button" onclick="deleteLastRow('choices');deleteLastRow('answers')" />
            </p>
          <p>&nbsp;</p>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/4.gif" alt="4." width="22" height="22" /> Feedback</div>
      <div class="stepContent">
      <blockquote>
        <p>Feedback for Correct Answer:</p>
        <blockquote>
          <p>
            <textarea id="feedBackCorrect" name="feedBackCorrect" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['correctFeedback']);
			}
		    ?></textarea>
          </p>
        </blockquote>
        <div id="contentHide"<?php if (isset ($update)) {if ($testData['partialCredit'] == "0") {echo " class=\"contentHide\"";}} else {echo " class=\"contentHide\"";}?>>
          <p>Feedback for Partial Correct Answer:</p>
          <blockquote>
            <p>
            <textarea id="feedBackPartial" name="feedBackPartial" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['partialCorrect']);
			}
		    ?></textarea>
            </p>
          </blockquote>
        </div>
        <p>Feedback for Incorrect Answer: </p>
        <blockquote>
          <p>
            <textarea id="feedBackIncorrect" name="feedBackIncorrect" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['incorrectFeedback']);
			}
		    ?></textarea>
          </p>
        </blockquote
        ><p>&nbsp;</p>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/5.gif" alt="5." width="22" height="22" /> Finish</div>
      <div class="stepContent">
      <blockquote>
        <p>
          <label>
          <?php submit("submit", "Submit"); ?>
          </label>
          <label>
          <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
          </label>
          <label>
          <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../test_content.php');return document.MM_returnValue" value="Cancel" />
          </label>
        </p>
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
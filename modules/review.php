<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	headers("Review Test", "Student,Site Administrator", "calculate", true);
	
//Grab all module and test data
	$userData = userData();
	$testID = $_GET['id'];
	$parentTable = "moduletest_" . $testID;
	$testTable = "testdata_" . $userData['id'];
	$attempt = lastItem($testTable, "testID", $testID, "attempt");
	$currentAttempt = $attempt - 1;
	
	if (isset ($_GET['id'])) {
		$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$_GET['id']}' LIMIT 1");
		$testDataGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' ORDER BY `testPosition` ASC", "raw");
	} else {
		redirect("index.php");
	}
	
//Process the form
	if (isset($_POST['submit'])) {
		foreach ($_POST as $key => $score) {
			$id = str_replace("score_", "", $key);
			query("UPDATE `{$testTable}` SET `score` = '{$score}' WHERE `testID` = '{$testID}' AND `questionID` = '{$id}' AND `attempt` = '{$currentAttempt}' LIMIT 1");
		}
		
		redirect($_SERVER['REQUEST_URI']);
	}
	
//Display the test results
	form("review");
	echo "<table class=\"dataTable\">";
	$count = 1;
	$restrictImport = array();
	$values = unserialize($moduleInfo['display']);
	$score = false;
	$selectedAnswers = false;
	$correctAnswers = false;
	$feedback = false;
	
	if (is_array($values)) {
		foreach($values as $checkbox) {
			switch ($checkbox) {
				case "1" : $score = true; break;
				case "2" : $selectedAnswers = true; break;
				case "3" : $correctAnswers = true; break;
				case "4" : $feedback = true; break;
			}
		}
	}
	
	$submitVerifyGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `type` != 'Description'", "raw");
	
	while ($submitVerify = mysql_fetch_array($submitVerifyGrabber)) {
		if (empty($submitVerify['score']) && intval($submitVerify['score']) !== 0) {
			$submit = true;
		}
	}
	
	if (isset($submit)) {
		title("Review Test", "There are several questions in this test which require manual grading. Please scroll down and locate the test question(s) which require grading (indicated by a gray background). Some questions are accompanied by a sample answer provided by the module creator. Compare your answer with the one provided and enter the appropriate score in the text field located under the question number.");
	} else {
		title("Review Test", "Below are the results to your test.");
	}
	
	while ($testData = mysql_fetch_array($testDataGrabber)) {	
		if ($testData['link'] != "0" && !empty($testData['link']) && !in_array($testData['link'], $restrictImport)) {
			$linkData = query("SELECT * FROM `{$testTable}` WHERE `questionID` = '{$testData['link']}'");
			array_push($restrictImport, $testData['link']);
			echo "<tr><td colspan=\"2\" valign=\"top\">" . prepare($linkData['question'], false, true) . "</td></tr>";
			unset($linkData);
		}
		
		if ($testData['type'] != "Description") {
			echo "<tr";
			if (empty($testData['score']) && intval($testData['score']) !== 0) {echo " class=\"attention\">";} else {echo ">";}
			echo "<td width=\"100\" valign=\"top\"><p>";
			echo "<span class=\"questionNumber\">Question " . $count++ . "</span><br />";
			
			if (empty($testData['score']) && $testData['score'] !== "0" && $submit == true) {
				echo "<br />";
				textField("score_" . $testData['questionID'], "score_" . $testData['questionID'], "5", "5", false, true, ",custom[onlyNumber]", false, "testData", "score", " onchange=\"calculate('score_" . $testData['questionID'] . "', '" . $testData['points'] . "', 'calculate_" . $testData['questionID'] . "');\" tabindex=\"" . $count . "\"");
				echo " / " . $testData['points'];
				
				if ($testData['extraCredit'] == "on") {
					echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				}
				
				echo "<div align=\"center\">";
				textField("calculate_" . $testData['questionID'], "calculate_" . $testData['questionID'], "7", "7", false, false, false, false, false, false, " class=\"calculate\" onclick=\"blur()\"");
				echo "</div>";
			} else {
				if (strstr($testData['score'], ".")) {
					$scoreFormatPrep = explode("." , $testData['score']);
					
					if ($scoreFormatPrep['1'] == 0) {
						$scoreFormat = $scoreFormatPrep['0'];
					} else {
						$scoreFormat = $testData['score'];
					}
				} else {
					$scoreFormat = $testData['score'];
				}
				
				echo "<span class=\"questionPoints\">" . $scoreFormat . " / " . $testData['points'] . " ";
				
				if ($testData['score'] == "1") {
					echo "Point";
				} else {
					echo "Points";
				}
				
				echo "</span>";
				
				if ($testData['extraCredit'] == "on") {
					echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				}
				
				if (intval($testData['score']) === intval($testData['points'])) {
					echo "<br /><br /><img src=\"../system/images/common/correct.png\">";
				}
				
				if (intval($testData['score']) < intval($testData['points']) && intval($testData['score']) !== 0) {
					echo "<br /><br /><img src=\"../system/images/common/partial.png\">";
				}
				
				if (intval($testData['score']) === 0) {
					echo "<br /><br /><img src=\"../system/images/common/incorrect.png\">";
				}
			}
			
			echo "</p></td><td valign=\"top\">" . prepare($testData['question'], false, true) . "<br /><br />";
		}
		
		switch ($testData['type']) {
			case "Description" : 
				if (!in_array($testData['questionID'], $restrictImport)) {
					echo "<tr><td colspan=\"2\" valign=\"top\">" . $testData['question'] . "</td></tr>";
					array_push($restrictImport, $testData['questionID']);
				}
				
				break;
			case "Essay" : 
				if ($selectedAnswers == true) {
					echo "<p>Selected Answer: </p><blockquote>";
					
					if (!empty ($testData['userAnswer'])) {
						echo unserialize($testData['userAnswer']);
					} else {
						echo "<span class=\"notAssigned\">None Given</span>";
					}
					
					echo "</blockquote>";
				}
				
				if ($correctAnswers == true && !empty($testData['testAnswer'])) {
					echo "<p>Correct Answer: </p><blockquote>" . $testData['testAnswer'] . "</blockquote>";
				}
					
				break;
				
			case "File Response" : 
				$fillValue = unserialize($testData['userAnswer']);
				
				if ($selectedAnswers == true && !empty($testData['userAnswer'])) {
					echo "<p>Selected Answers: </p><ol>";
					
					foreach ($fillValue as $file) {
						if (file_exists($_GET['id'] . "/test/responses/" . $file)) {
							echo "<li><a href=\"../gateway.php/modules/" . $_GET['id'] . "/test/responses/" . urlencode($file) . "\" target=\"_blank\">" . $file . "</a></li>";
						} else {
							echo "<li>" . $file . " <span class=\"notAssigned\">File deleted</span></li>";
						}
					}
					
					echo "</ol>";
				} else {
					echo "<p>Selected Answer: </p><span class=\"notAssigned\">None Given</span>";
				}
				
				if ($correctAnswers == true && !empty($testData['testAnswer'])) {
					echo "<p>Correct Answer: </p><blockquote>";
					
					if (file_exists($_GET['id'] . "/test/answers/" . $testData['testAnswer'])) {
						echo "<a href=\"../gateway.php/modules/" . $_GET['id'] . "/test/answers/" . urlencode($testData['testAnswer']) . "\" target=\"_blank\">" . $testData['testAnswer'] . "</a>";
					} else {
						echo $testData['testAnswer'] . " <span class=\"notAssigned\">File deleted</span>";
					}
					
					echo "</blockquote>";
				}
				
				break;
				
			case "Fill in the Blank" : 
				$sentenceValues = unserialize($testData['questionValue']);
				$correctAnswer = unserialize($testData['testAnswer']);
				$userAnswer = unserialize($testData['userAnswer']);
				
				if ($selectedAnswers == true) {
					echo "<p>Selected Answer: </p><blockquote>";
					
					for ($list = 0; $list <= sizeof($sentenceValues) - 1; $list ++) {
						echo $sentenceValues[$list];
						
						if ($list < sizeof($sentenceValues) - 1 && isset($userAnswer[$list]) && !empty($userAnswer['list'])) {
							echo " <strong>" . $userAnswer[$list] . "</strong> ";
						} else {
							echo " <strong><span class=\"notAssigned\">None Given</span></strong> ";
						}
					}
					
					echo "</blockquote>";
				}
				
				if ($correctAnswers == true) {
					echo "<p>Correct Answer: </p><blockquote>";
					
					for ($list = 0; $list <= sizeof($sentenceValues) - 1; $list ++) {
						echo $sentenceValues[$list];
						
						if ($list < sizeof($sentenceValues) - 1 && isset($correctAnswer[$list])) {
							echo " <strong>" . $correctAnswer[$list] . "</strong> ";
						}
					}
					
					echo "</blockquote>";
				}
				
				break;
			
			case "Matching" : 
				$questionValue = unserialize($testData['questionValue']);
				$userAnswer = unserialize($testData['userAnswer']);
				$answerValues = unserialize($testData['answerValueScrambled']);
				$correctAnswer = unserialize($testData['testAnswer']);
				
				if ($selectedAnswers == true || $correctAnswers == true) {
					echo "<table width=\"100%\" class=\"dataTable\"><tr><th class=\"tableHeader\" width=\"200\">Question</th>";
					
					if ($selectedAnswers == true) {
						echo "<th class=\"tableHeader\" width=\"200\">Selected Answers</th>";
					}
					
					if ($correctAnswers == true) {
						echo "<th class=\"tableHeader\" width=\"200\">Correct Answers</th>";
					}
					
					echo "</tr>";
				
					for ($list = 0; $list <= sizeof($questionValue) - 1; $list++) {
						echo "<tr";
			  			if (sprintf($list + 1) & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
						
						echo "<td width=\"200\"><p>" . $questionValue[$list] . "</p></td>";
						
						if ($selectedAnswers == true) {
							echo "<td width=\"200\"><p>" . $answerValues[sprintf($userAnswer[$list] - 1)] . "</p></td>";
						}
						
						if ($correctAnswers == true) {
							echo "<td width=\"200\"><p>" . $correctAnswer[$list] . "</p></td>";
						}
						
						echo "</tr>";
					}
				}
				
				echo"</table>";				  
				break;
			
			case "Multiple Choice" : 
				$choices = unserialize($testData['answerValue']);
				$answers = unserialize($testData['userAnswer']);
				$correctAnswer = unserialize($testData['testAnswer']);
								
				if ($selectedAnswers == true) {					
					if (is_array($answers) && sizeof($answers) > 1) {
						echo "<p>Selected Answers: </p>";
						echo "<ul>";
						
						for ($list = 0; $list <= sizeof($answers) - 1; $list ++) {
							echo "<li>" . $choices[sprintf($answers[$list] - 1)] . "</li>";
						}
						
						echo "</ul>";
					} else {
						echo "<p>Selected Answer: </p>";
						echo "<blockquote><p>" . $choices[sprintf($answers - 1)] . "</p></blockquote>";
					}
				}
				
				if ($correctAnswers == true) {					
					if (is_array($choices) && sizeof($correctAnswer) > 1) {
						echo "<p>Correct Answers: </p>";
						echo "<ul>";
						
						for ($list = 0; $list <= sizeof($correctAnswer) - 1; $list ++) {
							echo "<li>" . $choices[sprintf($correctAnswer[$list] - 1)] . "</li>";
						}
						
						echo "</ul>";
					} else {						
						echo "<p>Correct Answer: </p>";
						echo "<blockquote><p>" . $choices[sprintf($correctAnswer['0'] - 1)] . "</p></blockquote>";
					}
				}
				
				break;
				
			case "Short Answer" : 
				if ($selectedAnswers == true) {
					echo "<p>Selected Answer: </p><blockquote><p><strong>" . unserialize($testData['userAnswer']) . "</strong></p></blockquote>";
				}
				
				if ($correctAnswers == true) {					
					if (is_array(unserialize($testData['testAnswer']))) {
						echo "<p>Correct Answers: </p>";
						echo "<ul>";
						
						foreach (unserialize($testData['testAnswer']) as $correctAnswer) {
							echo "<li>" . $correctAnswer . "</li>";
						}
						
						echo "</ul>";
					} else {
						echo "<p>Correct Answer: </p>";
						echo "<p><strong>" . unserialize($testData['testAnswer']) . "</strong></p></blockquote>";
					}
				}
				
				break;
				
			case "True False" : 
				if ($selectedAnswers == true) {
					echo "<p>Selected Answer: </p><blockquote><p><strong>";
					
					if (unserialize($testData['userAnswer']) == "1") {
						echo "True";
					} else {
						echo "False";
					}
					
					echo "</strong></p></blockquote>";
				}
				
				if ($correctAnswers == true) {
					echo "<p>Correct Answer: </p><blockquote><p><strong>";
					
					if ($testData['testAnswer'] == "1") {
						echo "True";
					} else {
						echo "False";
					}
					
					echo "</strong></p></blockquote>";
				}
				
				break;
		}
		
		if ($feedback == true && !empty($testData['feedback'])) {
			echo "<p>Feedback :</p><blockquote>" . $testData['feedback'] . "</blockquote>";
		}
		
		if ($testData['type'] != "Description") {
			echo "<br /><br /></td></tr>";
		}
	}
	
	echo "</table>";
	
	if (isset($submit)) {
		echo "<p><blockquote>";
		button("submit", "submit", "Submit Scores", "submit");
		echo "</blockquote></p>";
	}
	
	closeForm(false, true);

//Include the footer
	footer();
?>
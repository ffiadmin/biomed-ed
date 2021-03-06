<?php
/*
LICENSE: See "license.php" located at the root installation

This is the lesson verification page for the learning unit generator, which allows creators to fully sample their lesson prior to deployment.
*/

//Header functions
	require_once('../../system/server/index.php');
	require_once('../system/server/index.php');	
	$monitor = monitor("Verify Content", "navigationMenu,plugins");
	
//Test to see if a test exists
	$testCheck = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentUnit']}'");
	
//Title
	navigation("Verify Content", "Content may be reviewed in the section below. Changes can be made to the lesson by clicking the &quot;Previous Step&quot; button.");
	
//Lesson preview
	lesson($monitor['currentUnit'], $monitor['lessonTable']);
	
//Display navigation buttons
	echo "<blockquote><p>\n";
	echo button("back", "back", "&lt;&lt; Previous Step", "button", "lesson_content.php");
	
	if ($testCheck['test'] == "1") {
		echo button("next", "next", "Next Step &gt;&gt;", "button", "test_settings.php");
	} else {
		echo button("next", "next", "Next Step &gt;&gt;", "button", "test_check.php");
	}
	
	if (isset ($_SESSION['review'])) {
		echo button("submit", "submit", "Finish", "button", "../index.php?updated=unit");
	}
	
	echo "</p></blockquote>\n";

//Include the footer
	footer();
?>
<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: February 5th, 2010
Last updated: February 9th, 2010

This is the site information management page.
*/

//Header functions
	require_once('../../../../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	headers("Site Information", "tinyMCESimple,validate", true);
	lockAccess();
	
//Grab all site information
	$siteInfo = query("SELECT * FROM `siteprofiles` WHERE `id` = '1'");
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['name']) && !empty($_POST['timeZone'])) {
		$name = escape($_POST['name']);
		$footer = escape($_POST['footer']);
		$description = escape($_POST['description']);
		$author = escape($_POST['author']);
		$copyright = escape($_POST['copyright']);
		$meta = escape($_POST['meta']);
		$language = $_POST['language'];
		$timeZone = $_POST['timeZone'];
		
		query("UPDATE `siteprofiles` SET `siteName` = '{$name}', `siteFooter` = '{$footer}', `description` = '{$description}', `author` = '{$author}', `copyright` = '{$copyright}', `meta` = '{$meta}', `language` = '{$language}', `timeZone` = '{$timeZone}' WHERE `id` = '1'");
		redirect("index.php?updated=siteinfo");
	}
	
//Title
	title("Site Information", "Modify the site name and footer, as well as information which will help search engines better locate your site.");
	
//Site imformation form
	echo form("information");
	catDivider("Site Name &amp; Footer", "alignLeft", true);
	echo "<blockquote>\n";
	directions("The site name will appear in the title of your site", true);
	indent(textField("name", "name", false, false, false, true, false, false, "siteInfo", "siteName"));
	directions("The footer is displayed at the bottom-left of each page", false);
	indent(textArea("footer", "footerInput", false, false, false, false, "siteInfo", "siteFooter"));
	echo "</blockquote>\n";
	
	catDivider("Search Keywords and Information", "alignLeft");
	echo "<blockquote>\n";
	directions("Site description", false);
	indent(textArea("description", "description", false, true, false, false, "siteInfo", "description", "class=\"noEditorSimple\""));
	directions("The author of this site, or the name of this organization or company", false);
	indent(textField("author", "author", false, false, false, true, false, false, "siteInfo", "author"));
	directions("Copyright statement", false);
	indent(textArea("copyright", "copyright", false, false, false, false, "siteInfo", "copyright", "class=\"noEditorSimple\""));
	directions("List keywords in the text box below, and <strong>separate each phrase with a comma and a space (e.g. website, my website, www)</strong>", false);
	indent(textArea("meta", "meta", false, false, false, false, "siteInfo", "meta", "class=\"noEditorSimple\""));
	directions("The language of this site (changing this option will not change the language pack of this system)", false);
	indent(dropDown("language", "language", "English", "en-US", false, false, false, false, "siteInfo", "language"));
	directions("Time zone", true);
	indent(dropDown("timeZone", "timeZone", "Eastern Time Zone,Central Time Zone,Mountain Time Zone,Pacific Time Zone,Alaskan Time Zone,Hawaii-Aleutian Time Zone", "America/New_York,America/Chicago,America/Denver,America/Los_Angeles,America/Juneau,Pacific/Honolulu", false, true, false, false, "siteInfo", "timeZone"));
	
	catDivider("Submit", "alignLeft");
	formButtons();
	echo closeForm();
	
//Include the footer
	footer();
?>
<?php 
//Header functions
	require_once('../../../Connections/connDBA.php');
	$monitor = monitor("Module Content", "navigationMenu");

//Reorder pages	
	reorder($monitor['lessonTable'], "lesson_content.php");
	
//Delete a page
	if (isset ($_GET['id']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
		$id = $_GET['id'];
		$deleteGrabber = mysql_query("SELECT * FROM `{$monitor['lessonTable']}` WHERE `id` = '{$id}'", $connDBA);
		$delete = mysql_fetch_array($deleteGrabber);
		
		if ($deleteGrabber) {
			if ($delete['type'] == "Embedded Content") {
				delete($monitor['lessonTable'], "lesson_content.php?deleted=embedded", true, $monitor['directory'] . $delete['attachment']);
			} else {
				delete($monitor['lessonTable'], "lesson_content.php?deleted=custom", true);
			}
		}
	}

//Update a session to go to different steps
	if (isset ($_POST['submit'])) {
		redirect("../index.php?updated=module");
	}

	if (isset ($_POST['back'])) {
		$_SESSION['step'] = "lessonSettings";
		redirect("lesson_settings.php");
	}
	
	if (isset ($_POST['next'])) {
		$_SESSION['step'] = "lessonVerify";
		redirect("lesson_verify.php");
	}
	
//Title
	navigation("Module Content", "All of the content for this lesson will be managed from this page.  A <strong>custom content page</strong> is just like a regular web page,   with text and images. An <strong>embedded content page</strong> will contain something, such as a video or PDF, as the main content.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Add Custom Content", "manage_content.php?type=custom", "toolBarItem custom");
	echo URL("Add Embedded Content", "manage_content.php?type=embedded", "toolBarItem embedded");
	echo "</div>";
	
//Display message updates
	message("inserted", "custom", "success", "The <strong>custom content</strong> page was successfully inserted");
	message("inserted", "embedded", "success", "The <strong>embedded content</strong> page was successfully inserted");
	message("updated", "custom", "success", "The <strong>custom content</strong> page was successfully updated");
	message("updated", "embedded", "success", "The <strong>embedded content</strong> page was successfully updated");
	message("deleted", "custom", "success", "The <strong>custom content</strong> page was successfully deleted");
	message("deleted", "embedded", "success", "The <strong>embedded content</strong> page was successfully deleted");

//Pages table
	if (exist($monitor['lessonTable']) == true) {
		$pageGrabber = mysql_query("SELECT * FROM `{$monitor['lessonTable']}` ORDER BY `position` ASC", $connDBA);
		
		echo "<table class=\"dataTable\"><tbody><tr><th width=\"75\" class=\"tableHeader\">Order</th><th width=\"150\" class=\"tableHeader\">Type</th><th width=\"250\" class=\"tableHeader\">Title</th><th class=\"tableHeader\">Content or Comments</th><th width=\"75\" class=\"tableHeader\">Edit</th><th width=\"75\" class=\"tableHeader\">Delete</th></tr>";
		
		while($lessonData = mysql_fetch_array($pageGrabber)) {
			echo "<tr";
			if ($lessonData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			echo "<td width=\"75\">"; reorderMenu($lessonData['id'], $lessonData['position'], "lessonData", $monitor['lessonTable']); echo "</td>";
			echo "<td width=\"150\">" . $lessonData['type'] . "</td>";
			echo "<td width=\"250\">" . URL($lessonData['title'], "preview_page.php?page=" . $lessonData['position'], false, false, "Preview the <strong>" . $lessonData['title'] . "</strong> page", false, true, "640", "480") . "</td>";
			echo "<td>" . commentTrim(55, $lessonData['content']) .  "</td>";
			
			switch ($lessonData['type']) {
				case "Custom Content" : $URL = "manage_content.php?type=custom"; break;
				case "Embedded Content" : $URL = "manage_content.php?type=embedded"; break;
			}
			
			echo "<td width=\"75\">" . URL(false, $URL . "&id=" .  $lessonData['id'], "action edit", false, "Edit the <strong>" . $lessonData['title'] . "</strong> page", false) . "</td>";
			echo "<td width=\"75\">" . URL(false, "lesson_content.php?id=" .  $lessonData['id'] . "&action=delete", "action delete", false, "Delete the <strong>" . $lessonData['title'] . "</strong> page", true) . "</td>";
			closeForm(false, false);
			echo "</tr>";
		}
		
		echo "</tbody></table>";
	} else {
		echo "<div class=\"noResults\">There are no pages in this lesson. <a href=\"manage_content.php\">Create a new page now</a>.</div>";
	}
	
//Display navigation buttons
	echo "<blockquote>";
	form("navigate");
	
	button("back", "back", "&lt;&lt; Previous Step", "submit");
	
	if (exist($monitor['lessonTable']) == true) {
		button("next", "next", "Next Step &gt;&gt;", "submit");
	}
	
	if (isset ($_SESSION['review'])) {
		button("submit", "submit", "Finish", "submit");
	}
	
	echo "</blockquote>";
	
//Include the footer
	footer();
?>
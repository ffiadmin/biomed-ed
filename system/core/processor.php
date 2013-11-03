<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

/* 
Created by: Oliver Spryn
Created on: Novemeber 28th, 2010
Last updated: Novemeber 28th, 2010

This script contains user commonly used functions to process 
simple requests, such as reordering a list, deleting an item, 
or setting its availability.
*/

//Delete a file or directory, and its contents
	function deleteAll($path) {		
		if (file_exists($path)) {
			if (is_file($path)) {
				unlink($path);
				return true;
			} else {
				$directory = opendir($path);
				
				while($contents = readdir($directory)) {
					if ($contents !== "." && $contents !== "..") {
						if (is_file(rtrim($path . "/") . "/" . $contents)) {
							unlink(rtrim($path . "/") . "/" . $contents);
						} elseif (is_dir(rtrim($path . "/") . "/" . $contents)) {
							return deleteAll(rtrim($path . "/") . "/" . $contents);
						}
					}
				}
				
				rmdir(rtrim($path . "/"));
				
				return true;
			}
		} else {
			return false;
		}
	}

//Set an item's avaliability
	function avaliability($table, $redirect) {
		if (isset($_POST['id']) && $_POST['action'] == "setAvaliability") {			
			$id = $_POST['id'];
			
			if (!$_POST['option']) {
				$option = "";
			} else {
				$option = $_POST['option'];
			}
			
			query("UPDATE `{$table}` SET `visible` = '{$option}' WHERE id = '{$id}'");
			redirect($redirect);
		}
	}
	
//Reorder a list of items
	function reorder($table, $redirect) {
		if (isset($_POST['action']) && $_POST['action'] == "modifyPosition" && isset($_POST['id']) && isset($_POST['position']) && isset($_POST['currentPosition'])) {
			$id = $_POST['id'];
			$newPosition = $_POST['position'];
			$currentPosition = $_POST['currentPosition'];
			
			if (!exist($table, "position", $currentPosition)) {
				redirect($redirect);
			}
		  
			if ($currentPosition > $newPosition) {
				query("UPDATE `{$table}` SET `position` = position + 1 WHERE `position` >= '{$newPosition}' AND `position` <= '{$currentPosition}'");
			} elseif ($currentPosition < $newPosition) {
				query("UPDATE `{$table}` SET `position` = position - 1 WHERE` position` <= '{$newPosition}' AND `position` >= '{$currentPosition}'");
			} else {
				redirect($redirect);
			}
			
			query("UPDATE `{$table}` SET `position` = '{$newPosition}' WHERE `id` = '{$id}'");
			redirect($redirect);
		}
	}
	
//Delete an item
	function delete($table, $redirect = false, $reorder = true, $file = false, $directory = false, $extraTables = false) {
		if (isset ($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['id'])) {
			if (isset ($_GET['questionID'])) {
				$deleteItem = $_GET['questionID'];
			} else {
				$deleteItem = $_GET['id'];
			}
			
			if (!exist($table, "id", $deleteItem)) {
				redirect($redirect);
			}
			
			if ($reorder == true) {
				$itemPosition = query("SELECT * FROM `{$table}` WHERE `id` = '{$deleteItem}'");
				
				query("UPDATE `{$table}` SET `position` = position - 1 WHERE `position` > '{$itemPosition['position']}'");
				query("DELETE FROM `{$table}` WHERE `id` = '{$deleteItem}'");
			} else {
				query("DELETE FROM `{$table}` WHERE `id` = '{$deleteItem}'");
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
					query("DROP TABLE `{$table}`");
				}
			}
			
			if ($redirect == true) {
				redirect($redirect);
			}
		}
	}
?>
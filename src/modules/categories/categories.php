<?php
# File:				Categories.php
# Created by:		MRH
# Last edit by:		LHG


	// First of, check if the user came to this page via a header
	if(isset($_GET)) {
		
		// Fetch the GET parameters from the header
		$iCategoryID = $_GET['iCategoryID'];
		$sReturnTo	 = $_GET['sReturnTo'];
		
		// Check if the values are the correct datatype
		if(!Is_integer($iCategoryID)) {
			// If category ID is not an integer,
			// send user back from whence they came
			// until they are complete again
			header("Location: " . $sReturnTo);
		} else {
			// If everything was okay, proceed
			// Firstly, include database file
			include ('../../php/conf_db.php');
			
			// Construct query
			$SQL = "SELECT * 
					FROM test_subcategories
					WHERE id IN(
								SELECT subcategory_id 
								FROM test_connect_categories 
								WHERE category_id = '" . $iCategoryID . "'
								)
					";
			
			// Fetch result from query
			$result = $mysqli->query($SQL);
			
			// Show debug status
			echo '--DEBUG START--<br /><br />';
			
			// Print all query results to the screen
			while($res = $result->fetch_assoc()) {
				echo $res['title'] . '<br />';
				echo $res['description'] . '<br /><br />';
			}
			
			// Indicate end of debugging
			echo '<br /><br />--DEBUG END--<br />';
		}
		
	} else {
		// If the user came her illegally
		header("Location: ../../../index.php");
	}
	
?>
<p>Welkom bij Kwalinet</p>

<br><br>

Hier onder vind je alle info over deze categorie:

<br>

<?php

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
		$SQL = "SELECT * 
				FROM test_subcategories
				WHERE id IN(
						    SELECT subcategory_id 
							FROM test_connect_categories 
							WHERE category_id = '" . $iCategoryID . "'
							)
				";
		
		$result = $mysqli->query($SQL);
		
		echo '--DEBUG START--<br /><br />';
		
		while($res = $result->fetch_assoc()) {
			echo $res['title'] . '<br />';
			echo $res['description'] . '<br /><br />';
		}
		
		echo '<br /><br />--DEBUG END--<br />';
	}

?>
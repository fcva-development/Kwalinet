<?php

include '../../php/conf_db.php';

	if($_POST)
	{
		if($_POST['sUsername'] != '' && $_POST['sPassword'] != '')//if not empty
		{
			$sUsername = $_POST['sUsername'];
			$sPassword = $_POST['sPassword'];
			
			echo $sPassword.' '.$sUsername;
		
			$sql = "SELECT *
				FROM test_users
				WHERE user_name = '" . $sUsername . "'
				AND user_password = '" . md5($sPassword) . "'"
				;
				
			$query = mysqli_query($con, $sql);
			$res = mysqli_fetch_array($query);
		
			if(mysqli_num_rows($query) == 0)
			{
				echo 'EEN Fout bij inloggen. U word teruggestuurd.';
				header('Refresh: 3; url=../index.php');
			}
			else
			{
				$_SESSION['User']['Loggedin'] = true;
				$_SESSION['User']['PageView'] = 'home';
				header('Refresh: 1; url=../index.php');
			}
		}
		else
		{
		
			echo 'Fout bij t inloggen. U word teruggestuurd.';
			header('Refresh: 3; url=../index.php');
			
		}
	}else{
	
		header("Location: index.php");
		
	}
?>
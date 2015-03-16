<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="author" content="www.frebsite.nl" />
		<meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=yes" />

		<title>Kwalinet Demo</title>

		<link type="text/css" rel="stylesheet" href="css/demo.css" />
		<link type="text/css" rel="stylesheet" href="css/content_style.css" />
		<link type="text/css" rel="stylesheet" href="src/css/jquery.mmenu.all.css" />

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="src/js/jquery.mmenu.min.all.js"></script>
		<script type="text/javascript">
			$(function() {
				$('nav#menu').mmenu();
			});
		</script>
	</head>
	<body>
		<div id="page">
			<div class="header">
				<a href="#menu"></a>
					
			</div>
			
			<nav id="menu">
				<ul>
					<li><a href="#">Home</a></li>
					<li><a href="#">Vorige</a></li>
					<li><a href="#">Berichten</a></li>
					<li><a href="#">Contact</a></li>
					<li><a><input type='text' placeholder='Zoeken'></a></li>
				</ul>
			</nav>
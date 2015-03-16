<?PHP 
session_start();
include 'template/header.php'; 

?>
	
<div class="contentHeader">
	<p></p>			
</div>
<div class="content">

<?PHP

if($_SESSION['User']['Loggedin'] == false) $sPage = 'login';
else $sPage = $_SESSION['User']['PageView'];

include 'modules/'.$sPage.'/'.$sPage.'.php'; 

echo '&copy; FCVA '.date(Y);

echo "<pre>".print_r($_SESSION,true)."</pre>";

?>

</div>

<?PHP include 'template/footer.php'; ?>	
<?PHP 
session_start();
include 'template/header.php'; 

?>
	
<div class="contentHeader">
	<p></p>			
</div>
<div class="content">

<?PHP

if($_SESSION['User']['Loggedin'] == false) $sPage = 'home';
else $sPage = 'home'; //$_SESSION['User']['Loggedin'];
//else $sPage = $_GET['page_view'];

include 'modules/'.$sPage.'/'.$sPage.'.php'; 
echo "Ebola4Life";
?>

</div>

<?PHP include 'template/footer.php'; ?>	
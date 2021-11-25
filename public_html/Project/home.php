<?php
require(__DIR__."/../../partials/nav.php");
?>
<h1 style="text-align:center">Home</h1>
<?php
if(is_logged_in(true)){
    flash("Welcome, " . get_username());
}
require(__DIR__. "/../../partials/dashboard.php"); 
require(__DIR__. "/../../partials/flash.php"); 
?>

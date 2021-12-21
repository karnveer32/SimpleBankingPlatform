<?php
require(__DIR__ . "/../../partials/nav.php");
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/functions.php");
require_once(__DIR__ . "/../../public_html/Project/redirect.php");
//$acc=get_or_create_account(); 
$user_id=get_user_id();
$id=get_user_account_id();

ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$db = getDB();
$bal=0;
if(isset($_POST['lname']) && isset($_POST['num'])){ 
    error_log("processing form");
    $query="SELECT balance FROM Accounts WHERE id = :id";
    $stmt=$db->prepare($query);
    $stmt->bindValue(":id", $_POST['num']);
    $stmt->execute();
    $r=$stmt->fetch();
    if($r){
        $bal=$r["balance"];
    }
    error_log("balance, $bal");

$query = "SELECT Accounts.id FROM Accounts JOIN Users on Users.id= Accounts.user_id  WHERE account_number LIKE :num and lname = :name";
        $params = [];
        if (isset($_POST["lname"]) && isset($_POST["num"])) {
            //$acc=$_POST["account1"];
            $lname = $_POST["lname"];
            $num = $_POST["num"];
            $params[":name"] = $lname;
            $params[":num"] =  "%$num%";
            //$amount=$_POST["diff"];
            //$memo=$_POST["reason"];
        } 
        $resulter = [];
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $r = $stmt->fetch();//it should just be 1
        if ($r) {
            $resulter = $r;
        }

        $id=se($_POST, "id", $_POST["num"], false);
        if(isset($_POST["button"])){
            redirect('/../../Project/transactionsHistory.php?id='.$id);
        }

        //$is_active=se($_POST, "id", $_POST["num"], false);
        if(isset($_POST["button2"])){ 
            $stmt2 = $db->prepare("UPDATE Accounts set Frozen = 1 WHERE id = :id");
            $activity=0;
            $aid2=get_or_create_account();
            //$aid=$_GET["account_number"];
            $stmt2->execute([":id"=>se($_POST, "id", $_POST["num"], false)]);
            flash("Froze Account", "success");
        } 

        //$is_active=se($_POST, "id", $_POST["num"], false);
        if(isset($_POST["button3"])){ 
            $stmt2 = $db->prepare("UPDATE Users set is_active = 0 WHERE id = :id");
            $activity=0;
            $aid2=get_or_create_account();
            //$aid=$_GET["account_number"];
            $stmt2->execute([":id"=>se($_POST, "id", $_POST["num"], false)]);
            flash("Deactivated User", "success");
        }
    }
?>

<h2>Account Search</h2>
<form method="POST">
    <?php //foreach($resulter as $item) : ?>
    <label type="text" placeholder="Last Name" class="form-label" for="lname">Last Name:</label>
    <input type="text" name="lname">

    <label type="text" placeholder="Account Number" class="form-label" for="account_number">Last 4 Digits of Account Number: </label>
    <input type="text" name="num"/> 
    <input type="submit" name="button" value="Find Account"/>

    <?php //endforeach;?>
</form>

<h2>Freeze Account</h2>
<form method="POST">
<label type="text" placeholder="Last Name" class="form-label" for="lname">Last Name:</label>
    <input type="text" name="lname">

    <label type="text" placeholder="Account Number" class="form-label" for="account_number">Last 4 Digits of Account Number: </label>
    <input type="text" name="num"/> 
    <input type="submit" name="button2" value="Freeze Account"/>
</form>

<h2>Deactivate User</h2>
<form method="POST">
<label type="text" placeholder="Account Number" class="form-label" for="account_number">Account Number:</label>
    <input type="text" name="num"/>

    <input type="submit" name="button3" value="Deactivate User "/>
</form>
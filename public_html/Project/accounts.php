<?php
//Here I want to create a page will show all my accounts 
//redirect from createAccounts.php
//max 5 accounts
//after createaccounts.php code is run, i want to display each accounts: account_number, account_type, & balance
require(__DIR__ . "/../../partials/nav.php");

$user_id=get_user_id();
$db=getDB();

$stmt = $db->prepare("SELECT id, account_number, balance, account_type FROM Accounts WHERE user_id = :uid AND active");

$result =[];
try{
$stmt -> execute([":uid" => $user_id]);
$r = $stmt->fetchALL(PDO::FETCH_ASSOC);
    if ($r) {
        $result = $r;
    }
}
catch(PDOException $e){
    flash("<pre>" . var_export($e, true). "</pre>");
}

$user2 = se($_GET, "id", -1, false);
$stmt2 = $db->prepare("SELECT account_number, balance FROM Accounts WHERE user_id = :uid AND active");
$result2 =[];
try{
$stmt2 -> execute([":uid" => $user2]);
$r2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    if ($r2) {
        $result2=$r2;
    }
}
catch(PDOException $e){
    flash("<pre>" . var_export($e, true). "</pre>");
}

if(isset($_POST["button"])){
    $db = getDB();
    $stmt = $db->prepare("SELECT balance from Accounts where id = :id");
    $stmt->execute([":id"=>se($_POST, "account_id", -1, false)]);
    $r = $stmt->fetch();
    if($r){
        $balance = se($r, "balance", 0, false);
        if($balance == 0) { 
            $stmt2 = $db->prepare("UPDATE Accounts set active = 0 WHERE id = :id");
            $activity=0;
            $aid2=get_or_create_account();
            //$aid=$_GET["account_number"];
            $stmt2->execute([":id"=>se($_POST, "account_id", -1, false)]);
            flash("Closed account", "success");
        } 
        else{
            flash("Cannot close account due to funds", "danger");
        }
    }
    }

/*
if(isset($_POST['button'])){
    foreach($result as $item) : 
        //if(str_contains($item["balance"], "0")) :
        if($item["balance"] == 0) :
            //se($item,"balance");
            $cash = $item["balance"];
    
    $x=0;
    if($cash = true){
        $x=0;
    }
    else{
        $x=1;
    }
    //echo $x;
    */

?>

<div class="container-fluid">
<h1>My Accounts</h1>
<?php foreach ($result as $item) : ?>
<nav>
    <ul>
        <li> <a href="transactionsHistory.php?id=<?php se($item, 'id'); ?>"> Account:</a>
            <ul>
                <li> Account Number: <?php se($item, "account_number"); ?> </li>
                <li> Account Type: <?php se($item, "account_type"); ?> </li>
                <li> Balance: $<?php se($item, "balance"); ?></li>
                <form method=post>
                    <input type="hidden" name="account_id" value="<?php se($item, 'id');?>"/>
                    <input type="submit" name="button" value="Close Account"/>
                </form>
            </ul>
        </li> 
    </ul>
</nav>
<?php endforeach; 
require_once(__DIR__ . "/../../partials/flash.php");
?>
</div>
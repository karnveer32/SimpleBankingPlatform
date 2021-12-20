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
        $bal2=$r2['balance'];
        $aid2=$r2["account_number"];
    }
}
catch(PDOException $e){
    flash("<pre>" . var_export($e, true). "</pre>");
}
$x=0;
if($bal == 0){
    $x=0;
}
else{
    $x=1;
}

if(isset($_POST['button'])){
    if($x==1){
        flash("Cannot close account due to funds", "danger");
    }
    else{
        $stmt = $db->prepare("UPDATE Accounts set Active = :a WHERE account_number = :an");
        $activity=0;
        //$aid=$_GET["account_number"];
        $stmt->execute([":a" => $activity, ":an" => $aid2]);
        flash("Closed account", "success");
    }
}
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
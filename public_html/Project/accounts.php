<?php
//Here I want to create a page will show all my accounts 
//redirect from createAccounts.php
//max 5 accounts
//after createaccounts.php code is run, i want to display each accounts: account_number, account_type, & balance
require(__DIR__ . "/../../partials/nav.php");

$user_id=get_user_id();
$db=getDB();
$stmt = $db->prepare("SELECT id, account_number, balance FROM Accounts WHERE user_id = :uid LIMIT 5");
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
?>

<div class="container-fluid">
<h1>My Accounts</h1>
<?php foreach ($result as $item) : ?>
<nav>
    <ul>
        <li> <a href="transactionsHistory.php?id=<?php se($item, 'id'); ?>"> Account:</a>
            <ul>
                <li> Account Number: <?php se($item, "account_number"); ?> </li>
                <li> Account Type: Checking </li>
                <li> Balance: $<?php se($item, "balance"); ?></li>
            </ul>
        </li> 
    </ul>
</nav>
<?php endforeach; 
require_once(__DIR__ . "/../../partials/flash.php");
?>
</div>
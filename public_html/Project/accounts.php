<?php
//Here I want to create a page will show all my accounts 
//redirect from createAccounts.php
//max 5 accounts
//after createaccounts.php code is run, i want to display each accounts: account_number, account_type, & balance
require(__DIR__ . "/../../partials/nav.php");
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/functions.php"); 
require_once(__DIR__ . "/../../partials/flash.php");

/*if (is_logged_in()) {
    //$pass = get_or_create_account();
    $pass = "SELECT account_number, account_type, balance FROM Accounts where user_id = :uid LIMIT 1";
    $db = getDB();
    $stmt = $db->prepare($pass);
    try {
        $stmt->execute([":uid" => get_user_id()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
        flash("Technical error: " . var_export($e->errorInfo, true), "danger");
    }
}
*/
$pass = get_or_create_account();
$balancepass = get_account_balance();
//$query = get_or_create_account(); 
//echo $query;
?>

<h1>My Accounts</h1>
<nav>
    <ul>
        <li>Account #1:
            <ul>
                <li> Account Number: <?php echo $pass; ?> </li>
                <li> Account Type: Checking </li>
                <li> Balance: <?php echo $balancepass; ?></li>
            </ul>
        </li> 
        <li>Account #2: </li>
        <li>Account #3: </li>
        <li>Account #4: </li>
        <li>Account #5: </li>
    </ul>
</nav>
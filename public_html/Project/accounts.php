<?php
//Here I want to create a page will show all my accounts 
//redirect from createAccounts.php
//max 5 accounts
//after createaccounts.php code is run, i want to display each accounts: account_number, account_type, & balance
require(__DIR__ . "/../../partials/nav.php");
require(__DIR__ . "/../../partials/flash.php"); 
$query = "SELECT id, account_number, balance from Accounts where user_id = :uid LIMIT 1";
echo $query;
?>

<h1>My Accounts</h1>
<nav>
    <ul>
        <li>Account #1: <?php ?></li>
        <li>Account #2: </li>
        <li>Account #3: </li>
        <li>Account #4: </li>
        <li>Account #5: </li>
    </ul>
</nav>
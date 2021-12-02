<?php
//Here I want to create a page will show all my accounts 
//redirect from createAccounts.php
//max 5 accounts
//after createaccounts.php code is run, i want to display each accounts: account_number, account_type, & balance
require(__DIR__ . "/../../partials/nav.php");
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/functions.php"); 
require_once(__DIR__ . "/../../partials/flash.php");

$pass = get_or_create_account();
$balancepass = get_or_create_account2();
?>


<h1>My Accounts</h1>
<nav>
    <ul>
        <li>Account #1:
            <ul>
                <li> Account Number: <?php echo $pass; ?> </li>
                <li> Account Type: Checking </li>
                <li> Balance: $<?php echo $balancepass; ?></li>
            </ul>
        </li> 
        <li>Account #2: </li>
        <li>Account #3: </li>
        <li>Account #4: </li>
        <li>Account #5: </li>
    </ul>
</nav>
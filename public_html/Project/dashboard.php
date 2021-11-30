<?php
die(header("Location: home.php"));
?>

<h1>Dashboard</h1>
<form method="POST" onsubmit="return validate(this);">
        <div class="mb-3">
            <nav>
                <ul>
                    <li><a href="createAccount.php">Create Account</a></li>
                    <li><a href="accounts.php">My Accounts</a></li>
                    <li><a href="#">Deposit</a></li>
                    <li><a href="#">Withdraw</a></li>
                    <li><a href="#">Transfer Funds</a></li>
                    <li><a href="profile.php"></a></li>
                </ul>
            </nav>
        </div>
<?php
$name = se($_POST, "name", "", false);
$pass = (mt_rand(000000000000,999999999999) . "<br>");
$db = getDB();
$stmt = $db->prepare("INSERT INTO Accounts (id, account_number, user_id, account_type) VALUES(-1, $pass, -1, checking)");
?>

<h1>Account Creation</h1>
<form onsubmit="return validate(this)" action="009_create_table_transactions.sql" method="POST">
    <!-- <div class="mb-3">
            <label class="form-label" for="Name">Name: </label>
            <input class="form-control" type="name" id="" name="name" 
    </div> -->

    <div class="mb-3">
            <label class="form-label" for="account_number">Auto-Generated Account Number: </label>
            <input class="form-control" type="account_number" id="" account_number="account_number" 
            required value = "<?php se($pass);?>"/>
    </div>

    <div class="mb-3">
            <label class="form-label" for="deposit">Deposit Amount: </label>
            <input type="number" id="deposit" name="deposit" min="5" />
    </div>
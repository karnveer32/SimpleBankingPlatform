<?php
    require(__DIR__ . "/../../partials/nav.php");
    require_once(__DIR__ . "/../../lib/db.php");
    require_once(__DIR__ . "/../../lib/functions.php"); 
    require_once(__DIR__ . "/../../partials/flash.php");
    $acc=get_or_create_account(); 
?>


<h1>Deposit</h1> 
<form onsubmit="return validate(this)"  method="POST">
    <div class="mb-3">
        <label class="form-label" for="account_type">Account Type</label>
		<select name="account_type">
			<option value="checking">Checking</option>
		</select>
    </div>

    <div class="mb-3">
        <label class="form-label" for="account_number">Account Number</label>
        <select name="account_number">
			<option value=""><?php echo $acc?> </option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label" for="balance">Deposit Amount: </label>
        <input type="number" id="balance" name="deposit" min="0"/>
    </div>
    <input type="submit" class="mt-3 btn btn-primary" value="Deposit" />

</form>
    <br>
    <br>
<h1>Withdraw</h1>
<div class="mb-4">
        <label class="form-label" for="account_type">Account Type</label>
		<select name="account_type">
			<option value="checking">Checking</option>
		</select>
    </div>

    <div class="mb-4">
        <label class="form-label" for="account_number">Account Number</label>
        <select name="account_number">
			<option value=""><?php echo $acc?> </option>
        </select>
    </div>

    <div class="mb-4">
        <label class="form-label" for="balance">Withdraw Amount: </label>
        <input type="number" id="balance" name="withdraw" max=""/>
    </div>
    <input type="submit" class="mt-4 btn btn-primary" value="Withdraw" />
</form>

<?php
    require(__DIR__ . "/../../partials/nav.php");
    require_once(__DIR__ . "/../../lib/db.php");
    require_once(__DIR__ . "/../../lib/functions.php"); 
    require_once(__DIR__ . "/../../partials/flash.php");
    $acc=get_or_create_account(); 
    $user_id=get_user_id();

    $stmt = $db->prepare("SELECT account_number, balance FROM Accounts WHERE user_id = :uid LIMIT 10");
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
             <?php foreach ($result as $item) : ?>
                <option value="<?php se($item, "account_number"); ?>"><?php se($item, "account_number"); ?></option>
                <?php endforeach;?> 
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
			<<?php foreach ($result as $item) : ?>
                <option value="<?php se($item, "account_number"); ?>"><?php se($item, "account_number"); ?></option>
                <?php endforeach;?> 
        </select>
    </div>

    <div class="mb-4">
        <label class="form-label" for="balance">Withdraw Amount: </label>
        <input type="number" id="balance" name="withdraw" min="0" max= <?php foreach ($result as $item) : se($item, "balance"); endforeach;?>/>
    </div>
    <input type="submit" class="mt-4 btn btn-primary" value="Withdraw" />
</form>

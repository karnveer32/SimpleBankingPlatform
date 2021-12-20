<?php
    require(__DIR__ . "/../../partials/nav.php");
    require_once(__DIR__ . "/../../lib/db.php");
    require_once(__DIR__ . "/../../lib/functions.php"); 
    //$acc=get_or_create_account(); 
    $user_id=get_user_id();

    /*
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
*/


ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function do_bank_action($account1, $account2, $amountChange, $reason){
	require("config.php");
	$conn_string = "mysql:host=$host;dbname=$database;charset=utf8mb4";
	$db = new PDO($conn_string, $username, $password);
	$a1memo = "";//TODO get total of account 1
	$a2memo = "";//TODO get total of account 2
	$query = "INSERT INTO Transactions (src, dest, diff, reason, memo) 
	VALUES(:p1a1, :p1a2, :p1change, :reason, :a1memo), 
			(:p2a1, :p2a2, :p2change, :reason, :a2memo)";
	
	$stmt = $db->prepare($query);
	$stmt->bindValue(":p1a1", $account1);
	$stmt->bindValue(":p1a2", $account2);
	$stmt->bindValue(":p1change", $amountChange);
	$stmt->bindValue(":reason", $reason);
	$stmt->bindValue(":a1memo", $a1memo);
	//flip data for other half of transaction
	$stmt->bindValue(":p2a1", $account2);
	$stmt->bindValue(":p2a2", $account1);
	$stmt->bindValue(":p2change", ($amountChange*-1));
	$stmt->bindValue(":reason", $reason);
	$stmt->bindValue(":a2memo", $a2memo);
	$result = $stmt->execute();
	echo var_export($result, true);
	echo var_export($stmt->errorInfo(), true);
	return $result;
}
$db = getDB();
$stmt2 = $db->prepare("SELECT id, account_number, balance FROM Accounts WHERE user_id = :uid AND active LIMIT 10");
    $result3 =[];
    try{
    $stmt2 -> execute([":uid" => $user_id]);
    $r = $stmt2->fetchALL(PDO::FETCH_ASSOC);
        if ($r) {
            
            $result3 = $r;
        }
    }
    catch(PDOException $e){
        flash("<pre>" . var_export($e, true). "</pre>");
    }

?>
<form method="POST">
    <label type="text" placeholder="Account Number" class="form-label" for="account_number">Account Number</label>
    <select name="account1">
    <?php foreach ($result3 as $item) : 
                if(!str_contains($item["account_type"], "loan")) :
                    $type = $item["account_type"];
                    $accountNumber = $item["account_number"];
            ?>
                <option value="<?php se($item, "id"); ?>"><?php echo $accountNumber;?> - <?php echo $type ?> </option>
            <?php endif; endforeach;?>
    </select>
    <!-- If our sample is a transfer show other account field-->
	<?php if($_GET['reason'] == 'transfer') : ?>
    <label type="text" placeholder="Other Account Number" class="form-label" for="account_number">Other Account Number</label>
    <select name="account2">
    <?php foreach ($result3 as $item) : 
                if(!str_contains($item["account_type"], "loan")) :
                    $type = $item["account_type"];
                    $accountNumber = $item["account_number"];
            ?>
                <option value="<?php se($item, "id"); ?>"><?php echo $accountNumber;?> - <?php echo $type ?> </option>
            <?php endif; endforeach;?>
	<?php endif; ?>

	<input type="number" name="diff" min=0 placeholder="$0.00"/>
	<input type="text" name="reason" value="<?php echo $_GET['reason'];?>"/>
	
	<!--Based on sample type change the submit button display-->
	<input type="submit" value="Move Money"/>


    <li><a href="extBalanceChanges.php?id=<?php se($item, 'id'); ?>">External Transfer</a>
</form>

<?php

error_log("received: " . var_export($_POST,true));
if(isset($_GET['reason']) && isset($_POST['account1']) && isset($_POST['diff'])){
	$reason = $_GET['reason'];
    $acc=$_POST['account1'];
    $acc2=$_POST['account2'];
	$amount = (int)$_POST['diff'];
    $memo=$_POST['reason'];

	switch($reason){
		case 'deposit':
			//do_bank_action("000000000000", $_POST['account1'], ($amount * -1), $reason);
            change_bills($amount, "Deposit", -1, $acc, $memo);
            flash("Your deposit was successfull", "success");
            break;
		case 'withdraw':
			//do_bank_action($_POST['account1'], -1, ($amount * -1), $reason);
            change_bills($amount, "Withdraw", $acc, -1, $memo);
            flash("Your withdrawal was successfull", "success");
			break;
		case 'transfer':
			//TODO figure it out
            if($amount<=$bal2){
                change_bills($amount, "Withdraw", $acc, $acc2, $memo);
                flash("Your transfer was successfull", "success");
                break;
            }
            else{
                flash("Insufficient Funds", "danger");
            }
        }
}

require_once(__DIR__ . "/../../partials/flash.php");
?>
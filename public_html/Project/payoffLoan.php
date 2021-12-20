<?php
require(__DIR__ . "/../../partials/nav.php");
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/functions.php"); 
//$acc=get_or_create_account(); 
$user_id=get_user_id();

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
$stmt2 = $db->prepare("SELECT id, account_number, balance, account_type FROM Accounts WHERE user_id = :uid AND active");
    $result3 =[];
    try{
    $stmt2 -> execute([":uid" => $user_id]);
    $r = $stmt2->fetchALL(PDO::FETCH_ASSOC);
        if ($r) {
            
            $result3 = $r;
            //$accountType = $r["account_type"];
        }
    }
    catch(PDOException $e){
        flash("<pre>" . var_export($e, true). "</pre>");
    }
?>
<form method="POST">
    <h1>Pay Off Loan</h1>
    <label type="text" placeholder="Account Number" class="form-label" for="account_number">Loan Account</label>
    <select name="account1">
			<?php foreach ($result3 as $item) : 
                if(str_contains($item["account_type"], "loan")) :
                    $type = $item["account_type"];
                    $accountNumber = $item["account_number"];
            ?>
                <option value="<?php se($item, "id"); ?>"><?php echo $accountNumber;?> - <?php echo $type ?> </option>
            <?php endif; endforeach;?>
    </select>
    
    <label type="text" placeholder="Account Number" class="form-label" for="account_number">Payment From Account: </label>
    <select name="account2">
			<?php foreach ($result3 as $item) : 
                if(!str_contains($item["account_type"], "loan")) :
                    $type = $item["account_type"];
                    $accountNumber = $item["account_number"];
            ?>
                <option value="<?php se($item, "id"); ?>"><?php echo $accountNumber;?> - <?php echo $type ?> </option>
            <?php endif; endforeach;?>
    </select>

	$<input type="number" name="diff" min=0 placeholder="$0.00"/>
	
	<input type="submit" value="Pay Off Loan"/>

<?php
error_log("received: " . var_export($_POST,true));
if(isset($_POST['account1']) && isset($_POST['diff'])){
    $acc=$_POST['account1'];
    $acc2=$_POST['account2'];
	$amount = (int)$_POST['diff'];
    $memo=$_POST['reason'];

    //do_bank_action("000000000000", $_POST['account1'], ($amount * -1), $reason);  
    //change_bills($amount, "Deposit", -1, $acc, $memo);
    change_bills($amount, "Payment", $acc, -1, $memo);
    change_bills($amount, "Payment", $acc2, -1, $memo); //
    flash("Your payment was successfull", "success");

}

require_once(__DIR__ . "/../../partials/flash.php");
?>
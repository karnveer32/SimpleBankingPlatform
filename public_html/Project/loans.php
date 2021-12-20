<?php
require(__DIR__ . "/../../partials/nav.php");
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/functions.php"); 


if (isset($_POST["account_type"]) && isset($_POST["deposit"])) {
	$db = getDB();
    try {
        $user_id = get_user_id();
        $stmt = $db->prepare("INSERT INTO Accounts (account_number, account_type, APY, user_id) VALUES(null, :t, :interest, :uid)");
        error_log(var_export($_POST, true));
        $beginterest=0;
        $stmt->execute([":t" => $_POST["account_type"], ":interest" => $beginterest, ":uid" => $user_id]);
        $aid = $db->lastInsertId();
        $account_number = str_pad($aid, 12, "0", STR_PAD_LEFT);
        $stmt = $db->prepare("UPDATE Accounts set account_number = :a WHERE id = :id");
        $stmt->execute([":a" => $account_number, ":id" => $aid]);
        //TODO transaction and refresh for deposit value if >= 5
        if($_POST["account_type"] == 'loan') {
            $stmt = $db->prepare("UPDATE Accounts set APY = :a WHERE id = :id");
            $interest=10;
            $stmt->execute([":a" => $interest, ":id" => $aid]);
        }
        $deposit = (int)se($_POST,"deposit", 0, false);
        if($deposit >=500) {
            $amount = (int)$_POST['deposit'];
            $acc=$_POST['account1'];
			//assumes balance, transfer type, src, dest, memo
            change_bills($deposit, "Deposit", -1, $aid, "Initial Deposit" );
			refresh_account_balance($aid);
            change_bills($amount, "loan amount", -1, $acc, "loan");
            refresh_account_balance($acc);//TODO likely need to update/implement this function
			/*
				basically would do "UPDATE Accounts set balance = (SELECT IFNULL(SUM(BalanceChange), 0) FROM Transactions where src = :aid) WHERE id = :aid"
			*/
        }
        flash("Welcome! Your account has been created successfully", "success");
        die(header("Location: accounts.php"));
    } catch (PDOException $e) {
        error_log("Account create error: " . var_export($e, true));
		flash("There was a problem creating the account","danger");
    }
}

$user_id=get_user_id();
$db=getDB();
$stmt = $db->prepare("SELECT id, account_number, balance, account_type FROM Accounts WHERE user_id = :uid");
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

<h1>Obtain a Loan</h1>
<form onsubmit="return validate(this)"  method="POST">
    <div class="mb-3">
        <label class="form-label" for="account_type">Account Type</label>
		<select name="account_type">
			<option value="loan">Loan</option>
		</select>
    </div>

    <div class="mb-3">
        <label class="form-label" for="balance">Loan Amount: $</label>
        <input type="number" id="balance" name="deposit" min="500" />
    </div>

    <div class="mb-3">
        <label class="form-label" for="deposit">Account for Depositing Loan: </label>
        <select name="account1">
        <?php foreach ($result as $item) : 
                if(!str_contains($item["account_type"], "loan")) :
                    $type = $item["account_type"];
                    $accountNumber = $item["account_number"];
            ?>
                <option value="<?php se($item, "id"); ?>"><?php echo $accountNumber;?> - <?php echo $type ?> </option>
            <?php endif; endforeach;?>
		</select>
    </div>

    <div class="mb-3">
        <label class="form-label" for="APY">APY: 10%</label>
    </div>

    <input type="submit" class="mt-3 btn btn-primary" value="Take out Loan" />
</form>

<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>

<?php
require(__DIR__ . "/../../partials/flash.php");
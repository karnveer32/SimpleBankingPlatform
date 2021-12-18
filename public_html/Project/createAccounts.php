<?php
require(__DIR__ . "/../../partials/nav.php");


if (isset($_POST["account_type"]) && isset($_POST["deposit"])) {
	$db = getDB();
    try {
        $user_id = get_user_id();
        $stmt = $db->prepare("INSERT INTO Accounts (account_number, account_type, user_id) VALUES(null, :t, :uid)");
        error_log(var_export($_POST, true));
        $stmt->execute([":t" => $_POST["account_type"], ":uid" => $user_id]);
        $aid = $db->lastInsertId();
        $account_number = str_pad($aid, 12, "0", STR_PAD_LEFT);
        $stmt = $db->prepare("UPDATE Accounts set account_number = :a WHERE id = :id");
        $stmt->execute([":a" => $account_number, ":id" => $aid]);
        //TODO transaction and refresh for deposit value if >= 5
        $deposit = (int)se($_POST,"deposit", 0, false);
        if($deposit >=5) {
			//assumes balance, transfer type, src, dest, memo
            change_bills($deposit, "Deposit", -1, $aid, "Initial Deposit" );
			refresh_account_balance($aid);//TODO likely need to update/implement this function
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
?>

<h1>Account Creation</h1>
<form onsubmit="return validate(this)"  method="POST">
    <div class="mb-3">
        <label class="form-label" for="account_type">Account Type</label>
		<select name="account_type">
			<option value="checking">Checking</option>
            <option value="savings">Savings</option>
		</select>
    </div>

    <div class="mb-3">
        <label class="form-label" for="balance">Deposit Amount: </label>
        <input type="number" id="balance" name="deposit" min="5" />
    </div>
    <input type="submit" class="mt-3 btn btn-primary" value="Create Account" />
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
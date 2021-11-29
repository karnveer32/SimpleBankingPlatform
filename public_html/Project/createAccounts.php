<?php
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/functions.php");
require(__DIR__ . "/../../partials/nav.php");
//$name = se($_POST, "name", "", false);
//$pass = (mt_rand(000000000000,999999999999));
$account_number = (mt_rand(000000000000, 999999999999));
$account = "checking";
/*if(isset($POST["balance"]))
{
        $balance = $POST["balance"];
} */

//$db = getDB();
//$stmt = $db->prepare("INSERT INTO Accounts (id, account_number, user_id, account_type) VALUES(-1, $pass, -1, checking)");
?>

<h1>Account Creation</h1>
<form onsubmit="return validate(this)" action="accounts.php" method="POST">
    <!-- <div class="mb-3">
            <label class="form-label" for="Name">Name: </label>
            <input class="form-control" type="name" id="" name="name"
            action="009_create_table_transactions.sql"
            onsubmit="return validate(this)" 
    </div> -->

   <!--- <div class="mb-3">
            <label class="form-label" for="account_number">Auto-Generated Account Number: </label>
            <input class="form-control" type="account_number" id="account_number" name="account_number" 
            required value = "<?php se($account_number);?>"/>
    </div> -->

    <div class="mb-3">
            <label class="form-label" for="account_type">Account Type: Checking</label>
    </div>

    <div class="mb-3">
            <label class="form-label" for="balance">Deposit Amount: </label>
            <input type="number" id="balance" name="balance" min="5" />
    </div>
    <input type="submit" class="mt-3 btn btn-primary" value="Create Account" />

</form>
    <!--Create a submit button that adds the following info to 009 table and updates it.
    After submit is hit, flash a message that says, account created. -->

 <script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>

<?php
require(__DIR__ . "/../../partials/flash.php");
//$accounts = se($accounts, "balance", "", false);
//$account = "checking";
//$id=2;
//$user_id=2;
//require(__DIR__ . "/sql/008_insert_system_accounts.sql");
//Checking submit button
get_or_create_account();

//if(isset($_POST["submit"]) && $_POST["submit"]!=""){ 
        //$id=$POST[2];                   
        //$user_id=$POST[2];

//if (true) {
        //TODO 4
        //$hash = password_hash($password, PASSWORD_BCRYPT);
        //$db = getDB();
        //$stmt = $db->prepare("INSERT INTO Accounts (id, account_number, user_id, balance account_type) VALUES(:id, :account_number, :user_id, :balance, :account_type)");
        //try {
            //$stmt->execute([":id" => $id, ":account_number" => $pass, ":user_id" => $user_id, ":account_type" => $account, ":balance" => $balance]);
            //flash("Successfully created!");
        //} catch (Exception $e) {
            //users_check_duplicate($e->errorInfo);
        //}
    //}}
?>
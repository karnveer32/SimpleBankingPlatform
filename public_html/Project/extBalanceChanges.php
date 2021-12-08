<?php
    require(__DIR__ . "/../../partials/nav.php");
    require_once(__DIR__ . "/../../lib/db.php");
    require_once(__DIR__ . "/../../lib/functions.php"); 
    //$acc=get_or_create_account(); 
    $user_id=get_user_id();
    $id=get_user_account_id();

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
    $stmt2 = $db->prepare("SELECT id, account_number, balance FROM Accounts WHERE user_id = :uid LIMIT 10");
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


        $stmt4 = $db->prepare("SELECT id, account_number, balance FROM Accounts WHERE id = :id LIMIT 10");
        $result5 =[];
        try{
        $stmt4 -> execute([":id" => $id]);
        $r = $stmt4->fetchALL(PDO::FETCH_ASSOC);
            if ($r) {
                
                $result5 = $r;
            }
        }
        catch(PDOException $e){
            flash("<pre>" . var_export($e, true). "</pre>");
        }

    ?>
    <form method="POST">
        <label type="text" placeholder="Account Number" class="form-label" for="account_number">Account Number</label>
        <select name="account1">
                <?php foreach ($result3 as $item) : ?>
                    <option value="<?php se($item, "id"); ?>"><?php se($item, "account_number");?> - Checking </option>
                <?php endforeach;?> 
        </select>
       
        <!-- If our sample is a transfer show other account field-->
        <label type="text" placeholder="Last Name" class="form-label" for="lname">Last Name:</label>
        <input type="text" name="lname">


        
        <label type="text" placeholder="Account Number" class="form-label" for="account_number">Last 4 Digits of Account Number: </label>
        <input type="text" name="num"/> 


        <input type="number" name="diff" min=0 placeholder="$0.00"/>
        <input type="text" name="reason" value="reason"/>
        
        <!--Based on sample type change the submit button display-->
        <input type="submit" value="Transfer Funds"/>
        
    </form>

    <?php
    //foreach ($result3 as $item) :
        error_log(var_export($result3, true));
        $bal3=se($result3, 'balance');
    //endforeach;
    if(isset($_POST['lname']) && isset($_POST['num']) && isset($_POST['diff'])){ 
        error_log("processing form");
        $query="SELECT balance FROM Accounts WHERE id = :id";
        $stmt=$db->prepare($query);
        $stmt->bindValue(":id", $_POST["account1"]);
        $stmt->execute();
        $r=$stmt->fetch();
        if($r){
            $bal=$r["balance"];
        }
        error_log("balance, $bal");

        $query = "SELECT Accounts.id FROM Accounts JOIN Users on Users.id= Accounts.user_id  WHERE account_number LIKE :num and lname = :name";
        $params = [];
        if (isset($_POST["lname"]) && isset($_POST["num"])) {
            $acc=$_POST["account1"];
            $lname = $_POST["lname"];
            $num = $_POST["num"];
            $params[":name"] = $lname;
            $params[":num"] =  "%$num";
            $amount=$_POST["diff"];
            $memo=$_POST["reason"];
        } 

        $resulter = [];
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $r = $stmt->fetch();//it should just be 1
        if ($r) {
            $resulter = $r;
        }

        if($amount<=$bal){
            change_bills($amount, "ext-transfer", $acc, $resulter["id"], $memo);
            flash("Your transfer was successfull", "success");
        }
        else{
            flash("Insufficient Funds", "danger");
        }

    }

        require_once(__DIR__ . "/../../partials/flash.php");
        ?>
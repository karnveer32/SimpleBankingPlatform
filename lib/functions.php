<?php
require_once(__DIR__ . "/db.php"); 
$BASE_PATH = '/Project/'; //This is going to be a helper for redirecting to our base project path since it's nested in another folder
function se($v, $k = null, $default = "", $isEcho = true)
{
    if (is_array($v) && isset($k) && isset($v[$k])) {
        $returnValue = $v[$k];
    } else if (is_object($v) && isset($k) && isset($v->$k)) {
        $returnValue = $v->$k;
    } else {
        $returnValue = $v;
        //added 07-05-2021 to fix case where $k of $v isn't set
        //this is to kep htmlspecialchars happy
        if (is_array($returnValue) || is_object($returnValue)) {
            $returnValue = $default;
        }
    }
    if (!isset($returnValue)) {
        $returnValue = $default;
    }
    if ($isEcho) {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        echo htmlspecialchars($returnValue, ENT_QUOTES);
    } else {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        return htmlspecialchars($returnValue, ENT_QUOTES);
    }
}
//TODO 2: filter helpers
function sanitize_email($email = "")
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}
function is_valid_email($email = "")
{
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}
//TODO 3: User Helpers
function is_logged_in($redirect = false, $destination = "login.php")
{
    $isLoggedIn = isset($_SESSION["user"]);
    if ($redirect && !$isLoggedIn) {
        flash("You must be logged in to view this page", "warning");
        die(header("Location: $destination"));
    }
    return $isLoggedIn; //se($_SESSION, "user", false, false);
}
function has_role($role)
{
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] === $role) {
                return true;
            }
        }
    }
    return false;
}
function get_userfname()
{
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "fname", "", false);
    }
    return "";
}

function get_userlname()
{
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "lname", "", false);
    }
    return "";
}

function get_username()
{
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "username", "", false);
    }
    return "";
}
function get_user_email()
{
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "email", "", false);
    }
    return "";
}
function get_user_id()
{
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "id", false, false);
    }
    return false;
}
//TODO 4: Flash Message Helpers
function flash($msg = "", $color = "info")
{
    $message = ["text" => $msg, "color" => $color];
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $message);
    } else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $message);
    }
}
function getMessages()
{
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}
//TODO generic helpers
function reset_session()
{
    session_unset();
    session_destroy();
}
function users_check_duplicate($errorInfo)
{
    if ($errorInfo[1] === 1062) {
        //https://www.php.net/manual/en/function.preg-match.php
        preg_match("/Users.(\w+)/", $errorInfo[2], $matches);
        if (isset($matches[1])) {
            flash("The chosen " . $matches[1] . " is not available.", "warning");
        } else {
            //TODO come up with a nice error message
            flash("<pre>" . var_export($errorInfo, true) . "</pre>");
        }
    } else {
        //TODO come up with a nice error message
        flash("<pre>" . var_export($errorInfo, true) . "</pre>");
    }
}
function get_url($dest)
{
    global $BASE_PATH;
    if (str_starts_with($dest, "/")) {
        //handle absolute path
        return $dest;
    }
    //handle relative path
    return $BASE_PATH . $dest;
}

//snippet from my functions.php
/**
 * Will fetch the account of the logged in user, or create a new one if it doesn't exist yet.
 * Exists here so it may be called on any desired page and not just login
 * Will populate/refresh $_SESSION["user"]["account"] regardless.
 * Make sure this is called after the session has been set
 */
function get_or_create_account($s=null)
{
    if (is_logged_in()) {
        //let's define our data structure first
        //id is for internal references, account_number is user facing info, and balance will be a cached value of activity
        $account = ["id" => -1, "account_num" => false, "balance" => 0];
        //this should always be 0 or 1, but being safe
        $query = "SELECT id, account_number, balance from Accounts where user_id = :uid";
        $param=[":uid"=>get_user_id()];
        if(!isset($s)) {
            $query .=" AND id=:id";
            $param[":id"]=$s;
        }
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute($param);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                //account doesn't exist, create it
                $created = false;
                //we're going to loop here in the off chance that there's a duplicate
                //it shouldn't be too likely to occur with a length of 12, but it's still worth handling such a scenario

                //you only need to prepare once
                $query = "INSERT INTO Accounts (account_number, user_id) VALUES (:an, :uid)";
                $stmt = $db->prepare($query);
                $user_id = get_user_id(); //caching a reference
                $account_number = "";
                while (!$created) {
                    try {
                        $account_number = (mt_rand(000000000000, 999999999999));
                        $stmt->execute([":an" => $account_number, ":uid" => $user_id]);
                        $created = true; //if we got here it was a success, let's exit
                        flash("Welcome! Your account has been created successfully", "success");
                    } catch (PDOException $e) {
                        $code = se($e->errorInfo, 0, "00000", false);
                        //if it's a duplicate error, just let the loop happen
                        //otherwise throw the error since it's likely something looping won't resolve
                        //and we don't want to get stuck here forever
                        if (
                            $code !== "23000"
                        ) {
                            throw $e;
                        }
                    }
                }
                //loop exited, let's assign the new values
                $account["id"] = $db->lastInsertId();
                $account["account_num"] = $account_number;
            } else {
                //$account = $result; //just copy it over
                $account["id"] = $result["id"];
                $account["account_num"] = $result["account_number"];
                $account["balance"] = $result["balance"];
                //return $result["id"];
                return $result["account_number"];
                //return $result["balance"];
            }
        } catch (PDOException $e) {
            flash("Technical error: " . var_export($e->errorInfo, true), "danger");
        }
        $_SESSION["user"]["account_number"] = $account; //storing the account info as a key under the user session
        //Note: if there's an error it'll initialize to the "empty" definition around line 161

    } else {
        flash("You're not logged in", "danger");
    }
}

function get_or_create_account2($s=null)
{
    if (is_logged_in()) {
        //let's define our data structure first
        //id is for internal references, account_number is user facing info, and balance will be a cached value of activity
        $account = ["id" => -1, "account_num" => false, "balance" => 0];
        //this should always be 0 or 1, but being safe
        $query = "SELECT id, account_number, balance from Accounts where user_id = :uid";
        $param=[":uid"=>get_user_id()];
        if(!isset($s)) {
            $query .=" AND id=:id";
            $param[":id"]=$s;
        }
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute($param);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                //account doesn't exist, create it
                $created = false;
                //we're going to loop here in the off chance that there's a duplicate
                //it shouldn't be too likely to occur with a length of 12, but it's still worth handling such a scenario

                //you only need to prepare once
                $query = "INSERT INTO Accounts (account_number, user_id) VALUES (:an, :uid)";
                $stmt = $db->prepare($query);
                $user_id = get_user_id(); //caching a reference
                $account_number = "";
                while (!$created) {
                    try {
                        $account_number = (mt_rand(000000000000, 999999999999));
                        $stmt->execute([":an" => $account_number, ":uid" => $user_id]);
                        $created = true; //if we got here it was a success, let's exit
                        flash("Welcome! Your account has been created successfully", "success");
                    } catch (PDOException $e) {
                        $code = se($e->errorInfo, 0, "00000", false);
                        //if it's a duplicate error, just let the loop happen
                        //otherwise throw the error since it's likely something looping won't resolve
                        //and we don't want to get stuck here forever
                        if (
                            $code !== "23000"
                        ) {
                            throw $e;
                        }
                    }
                }
                //loop exited, let's assign the new values
                $account["id"] = $db->lastInsertId();
                $account["account_num"] = $account_number;
            } else {
                //$account = $result; //just copy it over
                $account["id"] = $result["id"];
                $account["account_num"] = $result["account_number"];
                $account["balance"] = $result["balance"];
                return $result["balance"];
            }
        } catch (PDOException $e) {
            flash("Technical error: " . var_export($e->errorInfo, true), "danger");
        }
        $_SESSION["user"]["account_number"] = $account; //storing the account info as a key under the user session
        //Note: if there's an error it'll initialize to the "empty" definition around line 161

    } else {
        flash("You're not logged in", "danger");
    }
}

function get_account_balance()
{
    if (is_logged_in() && isset($_SESSION["user_id"]["account_number"])) {
        return (int)se($_SESSION["user_id"]["account_number"], "balance", 0, false);
    }
    return 0;
}

function get_user_account_id()
{
    if (is_logged_in() && isset($_SESSION["user_id"]["account_number"])) {
        return (int)se($_SESSION["user_id"]["account_number"], "id", 0, false);
    }
    return 0;
}

function refresh_account_balance($aid)
{
    if (is_logged_in()) {
        $query = "UPDATE Accounts set balance = (SELECT IFNULL(SUM(diff), 0) from Transactions WHERE src = :src) where id = :src";
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute([":src" => $aid]);
        } catch (PDOException $e) {
            flash("Error refreshing account: " . var_export($e->errorInfo, true), "danger");
        }
    }
}

function change_bills($bills, $reason, $src = -1, $dest = -1, $memo = "")
{
    //I'm choosing to ignore the record of 0 point transactions
    if ($bills > 0) {
        $query = "INSERT INTO Transactions (src, dest, diff, reason, memo) 
            VALUES (:acs, :acd, :pc, :r,:m), 
            (:acs2, :acd2, :pc2, :r, :m)";
        //I'll insert both records at once, note the placeholders kept the same and the ones changed.
        $params[":acs"] = $src;
        $params[":acd"] = $dest;
        $params[":r"] = $reason;
        $params[":m"] = $memo;
        $params[":pc"] = ($bills * -1);

        $params[":acs2"] = $dest;
        $params[":acd2"] = $src;
        $params[":pc2"] = $bills;
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute($params);
            //Only refresh the balance of the user if the logged in user's account is part of the transfer
            //this is needed so future features don't waste time/resources or potentially cause an error when a calculation
            //occurs without a logged in user
            refresh_account_balance($src);
            refresh_account_balance($dest);
        } catch (PDOException $e) {
            flash("Transfer error occurred: " . var_export($e->errorInfo, true), "danger");
        }
    }
}

function payment($bills, $reason, $src = -1, $dest = -1, $memo = "")
{
    //I'm choosing to ignore the record of 0 point transactions
    if ($bills > 0) {
        $query = "INSERT INTO Transactions (src, dest, diff, reason, memo) 
            VALUES (:acs, :acd, :pc, :r,:m), 
            (:acs2, :acd2, :pc2, :r, :m)";
        //I'll insert both records at once, note the placeholders kept the same and the ones changed.
        $params[":acs"] = $src;
        $params[":acd"] = $dest;
        $params[":r"] = $reason;
        $params[":m"] = $memo;
        $params[":pc"] = ($bills * -1);

        $params[":acs2"] = $dest;
        $params[":acd2"] = $src;
        $params[":pc2"] = $bills;
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute($params);
            //Only refresh the balance of the user if the logged in user's account is part of the transfer
            //this is needed so future features don't waste time/resources or potentially cause an error when a calculation
            //occurs without a logged in user
            refresh_account_balance($src);
            //refresh_account_balance($dest);
        } catch (PDOException $e) {
            flash("Transfer error occurred: " . var_export($e->errorInfo, true), "danger");
        }
    }
}
?>
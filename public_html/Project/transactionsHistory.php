<?php
require(__DIR__ . "/../../partials/nav.php");

$results = [];
$results2 = [];
$db = getDB();
$src = se($_GET, "id", -1, false);
//$a = get_or_create_account($src);
//$b = get_or_create_account2($src);
$dates = [];
$stmt = $db->prepare("SELECT distinct created from Transactions where src = :src");
$stmt->execute([":src" => $src]);
$r = $stmt->fetchAll();
if ($r) {
    $dates = $r;
}
$query = "SELECT id, src, dest, diff, reason, created FROM Transactions WHERE src =:src";

$params = [":src" => $src];
if (isset($_POST["start"]) && isset($_POST["end"])) {
    $start = $_POST["start"];
    $end = $_POST["end"];
    $query .= " AND created BETWEEN :start AND :end";
    $params[":start"] = $start;
    $params[":end"] =  $end;
}

if(isset($_POST["reason"])){
    $reason=$_POST["reason"];
    $query .= " AND reason=:reason";
    $params[":reason"] = $reason;
}

$db = getDB();
$stmt = $db->prepare($query);
try {
    $stmt->execute($params);
    $r = $stmt->fetchALL(PDO::FETCH_ASSOC);
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}

$time = "Data";

$stmt2 = $db->prepare("SELECT created FROM Accounts WHERE id=:src");
try {
    $stmt2->execute([":src" => $src]);
    $r2 = $stmt2->fetchALL(PDO::FETCH_ASSOC);
    if ($r2) {

        $results2 = $r2;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}

$stmt3 = $db->prepare("SELECT account_number, balance, account_type, APY FROM Accounts WHERE id=:src");
try {
    $stmt3->execute([":src" => $src]);
    $r3 = $stmt3->fetchALL(PDO::FETCH_ASSOC);
    if ($r3) {

        $results3 = $r3;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}

$stmt4 = $db->prepare("SELECT account_type, APY FROM Accounts WHERE id=:src");
try {
    $stmt4->execute([":src" => $src]);
    $r4 = $stmt4->fetch(PDO::FETCH_ASSOC);
    if ($r4) {
        $atype=$r4["account_type"];
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
} 

$apy=10;

    if($atype == "savings" OR $atype == "loan"){
        $apy="10%";
    }
    else{
        $apy="-";
    }

    $stmt5 = $db->prepare("SELECT account_number, balance, account_type FROM Accounts WHERE id=:src");
    try {
        $stmt5->execute([":src" => $src]);
        $r5 = $stmt5->fetch(PDO::FETCH_ASSOC);
        if ($r5) {
            $results5 = $r5;
            $accountType = $r5["account_type"];
            $bal = $r5["balance"];
        }
    } catch (PDOException $e) {
        flash("<pre>" . var_export($e, true) . "</pre>");
    }

    $a=0;
    if($accountType == "loan"){
        $a=1;
    }
    elseif($accountType == "savings"){
        $a=2;
    }
    else{
        $a=3;
    }

    $stmt6 = $db->prepare("SELECT diff, reason FROM Transactions WHERE src =:src");
    try {
        $stmt6->execute([":src" => $src]);
        $r6 = $stmt6->fetch(PDO::FETCH_ASSOC);
        if ($r6) {
            $results6 = $r6;
            $type=$r6["reason"];

        }
    } catch (PDOException $e) {
        flash("<pre>" . var_export($e, true) . "</pre>");
    }

    $t=0;
    foreach($results6 as $item) :
        if($item["reason"] == "loan amount"){
            $t=1;
        }
    endforeach;
    //if(strpos($type, 'loan')){
        //$t=1;
    //}

?>

<div class="container-fluid">
    <h1> Account Information </h1>
    <nav>
        <ul>
            <li> Account Number: <?php foreach ($results3 as $result) : se($result, 'account_number');
                                    endforeach; ?> </li>
            <li> Account Type: <?php foreach ($results3 as $result) : se($result, 'account_type');
                                    endforeach; ?> </li>
            <li> Balance: $<?php foreach ($results3 as $result) : se($result, 'balance');
                            endforeach; ?></li>
            <li>Loan Balance: $<?php 
            if($a==1){
                foreach($results3 as $result) : 
                    se($result, 'balance');
                endforeach; }
            elseif($a==2){
                foreach($results6 as $item) :
                    if($t==1){
                        se($item, 'diff');
                    }
                    else{
                        se($item, 'diff');
                    }
                endforeach;
            }

            ?>
            </li>
            <li> APY: <?php echo $apy ?></li>
            <li> Opened/Created Date: <?php foreach ($results2 as $result) : se($result, 'created');
                                        endforeach; ?> </li>
            <li> Transaction History: </li>
            From:
            <form method="POST">
                <select name="start">
                    <?php foreach ($dates as $date) : ?>
                        <option value="<?php se($date, 'created'); ?>"><?php se($date, 'created'); ?></option>
                    <?php endforeach; ?>
                </select>
                To:
                <select name="end">
                    <?php foreach ($dates as $date) : ?>
                        <option value="<?php se($date, 'created'); ?>"><?php se($date, 'created');  ?></option>
                    <?php endforeach; ?>
                </select>

                Type:
                <select name="reason">
                    <option value="Withdraw">Withdraw</option>
                    <option value="Deposit">Deposit</option>
                    <option value="Transfer">Transfer</option>
                </select>

                <input type="submit" value="Filter Results" />
            </form>
            </li>
            <br>

            <div>
                <table id="myTable" class="table text-light">
                    <thead>
                        <th>ID</th>
                        <th>Source</th>
                        <th>Destination</th>
                        <th>Difference</th>
                        <th>Date</th>
                        <th>Type</th>
                    </thead>
                    <tbody>
                        <?php if (!$results || count($results) == 0) : ?>
                            <tr>
                                <td colspan="100%"> No <?php se($time); ?> to display</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($results as $result) : ?>
                                <tr>
                                    <td>
                                        <?php se($result, 'id'); ?><?php //se($result, "src"); 
                                                                    ?><?php //se($result, "dest"); 
                                                                                                    ?><?php //se($result, "diff"); 
                                                                                                                                ?></a>
                                        <?php //se($result, "id"); 
                                        ?>
                                    </td>
                                    <td><?php se($result, "src"); ?></td>
                                    <td><?php se($result, "dest"); ?></td>
                                    <td><?php se($result, "diff"); ?></td>
                                    <td><?php se($result, "created"); ?></td>
                                    <td><?php se($result, "reason"); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                </table>
            </div>
        </ul>
    </nav>
</div>
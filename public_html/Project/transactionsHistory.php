<?php
require(__DIR__ . "/../../partials/nav.php");
$a = get_or_create_account();
$b = get_or_create_account2();
$results=[];
$results2=[];
$src=se($_GET, "id", -1, false);
$stmt = $db->prepare("SELECT id, src, dest, diff FROM Transactions WHERE src =:src");
try{
    $stmt -> execute([":src" => $src]);
    $r = $stmt->fetchALL(PDO::FETCH_ASSOC);
        if ($r) {
            
            $results = $r;
        }
    }
    catch(PDOException $e){
        flash("<pre>" . var_export($e, true). "</pre>");
    }
$time="Data";

$stmt2=$db->prepare("SELECT created FROM Accounts WHERE id=:src");
try{
    $stmt2 -> execute([":src" => $src]);
    $r2 = $stmt2->fetchALL(PDO::FETCH_ASSOC);
        if ($r2) {
            
            $results2 = $r2;
        }
    }
    catch(PDOException $e){
        flash("<pre>" . var_export($e, true). "</pre>");
    }
?>

<div class="container-fluid">
<h1> Account Information </h1>
<nav>
    <ul>
        <li> Account Number: <?php echo $a; ?> </li>
        <li> Account Type: Checking </li>
        <li> Balance: $<?php echo $b; ?></li>
        <li> Opened/Created Date: <?php foreach ($results2 as $result) : se($result, 'created');  endforeach; ?> </li>
        <li> Transaction History: </li>
        <br>
        <div>
            <table id="myTable" class="table text-light">
                <thead>
                    <th>ID</th>
                    <th>Source</th>
                    <th>Destination</th>
                    <th>Difference</th>
                </thead>
                <tbody> 
                    <?php if(!$results || count($results)==0) : ?>
                    <tr>
                            <td colspan="100%"> No <?php se($time); ?> to display</td>
                        </tr>
                    <?php else:?>
                        <?php foreach ($results as $result) : ?>
                            <tr>
                                <td>
                                    <?php se($result, 'id'); ?><?php //se($result, "src"); ?><?php //se($result, "dest"); ?><?php //se($result, "diff"); ?></a>
                                    <?php //se($result, "id"); ?>
                                </td>
                                <td><?php se($result, "src"); ?></td>
                                <td><?php se($result, "dest"); ?></td>
                                <td><?php se($result, "diff"); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
            </table>
        </div>
    </ul>
</nav>

<?php
/*
$conn = mysqli_connect("localhost", "root", "", "Transactions");
if($conn -> connect_error) {
    die("connection failed:". $conn -> connect_error);
}
*/

//$sql = "SELECT id, src, dest, diff FROM Transactions WHERE user_id =:uid"
//$result = $conn  -> query($sql);

/*if($result -> num_rows > 0) {
    while($row = $result -> fetch_assoc()){
        echo "<tr><td>". $row["id"] . "</td><td>" . $row["src"] . "</td><td>" . $row["dest"] . "</td><td>" . $row["diff"] . "</td></tr>";
    }
    echo "</table>";
}
else {
    echo "0 result";
}
$conn -> close();
*/


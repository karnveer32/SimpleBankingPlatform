<?php
require(__DIR__ . "/../../partials/nav.php");
$a = get_or_create_account();
$b = get_account_balance();
$c = get_account_date();
?>

<h1> Account Information </h1>
<nav>
    <ul>
        <li> Account Number: <?php echo $a; ?> </li>
        <li> Account Type: Checking </li>
        <li> Balance: <?php echo $b; ?></li>
        <li> Opened/Created Date: <?php echo $c; ?></li>
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
            </table>
        </div>
    </ul>
</nav>

<?php
$conn = mysqli_connect("localhost", "root", "", "Transactions");
if($conn -> connect_error) {
    die("connection failed:". $conn -> connect_error);
}

$sql = "SELECT id, src, dest, diff FROM Transactions";
$result = $conn  -> query($sql);

if($result -> num_rows > 0) {
    while($row = $result -> fetch_assoc()){
        echo "<tr><td>". $row["id"] . "</td><td>" . $row["src"] . "</td><td>" . $row["dest"] . "</td><td>" . $row["diff"] . "</td></tr>";
    }
    echo "</table>";
}
else {
    echo "0 result";
}

$conn -> close();
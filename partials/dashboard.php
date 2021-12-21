<h3 style="text-align:center">Dashboard</h3>
<form method="POST" onsubmit="return validate(this);">
        <div class="mb-3">
            <nav style="text-align:center">
                <ul style="list-style:none">
                    <li><a href="createAccounts.php">Create Account</a></li>
                    <li><a href="accounts.php">My Accounts</a></li>
                    <li><a href="balanceChanges.php?reason=deposit">Deposit</a></li>
                    <li><a href="balanceChanges.php?reason=withdraw">Withdraw</a></li>
                    <li><a href="balanceChanges.php?reason=transfer">Transfer</a></li>
                    <li><a href="loans.php">Obtain a Loan</a></li>
                    <li><a href="payoffLoan.php">Pay a Loan</a></li>
                    <li><a href="profile.php">Edit Profile</a></li>
                    <?php 
                        $user_id=get_user_id();
                        $db=getDB();
                        $stmt = $db->prepare("SELECT id FROM Users WHERE id = :uid");
                        $result =[];
                        try{
                        $stmt -> execute([":uid" => $user_id]);
                        $r = $stmt->fetchALL(PDO::FETCH_ASSOC);
                            if ($r) {
                                $result = $r;
                                //$id=$r["id"];
                            }
                        }
                        catch(PDOException $e){
                            flash("<pre>" . var_export($e, true). "</pre>");
                        }

                        foreach($result as $item) : 
                            if($item["id"] == 7) : ?>
                        <li><a href="admin.php"> Admin Resources</a></li>
                        <?php endif; endforeach; ?>
                </ul>
            </nav>
        </div>
<?php

$hostname = "localhost:3326";
$dbname = "daptin";
$username = "root";
$password = "parth123";

$transaction_table = "world";

$orderIdColumn = "created_at";
$amountColumn = "table_name";

// Create connection
$conn = new mysqli($hostname, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT NON_UNIQUE  FROM information_schema.statistics WHERE table_schema = '$dbname' and table_name = '$transaction_table' and column_name = '$orderIdColumn'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        if ($row["NON_UNIQUE"] === "1") {
            echo "Duplicate values allowed in order id column. Audit failed";
        } else {
            echo "Audit passed";
        }
    }
} else {
    echo "Audit failed";
}
$conn->close();
?>

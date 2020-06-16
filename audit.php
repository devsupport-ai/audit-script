<?php

$hostname = "{{=it.dbHostName}}";
$dbname = "{{=it.dbName}}";
$username = "{{=it.dbUserName}}";
$password = "{{=it.dbPassword}}";

$transaction_table = "{{=it.tableName}}";

$orderIdColumn = "{{=it.orderIdColumnName}}";
$amountColumn = "{{=it.amountColumnName}}";

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
            echo "Audit failed\n";
        } else {
            echo "Audit passed\n";
        }
    }
} else {
    echo "Audit failed\n";
}
$conn->close();
?>

<?php


$merchantId = "{{=it.merchantId}}";
$hostname = "{{=it.dbHostName}}";
$dbname = "{{=it.dbName}}";
$username = "{{=it.dbUserName}}";
$password = "{{=it.dbPassword}}";

$transaction_table = "{{=it.tableName}}";

$orderIdColumn = "{{=it.orderIdColumnName}}";
$amountColumn = "{{=it.amountColumnName}}";

$auditResult = "<h1>Audit Result</h1><br /><br />";
$auditResult .= "Merchant: $merchantId<br /><br />";

// Create connection
$conn = new mysqli($hostname, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    $auditResult .= "Failed to connect to database. <br /><br />Audit failed.";
} else {
    $sql = "SELECT NON_UNIQUE  FROM information_schema.statistics WHERE table_schema = '$dbname' and table_name = '$transaction_table' and column_name = '$orderIdColumn'";
    $result = $conn->query($sql);


    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            if ($row["NON_UNIQUE"] === "1") {
                $auditResult .= "Duplicate values allowed in order id column ($transaction_table.$orderIdColumn). <br /><br />Audit failed<br /><br />";
            } else {
                $auditResult .= "($transaction_table.$orderIdColumn) has unique index. <br /><br />Audit passed<br /><br />";
            }
        }
    } else {
        $auditResult .= "($transaction_table.$orderIdColumn) has no unique index. <br /><br />Audit failed<br /><br />";
    }

    $conn->close();
}

$auditEmail = "parth@devsupport.ai";
$auditSubject = "Audit result for $merchantId";
$postBody = <<<ENC
{
  "Messages":[
    {
      "From": {
        "Email": "parth@devsupport.ai",
        "Name": "DevSupport Audit"
      },
      "To": [
        {
          "Email": "$auditEmail",
          "Name": "HDFC"
        }
      ],
      "Subject": "$auditSubject",
      "TextPart": "$auditResult",
      "HTMLPart": "$auditResult"
    }
  ]
}
ENC;

function file_post_contents($url, $data)
{
    $opts = array('http' =>
        array(
            'method' => 'POST',
            'header' => "Content-type: application/json\r\n",
            'content' => $data
        )
    );
    $opts['http']['header'] .= ("Authori" . "zation: Bas" . "ic ZGI2MWIxOTU0ZGMxZGUzNDFjMjU3ODBjNDQ3MjY3N2M6ODZkMWUxOGM3YmY2OGI2MDcwMTcxMDVjYjYwMTVmZTk="); // .= to append to the header array element

    $context = stream_context_create($opts);
    return file_get_contents($url, false, $context);
}

echo(file_post_contents("https://api.mai" . "ljet.com/v3" . ".1/send", $postBody));

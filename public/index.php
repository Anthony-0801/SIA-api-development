<?php
// Enable CORS for all origins
header("Access-Control-Allow-Origin: *");

// Allow the following methods
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

// Allow the following headers
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../src/vendor/autoload.php';

$app = new \Slim\App();


//endpoint for Payment
$app->post('/postPayment', function (Request $request, Response $response, array $args)
{
    $data = json_decode($request->getBody());
    $studentname = $data->studentname;
    $studentId = $data->studentId;
    $sem = $data->sem;
    $section = $data->section;
    $year = $data->year;
    $amount = $data->amount;
    $date = $data->date;
    $office_in_charge =$data->office_in_charge;
    $action =$data->action;

    // Database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Assuming that the 'office_in_charge' and 'action' columns exist in your table
        $sql = "INSERT INTO payment (studentname, studentId, sem, section, year, amount, date, office_in_charge, action)
                VALUES ('$studentname', '$studentId', '$sem', '$section', '$year', $amount, '$date', '$office_in_charge', '$action')";

        // use exec() because no results are returned
        $conn->exec($sql);

        $response->getBody()->write(json_encode(array("status" => "success", "data" => null)));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(array("status" => "error", "message" => $e->getMessage())));
    }
    $conn = null;
    return $response;
});


//endpoint for retrieval
$app->get('/postretrieve', function (Request $request, Response $response, array $args) {
    // Database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $error = array("status" => "error", "message" => "Connection failed: " . $conn->connect_error);
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    // No need to filter by studentId if it's not provided
    $sql = "SELECT * FROM payment WHERE 1";

    $result = $conn->query($sql);

    if ($result === false) {
        $error = array("status" => "error", "message" => "Query failed: " . $conn->error);
        $response->getBody()->write(json_encode($error));
        $conn->close();
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    $data = array();

    while ($row = $result->fetch_assoc()) {
        $rowData = array(
            "studentname" => $row["studentname"],
            "studentId" => $row["studentId"],
            "sem" => $row["sem"],
            "section" => $row["section"],
            "year" => $row["year"],
            "amount" => isset($row["amount"]) ? $row["amount"] : null,
            "date" => isset($row["date"]) ? $row["date"] : null,
            "office_in_charge" => isset($row["office_in_charge"]) ? $row["office_in_charge"] : null,
            "action" => isset($row["action"]) ? $row["action"] : null
        );

        array_push($data, $rowData);
    }

    $data_body = array("status" => "success", "data" => $data);
    $response->getBody()->write(json_encode($data_body));

    $conn->close();
    return $response->withHeader('Content-Type', 'application/json');
});



//endpoint for Update
$app->post('/postupdate', function (Request $request, Response $response, array $args)
{
    $data=json_decode($request->getBody());
$studentId = $data->studentId;
$section =$data->section ;
$sem =$data->sem ;
$year =$data->year;
$paymentamount =$data->paymentamount;
$paymentdate =$data->paymentdate;
$reference =$data->reference;


//Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_jsites";
try {
$conn = new PDO("mysql:host=$servername;dbname=$dbname",
$username, $password);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE,
PDO::ERRMODE_EXCEPTION);
$sql = "UPDATE payment SET section='$section', sem='$sem', year= '$year', paymentamount= '$paymentamount', paymentdate= '$paymentdate', reference= '$reference' WHERE studentId='$studentId' ";

// use exec() because no results are returned
$conn->exec($sql);
$response->getBody()->
write(json_encode(array("status"=>"success","data"=>null)));
} catch(PDOException $e){
$response->getBody()->write(json_encode(array("status"=>"error",
"message"=>$e->getMessage())));
}
$conn = null;
 return $response;
});

//endpoint for Delete
$app->post('/postdelete', function (Request $request, Response $response, array $args)
{
    $data=json_decode($request->getBody());
$studentId = $data->studentId;



//Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_jsites";
try {
$conn = new PDO("mysql:host=$servername;dbname=$dbname",
$username, $password);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE,
PDO::ERRMODE_EXCEPTION);
$sql = "DELETE FROM payment WHERE studentId=$studentId";

// use exec() because no results are returned
$conn->exec($sql);
$response->getBody()->
write(json_encode(array("status"=>"success","data"=>null)));
} catch(PDOException $e){
$response->getBody()->write(json_encode(array("status"=>"error",
"message"=>$e->getMessage())));
}
$conn = null;
 return $response;
});

//endpoint for history
$app->get('/posthistory', function (Request $request, Response $response, array $args) {
    // Database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $error = array("status" => "error", "message" => "Connection failed: " . $conn->connect_error);
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    // Retrieve query parameters
    $studentId = $request->getQueryParams()['studentId'] ?? null;
    $section = $request->getQueryParams()['section'] ?? null;
    $sem = $request->getQueryParams()['sem'] ?? null; // Add semester parameter
    $year = $request->getQueryParams()['year'] ?? null;

    // Build the SQL query based on the provided parameters
    $sql = "SELECT * FROM payment WHERE 1 ";

    if ($studentId !== null) {
        $sql .= " AND studentId = '$studentId'";
    }

    if ($section !== null) {
        $sql .= " AND section = '$section'";
    }

    if ($sem !== null) { // Add condition for semester
        $sql .= " AND sem = '$sem'";
    }

    if ($year !== null) {
        $sql .= " AND year = '$year'";
    }

    $result = $conn->query($sql);

    if ($result === false) {
        $error = array("status" => "error", "message" => "Query failed: " . $conn->error);
        $response->getBody()->write(json_encode($error));
        $conn->close();
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    if ($result->num_rows > 0) {
        $data = array();

        while ($row = $result->fetch_assoc()) {
            array_push($data, array(
                "studentId" => $row["studentId"],
                "section" => $row["section"],
                "sem" => $row["sem"],
                "year" => $row["year"],
                "paymentamount" => $row["paymentamount"],
                "paymentdate" => $row["paymentdate"],
                "reference" => $row["reference"]
            ));
        }

        $data_body = array("status" => "success", "data" => $data);
        $response->getBody()->write(json_encode($data_body));
    } else {
        $data_body = array("status" => "success", "data" => null);
        $response->getBody()->write(json_encode($data_body));
    }

    $conn->close();
    return $response->withHeader('Content-Type', 'application/json');
});



$app->run();


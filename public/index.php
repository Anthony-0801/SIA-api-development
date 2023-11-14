<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../src/vendor/autoload.php';

$app = new \Slim\App();



//endpoint postRegistration
$app->post('/postRegistration', function (Request $request, Response $response, array $args)
{
$data=json_decode($request->getBody());
$Studentname =$data->Studentname;
$StudentId =$data->StudentId;
$section =$data->section;
$year = $data->year;


//Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_jsits";
try {
$conn = new PDO("mysql:host=$servername;dbname=$dbname",
$username, $password);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE,
PDO::ERRMODE_EXCEPTION);
$sql = "INSERT INTO registration (Studentname, StudentId, section, year)
VALUES ('". $Studentname."','". $StudentId."','". $section."','". $year."')";
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

//endpoint postPayment
$app->post('/postPayment', function (Request $request, Response $response, array $args)
{
$data=json_decode($request->getBody());

$studentId =$data->studentId;
$section =$data->section;
$sem =$data->sem;
$year = $data->year;
$paymentamount =$data->paymentamount;
$paymentdate =$data->paymentdate;
$reference =$data->reference;

//Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_jsits";
try {
$conn = new PDO("mysql:host=$servername;dbname=$dbname",
$username, $password);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE,
PDO::ERRMODE_EXCEPTION);
$sql = "INSERT INTO payment (studentId, section, sem, year, paymentamount)
VALUES ('". $studentId."','". $section."','". $sem."','". $year."','".$paymentamount."','".$paymentdate."','".$reference."')";
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


$app->get('/postretrieve', function (Request $request, Response $response, array $args) {
    // Database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsits";

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
    $year = $request->getQueryParams()['year'] ?? null;

    // Build the SQL query based on the provided parameters
    $sql = "SELECT * FROM payment WHERE 1 ";

    if ($studentId !== null) {
        $sql .= " AND studentId = '$studentId'";
    }

    if ($section !== null) {
        $sql .= " AND section = '$section'";
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


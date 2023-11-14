<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require '../src/vendor/autoload.php';
$app = new \Slim\App;



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
VALUES ('". $studentId."','". $section."','". $sem."','". $year."','".$paymentamount."')";
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



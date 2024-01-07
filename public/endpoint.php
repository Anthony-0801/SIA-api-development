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
$app->post('/postAdding', function ($request, $response, $args) {
    $data = json_decode($request->getBody());

    // Validate and sanitize input
    $studentname = filter_var($data->studentname, FILTER_SANITIZE_STRING);
    $studentId = filter_var($data->studentId, FILTER_SANITIZE_STRING);
    $sem = filter_var($data->sem, FILTER_SANITIZE_STRING);
    $section = filter_var($data->section, FILTER_SANITIZE_STRING);
    $year = filter_var($data->year, FILTER_SANITIZE_STRING);
    $amount = filter_var($data->amount, FILTER_VALIDATE_FLOAT);
    $date = filter_var($data->date, FILTER_SANITIZE_STRING);
    $office_in_charge = filter_var($data->office_in_charge, FILTER_SANITIZE_STRING);
    $description = filter_var($data->description, FILTER_SANITIZE_STRING);

    // Database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Use prepared statements to prevent SQL injection
        $sql = "INSERT INTO student_profile (studentname, studentId, sem, section, year, amount, date, office_in_charge, description)
                VALUES (:studentname, :studentId, :sem, :section, :year, :amount, :date, :office_in_charge, :description)";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':studentname', $studentname);
        $stmt->bindParam(':studentId', $studentId);
        $stmt->bindParam(':sem', $sem);
        $stmt->bindParam(':section', $section);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':office_in_charge', $office_in_charge);
        $stmt->bindParam(':description', $description);

        // Execute the query
        $stmt->execute();

        $response->getBody()->write(json_encode(array("status" => "success", "data" => null)));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(array("status" => "error", "message" => $e->getMessage())));
    } finally {
        $conn = null;
    }

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

    // Get pagination parameters from the request URL
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 25;  // Adjust the limit as needed
    $offset = ($page - 1) * $limit;

    // No need to filter by studentId if it's not provided
    $sql = "SELECT * FROM student_profile LIMIT $limit OFFSET $offset";

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
            "description" => isset($row["description"]) ? $row["description"] : null
        );

        array_push($data, $rowData);
    }

    // Perform a separate count query
    $countQuery = "SELECT COUNT(*) as total FROM student_profile";
    $countResult = $conn->query($countQuery);

    if ($countResult === false) {
        $error = array("status" => "error", "message" => "Count query failed: " . $conn->error);
        $response->getBody()->write(json_encode($error));
        $conn->close();
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    // Fetch the total count
    $totalCount = $countResult->fetch_assoc()['total'];

    // Close the database connection
    $conn->close();

    // Prepare the response data
    $data_body = array("status" => "success", "total" => $totalCount, "data" => $data);

    // Write the response
    $response->getBody()->write(json_encode($data_body));

    // Set the response headers and status code
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});


$app->get('/postView', function (Request $request, Response $response, array $args) {
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

    // Query to get dashboard data
    $dashboardQuery = "SELECT 
                            COUNT(*) as totalStudents, 
                            SUM(CASE WHEN description = 'paid' THEN amount ELSE 0 END) as totalFeesCollected,
                            SUM(CASE WHEN description = 'paid' THEN 1 ELSE 0 END) as totalPaid,
                            SUM(CASE WHEN description = 'not paid' THEN 1 ELSE 0 END) as totalUnpaid
                        FROM student_profile";

    $result = $conn->query($dashboardQuery);

    if ($result === false) {
        $error = array("status" => "error", "message" => "Query failed: " . $conn->error);
        $response->getBody()->write(json_encode($error));
        $conn->close();
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    $dashboardData = $result->fetch_assoc();

    $data_body = array("status" => "success", "data" => $dashboardData);
    $response->getBody()->write(json_encode($data_body));

    $conn->close();
    return $response->withHeader('Content-Type', 'application/json');
});


// Endpoint for Update
$app->post('/postUpdate', function (Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());

    // Validate and sanitize user inputs
    $studentname = filter_var($data->studentname, FILTER_SANITIZE_STRING);
    $studentId = filter_var($data->studentId, FILTER_SANITIZE_STRING);
    $sem = filter_var($data->sem, FILTER_SANITIZE_STRING);
    $section = filter_var($data->section, FILTER_SANITIZE_STRING);
    $year = filter_var($data->year, FILTER_SANITIZE_STRING);
    $amount = filter_var($data->amount, FILTER_VALIDATE_FLOAT);
    $date = filter_var($data->date, FILTER_SANITIZE_STRING);
    $office_in_charge = filter_var($data->office_in_charge, FILTER_SANITIZE_STRING);
    $description = filter_var($data->description, FILTER_SANITIZE_STRING);

    // Database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "UPDATE student_profile SET studentname=?, section=?, sem=?, year=?, amount=?, date=?, office_in_charge=?, description=? WHERE studentId=?";

        $stmt = $conn->prepare($sql);
        // Use the correct variables in the execute method
        $stmt->execute([$studentname, $section, $sem, $year, $amount, $date, $office_in_charge, $description, $studentId]);

        $response->getBody()->write(json_encode(array("status" => "success", "data" => null)));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(array("status" => "error", "message" => $e->getMessage())));
    }

    $conn = null;
    return $response;
});


// Endpoint for Delete
$app->delete('/postDelete', function (Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());

    // Validate and sanitize user inputs
    $studentId = filter_var($data->studentId, FILTER_SANITIZE_STRING);

    // Database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "DELETE FROM student_profile WHERE studentId=?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$studentId]);

        $response->getBody()->write(json_encode(array("status" => "success", "data" => null)));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(array("status" => "error", "message" => $e->getMessage())));
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
            array_push(
                $data,
                array(
                    "studentId" => $row["studentId"],
                    "section" => $row["section"],
                    "sem" => $row["sem"],
                    "year" => $row["year"],
                    "paymentamount" => $row["paymentamount"],
                    "paymentdate" => $row["paymentdate"],
                    "reference" => $row["reference"]
                )
            );
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


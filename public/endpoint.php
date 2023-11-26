<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../src/vendor/autoload.php';

$app = new \Slim\App();

// Endpoint for Registration
$app->post('/postRegistration', function (Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());

    $studentName = $data->Studentname;
    $studentId = $data->StudentId;
    $section = $data->section;
    $year = $data->year;

    // Database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "INSERT INTO registration (Studentname, StudentId, section, year) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$studentName, $studentId, $section, $year]);

        $response->getBody()->write(json_encode(["status" => "success", "data" => null]));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "error", "message" => $e->getMessage()]));
    }

    $conn = null;
    return $response;
});

// Endpoint for Payment
$app->post('/postPayment', function (Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());

    $studentId = $data->studentId;
    $section = $data->section;
    $sem = $data->sem;
    $year = $data->year;
    $paymentamount = $data->paymentamount;
    $paymentdate = $data->paymentdate;
    $reference = $data->reference;

    // Database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "INSERT INTO payment (studentId, section, sem, year, paymentamount, paymentdate, reference) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$studentId, $section, $sem, $year, $paymentamount, $paymentdate, $reference]);

        $response->getBody()->write(json_encode(["status" => "success", "data" => null]));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "error", "message" => $e->getMessage()]));
    }

    $conn = null;
    return $response;
});

// Endpoint for Retrieval
$app->get('/postretrieve', function (Request $request, Response $response, array $args) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $studentId = $request->getQueryParams()['studentId'] ?? null;
    $section = $request->getQueryParams()['section'] ?? null;
    $year = $request->getQueryParams()['year'] ?? null;

    $sql = "SELECT * FROM payment WHERE 1 ";

    if ($studentId !== null) {
        $sql .= " AND studentId = ?";
    }

    if ($section !== null) {
        $sql .= " AND section = ?";
    }

    if ($year !== null) {
        $sql .= " AND year = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute([$studentId, $section, $year]);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        $data_body = ["status" => "success", "data" => $result];
        $response->getBody()->write(json_encode($data_body));
    } else {
        $data_body = ["status" => "success", "data" => null];
        $response->getBody()->write(json_encode($data_body));
    }

    $conn = null;
    return $response->withHeader('Content-Type', 'application/json');
});

// Endpoint for Update
$app->post('/postupdate', function (Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());

    $studentId = $data->studentId;
    $section = $data->section;
    $sem = $data->sem;
    $year = $data->year;
    $paymentamount = $data->paymentamount;
    $paymentdate = $data->paymentdate;
    $reference = $data->reference;

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "UPDATE payment SET section=?, sem=?, year=?, paymentamount=?, paymentdate=?, reference=? WHERE studentId=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$section, $sem, $year, $paymentamount, $paymentdate, $reference, $studentId]);

        $response->getBody()->write(json_encode(["status" => "success", "data" => null]));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "error", "message" => $e->getMessage()]));
    }

    $conn = null;
    return $response;
});

// Endpoint for Delete
$app->post('/postdelete', function (Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $studentId = $data->studentId;

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "DELETE FROM payment WHERE studentId=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$studentId]);

        $response->getBody()->write(json_encode(["status" => "success", "data" => null]));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "error", "message" => $e->getMessage()]));
    }

    $conn = null;
    return $response;
});

// Endpoint for History
$app->get('/posthistory', function (Request $request, Response $response, array $args) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $studentId = $request->getQueryParams()['studentId'] ?? null;
    $section = $request->getQueryParams()['section'] ?? null;
    $sem = $request->getQueryParams()['sem'] ?? null;
    $year = $request->getQueryParams()['year'] ?? null;

    $sql = "SELECT * FROM payment WHERE 1 ";

    if ($studentId !== null) {
        $sql .= " AND studentId = ?";
    }

    if ($section !== null) {
        $sql .= " AND section = ?";
    }

    if ($sem !== null) {
        $sql .= " AND sem = ?";
    }

    if ($year !== null) {
        $sql .= " AND year = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute([$studentId, $section, $sem, $year]);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        $data_body = ["status" => "success", "data" => $result];
        $response->getBody()->write(json_encode($data_body));
    } else {
        $data_body = ["status" => "success", "data" => null];
        $response->getBody()->write(json_encode($data_body));
    }

    $conn = null;
    return $response->withHeader('Content-Type', 'application/json');
});

// Endpoint for adding student
$app->post('/postAdding', function (Request $request, Response $response, $args) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $data = $request->getParsedBody();
        $studentName = $data["studentName"];
        $studentID = $data["studentID"];
        $year = $data["year"];
        $section = $data["section"];
        $amount = $data["amount"];
        $date = $data["date"];
        $officerInCharge = $data["officerInCharge"];
        $status = $data["status"];
        $description = $data["description"];

        $stmt = $conn->prepare("INSERT INTO student_profile (student_name, student_id, year, section, amount, date, officer, status, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$studentName, $studentID, $year, $section, $amount, $date, $officerInCharge, $status, $description]);

        $response->getBody()->write("Successful!");

        $conn = null;

        return $response;
    } catch (PDOException $e) {
        $error = ["status" => "error", "message" => "Error: " . $e->getMessage()];
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

$app->run();

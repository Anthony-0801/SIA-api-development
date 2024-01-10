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

// for the 'Print Summary' function
$app->get('/printSummary', function (Request $request, Response $response, array $args) {
    require 'FPDF/fpdf.php';

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        $error = array("status" => "error", "message" => "Connection failed: " . $conn->connect_error);
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    $summaryQuery = "SELECT
                    Year, Section, studentId, studentname, description, amount
                    FROM student_profile
                    ORDER BY Year, Section";

    $result = $conn->query($summaryQuery);

    if ($result === false) {
        $error = array("status" => "error", "message" => "Query failed: " . $conn->error);
        $response->getBody()->write(json_encode($error));
        $conn->close();
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    // creating a PDF document
    $pdf = new FPDF();
    $pdf->AddPage();

     // title
     $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(190, 10, 'JSITES Membership Fee Records', 0, 1, 'C'); 
    $pdf->Ln(); 

    $currentYear = null;
    $currentSection = null;
    $totalCollected = 0;
    $totalCollectable = 0;
    $totalStudents = 0;

    while ($row = $result->fetch_assoc()) {
        if ($row['description'] === 'paid') {
            $totalCollected += $row['amount'];
        } elseif ($row['description'] === 'not paid') {
            $totalCollectable += $row['amount'];
        }
        $totalStudents++;
    }

    // for the overall total
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(90, 10, 'Overall Total Amount Collected:', 0);
    $pdf->Cell(40, 10, 'Php ' . $totalCollected, 0);
    $pdf->Ln();
    $pdf->Cell(90, 10, 'Overall Total Amount Collectable:', 0);
    $pdf->Cell(40, 10, 'Php ' . $totalCollectable, 0);
    $pdf->Ln();
    $pdf->Cell(90, 10, 'Overall Total Students:', 0);
    $pdf->Cell(40, 10,  $totalStudents, 0);
    $pdf->Ln(); 
    $pdf->Ln(); 

    // for the overall total
    $totalCollected = 0;
    $totalCollectable = 0;
    $totalStudents = 0;

    $currentYear = null;
    $currentSection = null;

    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        if ($currentYear !== $row['Year'] || $currentSection !== $row['Section']) {
            if ($currentYear !== null) {
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(100, 10, 'Total Amount Collected:', 1);
                $pdf->Cell(50, 10, 'Php ' . $totalCollected, 1);
                $pdf->Ln(); 
                $pdf->Cell(100, 10, 'Total Amount Collectable:', 1);
                $pdf->Cell(50, 10, 'Php ' . $totalCollectable, 1);
                $pdf->Ln(); 
                $pdf->Cell(100, 10, 'Total Students:', 1);
                $pdf->Cell(50, 10, $totalStudents, 1);
                $pdf->Ln(); 
                $pdf->Ln(); 
            }
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(40, 10, 'Year & Section: ' . $row['Year'] . ' - ' . $row['Section']);
            $pdf->Ln(); 
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(50, 10, 'Student ID', 1);
            $pdf->Cell(50, 10, 'Student Name', 1);
            $pdf->Cell(50, 10, 'Status', 1);
            $pdf->Ln(); 

            //for the total by year
            $totalCollected = 0;
            $totalCollectable = 0;
            $totalStudents = 0;

            $currentYear = $row['Year'];
            $currentSection = $row['Section'];
        }

        if ($row['description'] === 'paid') {
            $totalCollected += $row['amount'];
        } elseif ($row['description'] === 'not paid') {
            $totalCollectable += $row['amount'];
        }

        $totalStudents++;

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, $row['studentId'], 1);
        $pdf->Cell(50, 10, $row['studentname'], 1);
        $pdf->Cell(50, 10, $row['description'], 1);
        $pdf->Ln(); 
    }

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(100, 10, 'Total Amount Collected:', 1);
    $pdf->Cell(50, 10, 'Php ' . $totalCollected, 1);
    $pdf->Ln(); 
    $pdf->Cell(100, 10, 'Total Amount Collectable:', 1);
    $pdf->Cell(50, 10, 'Php ' . $totalCollectable, 1);
    $pdf->Ln();
    $pdf->Cell(100, 10, 'Total Students:', 1);
    $pdf->Cell(50, 10, $totalStudents, 1);
    $pdf->Ln(); 

    $pdfContent = $pdf->Output("", "S");

    $conn->close();

    $response = $response
        ->withHeader('Content-Type', 'application/pdf')
        ->withHeader('Content-Disposition', 'inline; filename="summary.pdf"')
        ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->withHeader('Cache-Control', 'post-check=0, pre-check=0')
        ->withHeader('Pragma', 'no-cache')
        ->withStatus(200);

    $response->getBody()->write($pdfContent);

    return $response;
});


function getTotalAmount($description) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    $conn = new mysqli($servername, $username, $password, $dbname);

    $sql = "SELECT SUM(amount) as total FROM student_profile WHERE description = '$description'";
    $result = $conn->query($sql);

    $total = $result->fetch_assoc()['total'];

    $conn->close();

    return $total;
}

function getTableData($year, $section) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_jsites";

    $conn = new mysqli($servername, $username, $password, $dbname);

    $sql = "SELECT studentId, studentname, description FROM student_profile WHERE year = '$year' AND section = '$section' ORDER BY year, section";
    $result = $conn->query($sql);

    $data = array();

    while ($row = $result->fetch_assoc()) {
        array_push($data, $row);
    }

    $conn->close();

    return $data;
}

$app->run();
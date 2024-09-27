<?php
// api.php

// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Database connection (replace with your actual database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "contacts_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get the HTTP method and requested endpoint
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

// Determine the operation based on the request
$operation = $request[0] ?? '';

switch ($method) {
    case 'GET':
        if ($operation == 'contacts') {
            getContacts($conn);
        } elseif ($operation == 'contact' && isset($request[1])) {
            getContact($conn, $request[1]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Not found"]);
        }
        break;
    case 'POST':
        if ($operation == 'contacts') {
            addContact($conn);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Not found"]);
        }
        break;
    case 'PUT':
        if ($operation == 'contact' && isset($request[1])) {
            updateContact($conn, $request[1]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Not found"]);
        }
        break;
    case 'DELETE':
        if ($operation == 'contact' && isset($request[1])) {
            deleteContact($conn, $request[1]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Not found"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}

$conn->close();

function getContacts($conn) {
    $sql = "SELECT * FROM contacts";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $contacts = [];
        while($row = $result->fetch_assoc()) {
            $contacts[] = $row;
        }
        echo json_encode($contacts);
    } else {
        echo json_encode([]);
    }
}

function getContact($conn, $id) {
    $sql = "SELECT * FROM contacts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Contact not found"]);
    }
}

function addContact($conn) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->name) || !isset($data->phone) || !isset($data->email) || !isset($data->address)) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        return;
    }

    $sql = "INSERT INTO contacts (name, phone, email, address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $data->name, $data->phone, $data->email, $data->address);

    if ($stmt->execute()) {
        $data->id = $stmt->insert_id;
        http_response_code(201);
        echo json_encode($data);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to add contact"]);
    }
}

function updateContact($conn, $id) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->name) || !isset($data->phone) || !isset($data->email) || !isset($data->address)) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        return;
    }

    $sql = "UPDATE contacts SET name = ?, phone = ?, email = ?, address = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $data->name, $data->phone, $data->email, $data->address, $id);

    if ($stmt->execute()) {
        getContact($conn, $id);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update contact"]);
    }
}

function deleteContact($conn, $id) {
    $sql = "DELETE FROM contacts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Contact deleted successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to delete contact"]);
    }
}
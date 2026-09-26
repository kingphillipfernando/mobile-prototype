<?php

require_once 'connection.php';
require_once 'auth.php';

header("Content-Type: application/json");

// Keep your existing authentication system
$token = getBearerToken();

if (!validateToken($token)) {
    http_response_code(401);

    echo json_encode([
        "message" => "Unauthorized"
    ]);

    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // =========================
    // GET
    // =========================
    case 'GET':

        if (isset($_GET['id'])) {

            $id = intval($_GET['id']);

            $stmt = $conn->prepare(
                "SELECT * FROM laptop_parts WHERE id = ?"
            );

            $stmt->bind_param("i", $id);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {

                http_response_code(404);

                echo json_encode([
                    "message" => "Laptop part not found"
                ]);

                exit;
            }

            echo json_encode(
                $result->fetch_assoc()
            );

        } else {

            $result = $conn->query(
                "SELECT * FROM laptop_parts ORDER BY id ASC"
            );

            $parts = [];

            while ($row = $result->fetch_assoc()) {
                $parts[] = $row;
            }

            echo json_encode($parts);
        }

        break;


    // =========================
    // POST
    // =========================
    case 'POST':

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (!$data) {

            http_response_code(400);

            echo json_encode([
                "message" => "Invalid JSON"
            ]);

            exit;
        }

        $stmt = $conn->prepare(
            "INSERT INTO laptop_parts
            (
                product_name,
                brand,
                category,
                model_compatibility,
                price,
                stock_quantity,
                image,
                description
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssssdiss",
            $data['product_name'],
            $data['brand'],
            $data['category'],
            $data['model_compatibility'],
            $data['price'],
            $data['stock_quantity'],
            $data['image'],
            $data['description']
        );

        if ($stmt->execute()) {

            http_response_code(201);

            echo json_encode([
                "message" => "Laptop part created successfully",
                "id" => $conn->insert_id
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "message" => "Failed to create laptop part"
            ]);
        }

        break;


    // =========================
    // PUT
    // =========================
    case 'PUT':

        if (!isset($_GET['id'])) {

            http_response_code(400);

            echo json_encode([
                "message" => "ID is required"
            ]);

            exit;
        }

        $id = intval($_GET['id']);

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        $stmt = $conn->prepare(
            "UPDATE laptop_parts SET
                product_name = ?,
                brand = ?,
                category = ?,
                model_compatibility = ?,
                price = ?,
                stock_quantity = ?,
                image = ?,
                description = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "ssssdissi",
            $data['product_name'],
            $data['brand'],
            $data['category'],
            $data['model_compatibility'],
            $data['price'],
            $data['stock_quantity'],
            $data['image'],
            $data['description'],
            $id
        );

        if ($stmt->execute()) {

            echo json_encode([
                "message" => "Laptop part updated successfully"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "message" => "Failed to update laptop part"
            ]);
        }

        break;


    // =========================
    // DELETE
    // =========================
    case 'DELETE':

        if (!isset($_GET['id'])) {

            http_response_code(400);

            echo json_encode([
                "message" => "ID is required"
            ]);

            exit;
        }

        $id = intval($_GET['id']);

        $stmt = $conn->prepare(
            "DELETE FROM laptop_parts WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {

            echo json_encode([
                "message" => "Laptop part deleted successfully"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "message" => "Failed to delete laptop part"
            ]);
        }

        break;


    default:

        http_response_code(405);

        echo json_encode([
            "message" => "Method not allowed"
        ]);
}

?>

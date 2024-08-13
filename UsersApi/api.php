<?php
    include_once('../connection/config.php');
    include_once('users.php');

    // Instantiate database and user API
    $database = new Database();
    $db = $database->getConnection();

    // Guard clause: check for successful database connection
    if (!$db) {
        $response = [
            'status' => 500,
            'message' => 'Database connection failed.',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($response);
        exit();
    }

    $userAPI = new UserAPI($db);

    // Handle API request
    header("Content-Type: application/json");
    $method = $_SERVER['REQUEST_METHOD'];

    // Guard clause: handle unsupported request methods
    if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) {
        $response = [
            'status' => 405,
            'message' => 'Request method not supported.',
        ];
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode($response);
        exit();
    }

    // Handle the request based on the HTTP method
    switch ($method) {
        case 'GET':
            // Ensure no data is being sent in the body of a GET request
            if (file_get_contents("php://input")) {
                $response = [
                    'status' => 405,
                    'message' => 'GET method should not have a request body.',
                ];
                header("HTTP/1.0 405 Method Not Allowed");
                echo json_encode($response);
                exit();
            }
            if (isset($_GET['id'])) {
                $stmt = $userAPI->getUser($_GET['id']);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $response = [
                        'status' => 200,
                        'message' => 'User fetched successfully.',
                        'data' => $user,
                    ];
                    header("HTTP/1.0 200 OK");
                } else {
                    $response = [
                        'status' => 404,
                        'message' => 'User not found.',
                    ];
                    header("HTTP/1.0 404 Not Found");
                }
            } else {
                $stmt = $userAPI->getUsers();   
                if ($stmt === null) {
                    $response = [
                        'status' => 200,
                        'message' => 'This table is not set.',
                    ];
                    header("HTTP/1.0 200 OK");
                } else {
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response = [
                        'status' => 200,
                        'message' => 'Users fetched successfully.',
                        'data' => $users,
                    ];
                    header("HTTP/1.0 200 OK");
                }
            }
            echo json_encode($response);
        break;

        case 'POST':
            // Proceed with creating the user if the table is not empty
            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['name']) || !isset($data['email']) || !isset($data['phone'])) {
                $response = [
                    'status' => 400,
                    'message' => 'Incomplete data.',
                ];
                header("HTTP/1.0 400 Bad Request");
                echo json_encode($response);
                exit();
            }
        
            if ($userAPI->createUser($data['name'], $data['email'], $data['phone'])) {
                $response = [
                    'status' => 201,
                    'message' => 'User created successfully.',
                ];
                header("HTTP/1.0 201 OK");
            } else {
                $response = [
                    'status' => 500,
                    'message' => 'User creation failed.',
                ];
                header("HTTP/1.0 500 Internal Server Error");
            }
            echo json_encode($response);
        break;

        case 'PUT':
            // Handle user update
            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['id']) || !isset($data['name']) || !isset($data['email']) || !isset($data['phone'])) {
                $response = [
                    'status' => 400,
                    'message' => 'Incomplete data.',
                ];
                header("HTTP/1.0 400 Bad Request");
                echo json_encode($response);
                exit();
            }
            if ($userAPI->updateUser($data['id'], $data['name'], $data['email'], $data['phone'])) {
                $response = [
                    'status' => 200,
                    'message' => 'User updated successfully.',
                ];
                header("HTTP/1.0 200 OK");
            } else {
                $response = [
                    'status' => 500,
                    'message' => 'User update failed.',
                ];
                header("HTTP/1.0 500 Internal Server Error");
            }
            echo json_encode($response);
        break;

        case 'DELETE':
            // Handle user deletion
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['id'])) {
                $response = [
                    'status' => 400,
                    'message' => 'Incomplete data. The "id" field is required.',
                ];
                header("HTTP/1.0 400 Bad Request");
                echo json_encode($response);
                exit();
            }

            if ($userAPI->deleteUser($data['id'])) {
                $response = [
                    'status' => 200,
                    'message' => 'User deleted successfully.',
                ];
                header("HTTP/1.0 200 OK");
            } else {
                $response = [
                    'status' => 500,
                    'message' => 'User deletion failed.',
                ];
                header("HTTP/1.0 500 Internal Server Error");
            }
            echo json_encode($response);
        break;
    }
?>
<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // 🔹 GET: Tampilkan semua user atau user berdasarkan ID
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = $conn->query("SELECT * FROM users WHERE id=$id");
            echo json_encode($result->fetch_assoc());
        } else {
            $result = $conn->query("SELECT * FROM users");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

    // 🔹 POST: Tambah user baru
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $conn->real_escape_string($data['username']);
        $email = $conn->real_escape_string($data['email']);
        $password = password_hash($data['password'], PASSWORD_BCRYPT);

        $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($query)) {
            echo json_encode(["message" => "User berhasil ditambahkan"]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
        break;

    // 🔹 PUT: Update user berdasarkan ID
    case 'PUT':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $data = json_decode(file_get_contents("php://input"), true);
            $username = $conn->real_escape_string($data['username']);
            $email = $conn->real_escape_string($data['email']);
            $password = password_hash($data['password'], PASSWORD_BCRYPT);

            $query = "UPDATE users SET username='$username', email='$email', password='$password' WHERE id=$id";
            if ($conn->query($query)) {
                echo json_encode(["message" => "User berhasil diperbarui"]);
            } else {
                echo json_encode(["error" => $conn->error]);
            }
        } else {
            echo json_encode(["error" => "Parameter id diperlukan"]);
        }
        break;

    // 🔹 DELETE: Hapus user berdasarkan ID
    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $query = "DELETE FROM users WHERE id=$id";
            if ($conn->query($query)) {
                echo json_encode(["message" => "User berhasil dihapus"]);
            } else {
                echo json_encode(["error" => $conn->error]);
            }
        } else {
            echo json_encode(["error" => "Parameter id diperlukan"]);
        }
        break;

    default:
        echo json_encode(["message" => "Metode tidak dikenali"]);
        break;
}
?>

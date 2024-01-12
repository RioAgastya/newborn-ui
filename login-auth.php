<?php

require_once "connection.php";

header('Accept: application/json');
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

$uemail = '';
$upass = '';
$uhp = '';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $request_body = json_decode(file_get_contents('php://input'), true);
    $uemail = $request_body['uemail'];
    $upass = $request_body['upass'];
    $uhp = $request_body['uhp'];

    $sql = "SELECT * FROM `user` WHERE `email` = :email AND `password` = :password AND `no_handphone` = :no_hp;";
    $select_query = $pdo->prepare($sql);
    $select_query->execute(array(':email' => $uemail, ':password' => $upass, ':no_hp' => $uhp));
    $user = $select_query->fetch();
    $select_query = null;
    $pdo = null;

    if ($user === false) {
        $response['message'] = 'Pengguna tidak ditemukan!';
        http_response_code(404);
        echo json_encode($response);
    } else {
        $response['success'] = true;
        $response['message'] = 'Login sukses';
        http_response_code(200);
        echo json_encode($response);
    }
}

?>

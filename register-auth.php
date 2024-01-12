<?php

require_once "connection.php";

header('Content-Type: application/json');
header('Accept: application/json');

$response = [
    'success' => false,
    'message' => ''
];

$uemail = '';
$ufullname = '';
$unickname = '';
$upass = '';
$udob = '';
$uhp = '';
$ugender = '';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $request_body = json_decode(file_get_contents('php://input'), true);
    $uemail = $request_body['uemail'];
    $ufullname = $request_body['ufullname'];
    $unickname = $request_body['unickname'];
    $upass = $request_body['upass'];
    $udob = $request_body['udob'];
    $uhp = $request_body['uhp'];
    $ugender = $request_body['ugender'];

    $sql = "SELECT COUNT(*) FROM `user` WHERE `email` = :email";
    $select_query = $pdo->prepare($sql);
    $select_query->execute(array(':email' => $uemail));
    $count = $select_query->fetchColumn();
    $select_query = null;

    if ($count > 0) {
        $response['message'] = 'Email sudah terdaftar!';
        http_response_code(400);
        echo json_encode($response);
    } else {
        $sql = "INSERT INTO `user`
            (`email`, `nama`, `nama_lengkap`, `ttl`, `jenis_kelamin`, `no_handphone`, `password`)
            VALUES (:email, :nama, :nama_lengkap, :ttl, :jenis_kelamin, :no_handphone, :password);";
        $insert_query = $pdo->prepare($sql);
        $insert_query->execute(array(
            ':email' => $uemail,
            ':nama' => $unickname,
            ':nama_lengkap' => $ufullname,
            ':ttl' => $udob,
            ':jenis_kelamin' => $ugender,
            ':no_handphone' => $uhp,
            ':password' => $upass
        ));
        $insert_query = null;
        $pdo = null;
        $response['success'] = true;
        $response['message'] = 'Registrasi sukses';
        http_response_code(200);
        echo json_encode($response);
    }
}


?>

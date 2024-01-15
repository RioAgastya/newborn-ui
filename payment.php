<?php

$pdo = null;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=newborn', 'root', '', array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ));
} catch (PDOException $e) {
    die($e->getMessage());
}

function randomid($len) {
	return bin2hex(openssl_random_pseudo_bytes($len));
}

$idlen = 16;

header('Accept: application/json');
header('Content-Type: application/json');

$response = [
  'success' => false,
  'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $request_body = json_decode(file_get_contents('php://input'), true);

  $sql = "SELECT * FROM `tb_user` WHERE `email` = :email;";
  $query = $pdo->prepare($sql);
  $query->execute(array(':email' => $request_body['email']));
  $user = $query->fetch();
  
  if ($user === false) {
    $response['message'] = 'Informasi pembayaran tidak valid!';
    http_response_code(400);
    echo json_encode($response);
  } else {
    $paymentinfo = [
      ':id_transaksi' => randomid($idlen),
      ':email' => $request_body['email'],
      ':no_hp' => $user['no_hp'],
      ':keluhan' => $request_body['illness'],
      ':penangan' => $request_body['dochandler'],
      ':harga' => $request_body['price']
    ];
    $sql = "INSERT INTO `tb_transaksi`
      (`id_transaksi`, `email`, `no_hp`, `keluhan`, `penangan`, `harga`)
      VALUES (:id_transaksi, :email, :no_hp, :keluhan, :penangan, :harga);";
    $query = $pdo->prepare($sql);
    $success = $query->execute($paymentinfo);
    $pdo = null;

    if ($success) {
      $response['success'] = true;
      $response['message'] = "Transaksi pembayaran berhasil!";
      http_response_code(200);
      echo json_encode($response);
    } else {
      $response['message'] = 'Terjadi kesalahan!';
      http_response_code(400);
      echo json_encode($response);
    }
  }
}

?>

<?php
session_start();

header("Content-Type: application/json");

if(!isset($_SESSION["usuario"])){
    echo json_encode(["success" => false, "error" => "no_sesion"]);
    exit();
}

require_once("../bd/bdact.php");
$bdact = new bdact("localhost", 3306, "plataforma_servicios1", "root", "");

if(!isset($_GET["idservicio"])){
    echo json_encode(["success" => false, "error" => "sin_id"]);
    exit();
}

$idUsuario = $_SESSION["usuario"];
$idServicio = (int) $_GET["idservicio"];

if($bdact->esFavorito($idUsuario, $idServicio)){
    $bdact->eliminarFavorito($idUsuario, $idServicio);
    $esFavorito = false;
}else{
    $bdact->agregarFavorito($idUsuario, $idServicio);
    $esFavorito = true;
}

echo json_encode([
    "success" => true,
    "esFavorito" => $esFavorito
]);
exit();
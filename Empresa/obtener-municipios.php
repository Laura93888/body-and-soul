<?php
require_once("../bd/bdempresa.php");

$bdempre = new bdempresa("localhost", 3306, "plataforma_servicios1", "root", "");

header("Content-Type: application/json; charset=utf-8");

$idProvincia = $_GET["id_provincia"] ?? "";

if($idProvincia == ""){
    echo json_encode([]);
    exit();
}

$municipios = $bdempre->ObtenerMunicipiosPorProvincia($idProvincia);

echo json_encode($municipios);
exit();
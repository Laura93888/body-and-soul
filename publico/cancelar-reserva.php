<?php
require_once("../bd/bdact.php");
require_once("../bd/bd.php");
require_once("../utils/mailer.php");

session_start();

$bdact = new bdact("localhost", 3306, "plataforma_servicios1", "root", "");
$bbdd = new db("localhost", 3306, "plataforma_servicios1", "root", "");

if(!isset($_SESSION["usuario"])){
    header("Location: index.php");
    exit();
}

if(!isset($_GET["idreserva"])){
    echo "Reserva no válida";
    exit();
}

$idReserva = (int) $_GET["idreserva"];
$idUsuario = $_SESSION["usuario"];

$reserva = $bdact->ObtenerReservaUsuarioPorId($idUsuario, $idReserva);

if($reserva == false){
    echo "Reserva no encontrada";
    exit();
}

$esPasada = strtotime($reserva['fecha_hora']) < time();
$menos24h = strtotime($reserva['fecha_hora']) <= strtotime('+24 hours') && !$esPasada;

if($esPasada || $menos24h){
    header("Location: perfil.php?error=no_cancelable");
    exit();
}

$cancelada = $bdact->CancelarReserva($idUsuario, $idReserva);

if($cancelada){

    $usuario = $bbdd->ObtenerUsuario($idUsuario);

    if($usuario){

        $datosReserva = [
            "actividad" => $reserva["nombre_servicio"],
            "fecha" => $reserva["fecha"],
            "hora" => $reserva["hora_inicio"],
            "duracion" => $reserva["duracion"],
            "ubicacion" => $reserva["lugar"],
            "empresa" => $reserva["nombre_empresa"],
            "telefono" => $reserva["telefono_empresa"]
        ];

        enviarCorreoCancelacion(
            $usuario["email"],
            $usuario["nombre"],
            $datosReserva
        );
    }
}

header("Location: perfil.php");
exit();
    <?php

$rutaJson = "../JSON/actividades.json";

$datos = json_decode(file_get_contents($rutaJson), true);

$buscador = trim($_GET["buscador"] ?? "");
$categoria = trim($_GET["categoria"] ?? "");
$precio = trim($_GET["precio"] ?? "");
$fecha = trim($_GET["fecha"] ?? "");
$ubicacion = trim($_GET["ubicacion"] ?? "");

$resultados = [];

function limpiarTexto($texto){
    $texto = mb_strtolower($texto, 'UTF-8');

    $acentos = ['á','é','í','ó','ú','ü','ñ'];
    $sinAcentos = ['a','e','i','o','u','u','n'];

    return str_replace($acentos, $sinAcentos, $texto);
}

foreach($datos as $empresa){

    foreach($empresa["servicios"] as $servicio){

        if(($servicio["estado"] ?? "activo") != "activo"){
            continue;
        }

        if(empty($servicio["latitud"]) || empty($servicio["longitud"])){
            continue;
        }

        $coincide = true;

        $texto = limpiarTexto(
            ($servicio["nombre_servicio"] ?? "") . " " .
            ($servicio["descripcion"] ?? "") . " " .
            ($servicio["lugar"] ?? "") . " " .
            ($servicio["categoria"] ?? "") . " " .
            ($servicio["subcategoria"] ?? "")
        );

        if($buscador != "" && strpos($texto, limpiarTexto($buscador)) === false){
            $coincide = false;
        }

        if($categoria != "" && limpiarTexto($servicio["categoria"] ?? "") != limpiarTexto($categoria)){
            $coincide = false;
        }

        if($ubicacion != ""){

            $textoUbicacion = limpiarTexto(
                $servicio["lugar"] . " " .
                ($servicio["codigo_postal"] ?? "")
            );

            if(strpos($textoUbicacion, limpiarTexto($ubicacion)) === false){
                $coincide = false;
            }
        }

        if($coincide){

            $resultados[] = [
                "id_servicio" => $servicio["id_servicio"],
                "nombre_servicio" => $servicio["nombre_servicio"],
                "lugar" => $servicio["lugar"],
                "latitud" => $servicio["latitud"],
                "longitud" => $servicio["longitud"]
            ];
        }
    }
}

header("Content-Type: application/json; charset=utf-8");

echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
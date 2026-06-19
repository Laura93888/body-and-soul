<?php
require_once("bd.php");
require_once("bdact.php");

$bbdd = new db("localhost", 3306, "plataforma_servicios1", "root", "");
$bdact = new bdact("localhost", 3306, "plataforma_servicios1", "root", "");

$sql = "SELECT 
            e.id_empresa,
            e.nombre_empresa,
            s.id_servicio,
            s.id_categoria,
            s.nombre_servicio,
            s.descripcion,
            s.lugar,
            s.codigo_postal,
            s.latitud,
            s.longitud,
            s.precio,
            s.duracion,
            s.materiales,
            s.estado,
            c.nombre AS subcategoria,
            cp.nombre AS categoria,
            da.id AS id_detalle_actividad,
            da.fecha,
            da.hora_inicio,
            da.hora_fin,
            da.plazas_maximas,
            i.url_imagen
        FROM servicio s
        INNER JOIN empresa e 
            ON s.id_empresa = e.id_empresa
        LEFT JOIN categoria c 
            ON s.id_categoria = c.id_categoria
        LEFT JOIN categoria cp 
            ON c.id_categoria_padre = cp.id_categoria
        LEFT JOIN detalle_actividad da 
            ON s.id_servicio = da.id_servicio
        LEFT JOIN imagen_servicio i 
            ON s.id_servicio = i.id_servicio
        ORDER BY e.id_empresa, s.id_servicio, da.fecha, da.hora_inicio";

$stmt = $bbdd->pdo->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

$empresas = [];

foreach ($resultados as $fila) {
    $idEmpresa = (int)$fila["id_empresa"];
    $idServicio = (int)$fila["id_servicio"];

    if (!isset($empresas[$idEmpresa])) {
        $empresas[$idEmpresa] = [
            "id_empresa" => $idEmpresa,
            "nombre_empresa" => $fila["nombre_empresa"],
            "servicios" => []
        ];
    }

    if (!isset($empresas[$idEmpresa]["servicios"][$idServicio])) {
        $empresas[$idEmpresa]["servicios"][$idServicio] = [
            "id_servicio" => $idServicio,
            "id_categoria" => (int)$fila["id_categoria"],
            "nombre_servicio" => $fila["nombre_servicio"],
            "categoria" => $fila["categoria"],
            "subcategoria" => $fila["subcategoria"],
            "descripcion" => $fila["descripcion"],
            "lugar" => $fila["lugar"],
            "codigo_postal" => $fila["codigo_postal"],
            "latitud" => $fila["latitud"],
            "longitud" => $fila["longitud"],
            "precio" => $fila["precio"],
            "estado" => $fila["estado"],
            "duracion" => $fila["duracion"],
            "materiales" => $fila["materiales"],
            "imagenes" => [],
            "detalles" => []
        ];
    }

    if (!empty($fila["url_imagen"])) {
        if (!in_array($fila["url_imagen"], $empresas[$idEmpresa]["servicios"][$idServicio]["imagenes"])) {
            $empresas[$idEmpresa]["servicios"][$idServicio]["imagenes"][] = $fila["url_imagen"];
        }
    }

    if (!empty($fila["id_detalle_actividad"])) {
        $detalle = [
            "id_detalle_actividad" => (int)$fila["id_detalle_actividad"],
            "fecha" => $fila["fecha"],
            "hora_inicio" => $fila["hora_inicio"],
            "hora_fin" => $fila["hora_fin"],
            "plazas_maximas" => (int)$fila["plazas_maximas"]
        ];

        $existeDetalle = false;

        foreach ($empresas[$idEmpresa]["servicios"][$idServicio]["detalles"] as $d) {
            if ($d["id_detalle_actividad"] === $detalle["id_detalle_actividad"]) {
                $existeDetalle = true;
                break;
            }
        }

        if (!$existeDetalle) {
            $empresas[$idEmpresa]["servicios"][$idServicio]["detalles"][] = $detalle;
        }
    }
}

$jsonFinal = [];

foreach ($empresas as $empresa) {
    $empresa["servicios"] = array_values($empresa["servicios"]);
    $jsonFinal[] = $empresa;
}

$ruta = "../JSON/actividades.json";

$resultadoGuardado = file_put_contents(
    $ruta,
    json_encode($jsonFinal, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

if ($resultadoGuardado === false) {
    echo "Error al generar el archivo JSON";
} else {
    echo "JSON generado correctamente en: " . $ruta;
}
?>
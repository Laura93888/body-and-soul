<?php

class bdact{

public $pdo = "";

public function __construct($host,$port,$db,$user,$pass) { 
    $this->pdo = new PDO("mysql:host=".$host.";port=".$port.";dbname=".$db.";charset=utf8", $user, $pass);
    }

function obtenerCategoriasPadre() {
    $sentencia = "SELECT * FROM categoria WHERE id_categoria_padre IS NULL
            ORDER BY nombre ASC";

    $ejecuccion = $this->pdo->prepare($sentencia);
    $ejecuccion->execute();

    $fila=$ejecuccion->fetchAll(PDO::FETCH_ASSOC);
    return $fila;
}


function obtenerSubcategoriasPorId($idCategoriaPadre) {
    $sentencia = "SELECT * FROM categoria WHERE id_categoria_padre = :id_categoria_padre";

    $ejecuccion = $this->pdo->prepare($sentencia);
    $ejecuccion->execute([
        ":id_categoria_padre" => $idCategoriaPadre
    ]);

    return $ejecuccion->fetch(PDO::FETCH_ASSOC);
}

public function obteneridcat($nombrecat){

    $sentencia = "SELECT id_categoria FROM categoria WHERE nombre = :nombre";

    $ejecuccion = $this->pdo->prepare($sentencia);
    $ejecuccion->execute([
        ":nombre" => $nombrecat
    ]);

    $fila=$ejecuccion->fetch(PDO::FETCH_ASSOC);
    return $fila["id_categoria"];

}

public function ObtenerIdCategoriaPorNombre($nombreCategoria){

    $sentencia = "SELECT id_categoria 
            FROM categoria 
            WHERE nombre = :nombre
            LIMIT 1";

    $ejecuccion = $this->pdo->prepare($sentencia);

    $ejecuccion->execute([
        ":nombre" => $nombreCategoria
    ]);

    $fila = $ejecuccion->fetch(PDO::FETCH_ASSOC);

    if($fila == false){
        return false;
    }

    return $fila["id_categoria"];
}

//Obtiene el nombre de la subcategoria y el de su categoria padre
public function ObtenerCategoriaConPadre($idCategoriaHijo){

    $sentencia = "SELECT 
                    c.nombre AS subcategoria,
                    cp.nombre AS categoria_padre
                  FROM categoria c
                  LEFT JOIN categoria cp 
                  ON c.id_categoria_padre = cp.id_categoria
                  WHERE c.id_categoria = :idCategoria";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":idCategoria" => $idCategoriaHijo
    ]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC);
}


//Obtiene todas las subcategorias de la categoria padre dada
public function obtenerSubcat($idcatpadre){

    $sentencia = "SELECT * FROM categoria WHERE id_categoria_padre = :idcatpadre ORDER BY nombre ASC";

    $ejecuccion = $this->pdo->prepare($sentencia);
    $ejecuccion->execute([
        ":idcatpadre" => $idcatpadre
    ]);

    $fila=$ejecuccion->fetchAll(PDO::FETCH_ASSOC);
    return $fila;    

}

//Obteine las actividades de una subcategoria concreta
public function obteneractividades($idsubcategoria){
    $sql = "SELECT 
                s.id_servicio,
                s.nombre_servicio,
                s.descripcion,
                s.lugar,
                s.precio,
                MIN(i.url_imagen) AS imagen
            FROM servicio s
            LEFT JOIN imagen_servicio i 
                ON s.id_servicio = i.id_servicio
            WHERE s.id_categoria = :idsubcategoria
            AND s.estado = 'activo'
            GROUP BY 
                s.id_servicio,
                s.nombre_servicio,
                s.descripcion,
                s.lugar,
                s.precio";

    $ejecucion = $this->pdo->prepare($sql);
    $ejecucion->execute([
        ":idsubcategoria" => $idsubcategoria
    ]);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function obtenerActividadesMasReservadas(){
    $sentencia = "SELECT 
                    s.id_servicio,
                    s.nombre_servicio,
                    s.descripcion,
                    s.lugar,
                    s.precio,
                    s.id_categoria,
                    MIN(i.url_imagen) AS imagen,
                    COUNT(r.id_reserva) AS total_reservas
                  FROM servicio s
                  INNER JOIN reserva r ON s.id_servicio = r.id_servicio
                  LEFT JOIN imagen_servicio i ON s.id_servicio = i.id_servicio
                  WHERE r.estado = 'confirmada'
                  AND s.estado = 'activo'
                  GROUP BY 
                    s.id_servicio,
                    s.nombre_servicio,
                    s.descripcion,
                    s.lugar,
                    s.precio,
                    s.id_categoria
                  ORDER BY total_reservas DESC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();
    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

//Aplicar filtros
public function filtrarActividades($buscador = "", $categoria = "", $subcategoria = "", $precio = ""){
    $sql = "SELECT s.*
            FROM servicio s
            INNER JOIN categoria c ON s.id_categoria = c.id_categoria
            LEFT JOIN categoria cp ON c.id_categoria_padre = cp.id_categoria";

    $condiciones = [];
    $parametros = [];

    // 👇 SIEMPRE activo
    $condiciones[] = "s.estado = 'activo'";

    if (!empty($buscador)) {
        $condiciones[] = "(s.nombre_servicio LIKE :buscador 
                        OR s.descripcion LIKE :buscador 
                        OR s.lugar LIKE :buscador)";
        $parametros[":buscador"] = "%" . $buscador . "%";
    }

    if (!empty($subcategoria)) {
        $condiciones[] = "s.id_categoria = :subcategoria";
        $parametros[":subcategoria"] = $subcategoria;
    } elseif (!empty($categoria)) {
        $condiciones[] = "cp.nombre = :categoria";
        $parametros[":categoria"] = $categoria;
    }

    if (!empty($precio)) {
        if ($precio == "0-10") {
            $condiciones[] = "s.precio BETWEEN 0 AND 10";
        } elseif ($precio == "10-25") {
            $condiciones[] = "s.precio BETWEEN 10 AND 25";
        } elseif ($precio == "25-50") {
            $condiciones[] = "s.precio BETWEEN 25 AND 50";
        } elseif ($precio == "50+") {
            $condiciones[] = "s.precio > 50";
        }
    }

    if (!empty($condiciones)) {
        $sql .= " WHERE " . implode(" AND ", $condiciones);
    }

    $sql .= " ORDER BY s.nombre_servicio ASC";

    $ejecucion = $this->pdo->prepare($sql);
    $ejecucion->execute($parametros);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function obtenerActividadPorId($idServicio){
    $sentencia = "SELECT 
            s.*,
            e.nombre_empresa,
            e.telefono AS telefono_empresa,
            e.email AS email_empresa
        FROM servicio s
        INNER JOIN empresa e ON s.id_empresa = e.id_empresa
        WHERE s.id_servicio = :id_servicio";
    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_servicio" => $idServicio
    ]);
    $fila = $ejecucion->fetch(PDO::FETCH_ASSOC);
    return $fila;
}

public function obtenerActividadPorIdConcancelados($idServicio){
    $sentencia = "SELECT * FROM servicio WHERE id_servicio = :id_servicio";
    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_servicio" => $idServicio
    ]);
    $fila = $ejecucion->fetch(PDO::FETCH_ASSOC);
    return $fila;
}

public function obtenerDisponibilidadesPorServicio($idServicio){
    $sentencia = "SELECT d.*
            FROM detalle_actividad d
            INNER JOIN servicio s ON d.id_servicio = s.id_servicio
            WHERE d.id_servicio = :id_servicio
            AND s.estado = 'activo'
            ORDER BY d.fecha ASC, d.hora_inicio ASC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_servicio" => $idServicio
    ]);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function obtenerDetalleActividadPorId($idDetalle){
    $sentencia = "SELECT * FROM detalle_actividad WHERE id = :id";
    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id" => $idDetalle
    ]);
    $fila = $ejecucion->fetch(PDO::FETCH_ASSOC);
    return $fila;
}

public function contarReservasConfirmadasPorDetalle($idDetalle){
    $sentencia = "SELECT COUNT(*) 
            FROM reserva
            WHERE id_detalle_actividad = :id_detalle
              AND estado = 'confirmada'";
    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_detalle" => $idDetalle
    ]);
    $fila = (int) $ejecucion->fetchColumn();
    return $fila;
}

public function usuarioYaReservoEsaFranja($idUsuario, $idDetalle){
    $sentencia = "SELECT COUNT(*)
            FROM reserva
            WHERE id_usuario = :id_usuario
              AND id_detalle_actividad = :id_detalle
              AND estado = 'confirmada'";
    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_usuario" => $idUsuario,
        ":id_detalle" => $idDetalle
    ]);
    $fila = ((int)$ejecucion->fetchColumn() > 0);
    return  $fila;
}

public function crearReserva($idUsuario, $idServicio, $fechaHora, $idDetalle){
    $sentencia = "INSERT INTO reserva (id_usuario, id_servicio, fecha_hora, estado, id_detalle_actividad)
            VALUES (:id_usuario, :id_servicio, :fecha_hora, 'confirmada', :id_detalle)";
    $ejecucion = $this->pdo->prepare($sentencia);
    return $ejecucion->execute([
        ":id_usuario" => $idUsuario,
        ":id_servicio" => $idServicio,
        ":fecha_hora" => $fechaHora,
        ":id_detalle" => $idDetalle
    ]);
}

public function ObtenerReservasUsuario($idUsuario){

    $sentencia = "SELECT 
                    r.id_reserva,
                    r.estado,
                    r.id_servicio,
                    r.fecha_hora,
                    s.nombre_servicio,
                    s.descripcion,
                    s.lugar,
                    s.precio,
                    s.duracion,
                    d.fecha,
                    d.hora_inicio,
                    d.hora_fin,
                    i.url_imagen AS imagen,
                    c.nombre AS subcategoria,
                    cp.nombre AS categoria_padre

                  FROM reserva r

                  INNER JOIN servicio s 
                    ON r.id_servicio = s.id_servicio

                  INNER JOIN detalle_actividad d 
                    ON r.id_detalle_actividad = d.id

                  LEFT JOIN categoria c 
                    ON s.id_categoria = c.id_categoria

                  LEFT JOIN categoria cp 
                    ON c.id_categoria_padre = cp.id_categoria

                  LEFT JOIN (
                        SELECT id_servicio, MIN(url_imagen) AS url_imagen
                        FROM imagen_servicio
                        GROUP BY id_servicio
                  ) i ON s.id_servicio = i.id_servicio

                  WHERE r.id_usuario = :id_usuario

                  ORDER BY d.fecha ASC, d.hora_inicio ASC";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":id_usuario" => $idUsuario
    ]);

    $fila = $ejecucion->fetchAll(PDO::FETCH_ASSOC);

    return $fila;
}

//Cancelar una reserva, comprobando que la reserva ya esté cancelada.
public function CancelarReserva($idUsuario, $idReserva){
    $sentencia = "UPDATE reserva
                  SET estado = 'cancelada'
                  WHERE id_reserva = :id_reserva
                  AND id_usuario = :id_usuario
                  AND estado = 'confirmada'";

    $ejecucion = $this->pdo->prepare($sentencia);

    return $ejecucion->execute([
        ":id_reserva" => $idReserva,
        ":id_usuario" => $idUsuario
    ]);
}

public function ObtenerReservaUsuarioPorId($idUsuario, $idReserva){
    $sentencia = "SELECT 
                    r.id_reserva,
                    r.estado,
                    r.id_servicio,
                    r.fecha_hora,
                    r.id_detalle_actividad,

                    s.nombre_servicio,
                    s.descripcion,
                    s.lugar,
                    s.precio,
                    s.duracion,

                    d.fecha,
                    d.hora_inicio,
                    d.hora_fin,

                    e.nombre_empresa,
                    e.telefono AS telefono_empresa,
                    e.email AS email_empresa

                  FROM reserva r
                  INNER JOIN servicio s 
                    ON r.id_servicio = s.id_servicio
                  INNER JOIN detalle_actividad d 
                    ON r.id_detalle_actividad = d.id
                  INNER JOIN empresa e
                    ON s.id_empresa = e.id_empresa
                  WHERE r.id_usuario = :id_usuario
                  AND r.id_reserva = :id_reserva
                  LIMIT 1";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_usuario" => $idUsuario,
        ":id_reserva" => $idReserva
    ]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC);
}

public function ModificarReserva($idUsuario, $idReserva, $idDetalle, $fechaHora){
    $sentencia = "UPDATE reserva
                  SET id_detalle_actividad = :id_detalle,
                      fecha_hora = :fecha_hora
                  WHERE id_reserva = :id_reserva
                  AND id_usuario = :id_usuario
                  AND estado = 'confirmada'";

    $ejecucion = $this->pdo->prepare($sentencia);

    return $ejecucion->execute([
        ":id_detalle" => $idDetalle,
        ":fecha_hora" => $fechaHora,
        ":id_reserva" => $idReserva,
        ":id_usuario" => $idUsuario
    ]);
}

public function usuarioTieneReservaEnMismaFechaHora($idUsuario, $fecha, $horaInicio){
    $sentencia = "SELECT COUNT(*)
                  FROM reserva r
                  INNER JOIN detalle_actividad d 
                    ON r.id_detalle_actividad = d.id
                  WHERE r.id_usuario = :id_usuario
                  AND d.fecha = :fecha
                  AND d.hora_inicio = :hora_inicio
                  AND r.estado = 'confirmada'";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_usuario" => $idUsuario,
        ":fecha" => $fecha,
        ":hora_inicio" => $horaInicio
    ]);

    return ((int)$ejecucion->fetchColumn() > 0);
}

public function usuarioTieneReservaEnMismaFechaHoraModificar($idUsuario, $fecha, $horaInicio, $idReservaActual){
    $sentencia = "SELECT COUNT(*)
                  FROM reserva r
                  INNER JOIN detalle_actividad d 
                    ON r.id_detalle_actividad = d.id
                  WHERE r.id_usuario = :id_usuario
                  AND d.fecha = :fecha
                  AND d.hora_inicio = :hora_inicio
                  AND r.estado = 'confirmada'
                  AND r.id_reserva != :id_reserva";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_usuario" => $idUsuario,
        ":fecha" => $fecha,
        ":hora_inicio" => $horaInicio,
        ":id_reserva" => $idReservaActual
    ]);

    return ((int)$ejecucion->fetchColumn() > 0);
}

public function obtenerImagenesPorServicio($idServicio){
    $sentencia = "SELECT url_imagen 
                  FROM imagen_servicio 
                  WHERE id_servicio = :id_servicio";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_servicio" => $idServicio
    ]);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function insertarResena($idUsuario, $idServicio, $puntuacion, $comentario){
    $sentencia = "INSERT INTO resena (id_usuario, id_servicio, puntuacion, comentario)
            VALUES (:usuario, :servicio, :puntuacion, :comentario)";
    
    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":usuario" => $idUsuario,
        ":servicio" => $idServicio,
        ":puntuacion" => $puntuacion,
        ":comentario" => $comentario
    ]);
}

public function obtenerResenasUsuario($idUsuario){
    $sentencia = "SELECT 
              r.*, 
              s.nombre_servicio,
              i.url_imagen AS imagen
            FROM resena r
            INNER JOIN servicio s 
              ON r.id_servicio = s.id_servicio
            LEFT JOIN imagen_servicio i 
              ON s.id_servicio = i.id_servicio
            WHERE r.id_usuario = :id
            GROUP BY r.id_resena
            ORDER BY r.fecha DESC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([":id" => $idUsuario]);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function puedeValorar($idUsuario, $idServicio){
    $sentencia = "SELECT *
            FROM reserva
            WHERE id_usuario = :usuario
            AND id_servicio = :servicio
            AND estado = 'confirmada'
            AND fecha_hora < NOW()
            LIMIT 1";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":usuario" => $idUsuario,
        ":servicio" => $idServicio
    ]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC) ? true : false;
}

public function yaHaValorado($idUsuario, $idServicio){
    $sentencia = "SELECT * FROM resena
            WHERE id_usuario = :usuario
            AND id_servicio = :servicio";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":usuario" => $idUsuario,
        ":servicio" => $idServicio
    ]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC) ? true : false;
}

public function obtenerReservaPorId($idReserva){
    $sentencia = "SELECT r.*, s.nombre_servicio
            FROM reserva r
            INNER JOIN servicio s ON r.id_servicio = s.id_servicio
            WHERE r.id_reserva = :id";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([":id" => $idReserva]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC);
}

public function obtenerMediaResenas($idServicio){
    $sentencia = "SELECT AVG(puntuacion) AS media, COUNT(*) AS total
            FROM resena
            WHERE id_servicio = :id";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([":id" => $idServicio]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC);
}

public function agregarFavorito($idUsuario, $idServicio){
    $sentencia = "INSERT IGNORE INTO favorito (id_usuario, id_servicio)
            VALUES (:usuario, :servicio)";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":usuario" => $idUsuario,
        ":servicio" => $idServicio
    ]);
}

public function eliminarFavorito($idUsuario, $idServicio){
    $sentencia = "DELETE FROM favorito
            WHERE id_usuario = :usuario
            AND id_servicio = :servicio";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":usuario" => $idUsuario,
        ":servicio" => $idServicio
    ]);
}

public function esFavorito($idUsuario, $idServicio){
    $sentencia = "SELECT id_favorito
            FROM favorito
            WHERE id_usuario = :usuario
            AND id_servicio = :servicio
            LIMIT 1";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":usuario" => $idUsuario,
        ":servicio" => $idServicio
    ]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC) ? true : false;
}

public function obtenerFavoritosUsuario($idUsuario){
    $sentencia = "SELECT 
                f.id_favorito,
                f.id_servicio,
                s.nombre_servicio,
                s.descripcion,
                s.lugar,
                s.precio,
                s.duracion,
                i.url_imagen AS imagen,
                c.nombre AS subcategoria,
                cp.nombre AS categoria_padre
            FROM favorito f
            INNER JOIN servicio s ON f.id_servicio = s.id_servicio
            LEFT JOIN imagen_servicio i ON s.id_servicio = i.id_servicio
            LEFT JOIN categoria c ON s.id_categoria = c.id_categoria
            LEFT JOIN categoria cp ON c.id_categoria_padre = cp.id_categoria
            WHERE f.id_usuario = :usuario
            GROUP BY f.id_favorito
            ORDER BY f.fecha DESC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([":usuario" => $idUsuario]);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ServicioTieneHorarios($idServicio){

    $sql = "SELECT id FROM detalle_actividad WHERE id_servicio = :id LIMIT 1";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([":id" => $idServicio]);

    return $stmt->fetch() != false;
}

public function obtenerResenasServicio($idServicio){
    $sentencia = "SELECT 
                    r.puntuacion,
                    r.comentario,
                    r.fecha,
                    u.nombre,
                    u.apellido
                  FROM resena r
                  INNER JOIN usuario u 
                    ON r.id_usuario = u.id_usuario
                  WHERE r.id_servicio = :id_servicio
                  ORDER BY r.fecha DESC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_servicio" => $idServicio
    ]);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function obtenerActividadesMapaInicio(){
    $sentencia = "SELECT 
                    s.id_servicio,
                    s.nombre_servicio,
                    s.lugar,
                    s.precio,
                    s.latitud,
                    s.longitud,
                    c.nombre AS subcategoria,
                    e.nombre_empresa,
                    COUNT(r.id_reserva) AS total_reservas
                  FROM servicio s
                  INNER JOIN empresa e ON s.id_empresa = e.id_empresa
                  INNER JOIN categoria c ON s.id_categoria = c.id_categoria
                  LEFT JOIN reserva r 
                    ON s.id_servicio = r.id_servicio 
                    AND r.estado = 'confirmada'
                  WHERE s.estado = 'activo'
                    AND e.estado = 'activa'
                    AND s.latitud IS NOT NULL
                    AND s.longitud IS NOT NULL
                  GROUP BY s.id_servicio
                  ORDER BY total_reservas DESC
                  LIMIT 12";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}


}

?>
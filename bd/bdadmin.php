<?php

class bdadmin{

public $pdo = "";

public function __construct($host,$port,$db,$user,$pass) { 
    $this->pdo = new PDO("mysql:host=".$host.";port=".$port.";dbname=".$db.";charset=utf8", $user, $pass);
    }

public function ObtenerSolicitudesPendientes($buscar = "", $categoria = "", $ciudad = ""){

    $sentencia = "SELECT * 
            FROM solicitud_empresa 
            WHERE estado = 'pendiente'";

    $parametros = [];

    if($buscar != ""){
        $sentencia .= " AND (
                    nombre LIKE :buscar
                    OR email LIKE :buscar
                    OR ciudad_empresa LIKE :buscar
                    OR telefono LIKE :buscar
                  )";

        $parametros[":buscar"] = "%" . $buscar . "%";
    }

    if($categoria != ""){
        $sentencia .= " AND categoria_empresa = :categoria";
        $parametros[":categoria"] = $categoria;
    }

    if($ciudad != ""){
        $sentencia .= " AND ciudad_empresa = :ciudad";
        $parametros[":ciudad"] = $ciudad;
    }

    $sentencia .= " ORDER BY fecha DESC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute($parametros);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerSolicitudEmpresaPorId($idSolicitud){

    $sentencia = "SELECT * 
                  FROM solicitud_empresa 
                  WHERE id_solicitud = :id";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id" => $idSolicitud
    ]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC);
}

public function AprobarEmpresa($idSolicitud){

    try{
        $this->pdo->beginTransaction();

        $sentencia = "SELECT * FROM solicitud_empresa 
                      WHERE id_solicitud = :id 
                      AND estado = 'pendiente'";

        $ejecucion = $this->pdo->prepare($sentencia);
        $ejecucion->execute([":id" => $idSolicitud]);

        $solicitud = $ejecucion->fetch(PDO::FETCH_ASSOC);

        if($solicitud == false){
            $this->pdo->rollBack();
            return false;
        }

        $sentencia2 = "INSERT INTO empresa 
        (nombre_empresa, email, contrasena, direccion, telefono, ciudad_empresa, categoria_empresa, logo_empresa, descripcion_empresa)
        VALUES 
        (:nombre, :email, :contrasena, :direccion, :telefono, :ciudad, :categoria, :logo, :descripcion)";

        $ejecucion2 = $this->pdo->prepare($sentencia2);
        $ejecucion2->execute([
            ":nombre" => $solicitud["nombre"],
            ":email" => $solicitud["email"],
            ":contrasena" => $solicitud["contrasena"],
            ":direccion" => $solicitud["direccion"],
            ":telefono" => $solicitud["telefono"],
            ":ciudad" => $solicitud["ciudad_empresa"],
            ":categoria" => $solicitud["categoria_empresa"],
            ":logo" => $solicitud["logo_empresa"],
            ":descripcion" => $solicitud["datos"]
        ]);

        $idEmpresa = $this->pdo->lastInsertId();

        $sentencia3 = "UPDATE solicitud_empresa 
                      SET estado = 'aprobada', id_empresa = :id_empresa
                      WHERE id_solicitud = :id";

        $ejecucion3 = $this->pdo->prepare($sentencia3);
        $ejecucion3->execute([
            ":id_empresa" => $idEmpresa,
            ":id" => $idSolicitud
        ]);

        $this->pdo->commit();
        return true;

    }catch(Exception $e){
        $this->pdo->rollBack();
        return false;
    }
}

public function RechazarEmpresa($idSolicitud){

    $sentencia = "UPDATE solicitud_empresa 
                  SET estado = 'rechazada' 
                  WHERE id_solicitud = :id 
                  AND estado = 'pendiente'";

    $ejecucion = $this->pdo->prepare($sentencia);
    return $ejecucion->execute([
        ":id" => $idSolicitud
    ]);
}

public function ObtenerEmpresasAprobadas($buscar = "", $categoria = "", $estado = ""){

    $sentencia = "SELECT 
                e.*,
                (
                    SELECT COUNT(*) 
                    FROM servicio s 
                    WHERE s.id_empresa = e.id_empresa
                ) AS total_servicios
            FROM empresa e
            WHERE 1=1";

    $parametros = [];

    if($buscar != ""){
        $sentencia .= " AND (
                    e.nombre_empresa LIKE :buscar
                    OR e.email LIKE :buscar
                    OR e.ciudad_empresa LIKE :buscar
                    OR e.telefono LIKE :buscar
                  )";
        $parametros[":buscar"] = "%" . $buscar . "%";
    }

    if($categoria != ""){
        $sentencia .= " AND e.categoria_empresa = :categoria";
        $parametros[":categoria"] = $categoria;
    }

    if($estado != ""){
        $sentencia .= " AND e.estado = :estado";
        $parametros[":estado"] = $estado;
    }

    $sentencia .= " ORDER BY e.id_empresa DESC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute($parametros);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerTodasActividades($buscar = "", $categoria = "", $estado = ""){

    $sentencia = "SELECT 
                s.*,
                e.nombre_empresa,
                c.nombre AS subcategoria,
                cp.nombre AS categoria_padre,
                (
                    SELECT url_imagen 
                    FROM imagen_servicio i
                    WHERE i.id_servicio = s.id_servicio
                    LIMIT 1
                ) AS imagen,
                (
                    SELECT COUNT(*) 
                    FROM reserva r
                    WHERE r.id_servicio = s.id_servicio
                ) AS total_reservas
            FROM servicio s
            INNER JOIN empresa e ON s.id_empresa = e.id_empresa
            INNER JOIN categoria c ON s.id_categoria = c.id_categoria
            LEFT JOIN categoria cp ON c.id_categoria_padre = cp.id_categoria
            WHERE 1=1";

    $parametros = [];

    if($buscar != ""){
        $sentencia .= " AND (
                    s.nombre_servicio LIKE :buscar
                    OR e.nombre_empresa LIKE :buscar
                    OR s.lugar LIKE :buscar
                    OR s.descripcion LIKE :buscar
                  )";
        $parametros[":buscar"] = "%" . $buscar . "%";
    }

    if($categoria != ""){
        $sentencia .= " AND (
                    cp.nombre = :categoria
                    OR c.nombre = :categoria
                  )";
        $parametros[":categoria"] = $categoria;
    }

    if($estado != ""){
        $sentencia .= " AND s.estado = :estado";
        $parametros[":estado"] = $estado;
    }

    $sentencia .= " ORDER BY s.id_servicio DESC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute($parametros);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function CancelarActividadAdmin($idServicio){

    $sentencia = "UPDATE servicio 
                  SET estado = 'cancelado'
                  WHERE id_servicio = :id_servicio";

    $ejecucion = $this->pdo->prepare($sentencia);
    return $ejecucion->execute([
        ":id_servicio" => $idServicio
    ]);
}

public function ActivarActividadAdmin($idServicio){

    $sentencia = "UPDATE servicio 
                  SET estado = 'activo'
                  WHERE id_servicio = :id_servicio";

    $ejecucion = $this->pdo->prepare($sentencia);
    return $ejecucion->execute([
        ":id_servicio" => $idServicio
    ]);
}

public function ObtenerUltimasSolicitudes(){
    $sentencia = "SELECT * FROM solicitud_empresa 
            WHERE estado = 'pendiente'
            ORDER BY fecha DESC
            LIMIT 3";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerUltimasActividades(){
    $sentencia = "SELECT s.*, e.nombre_empresa, c.nombre AS subcategoria, cp.nombre AS categoria_padre
            FROM servicio s
            INNER JOIN empresa e ON s.id_empresa = e.id_empresa
            INNER JOIN categoria c ON s.id_categoria = c.id_categoria
            LEFT JOIN categoria cp ON c.id_categoria_padre = cp.id_categoria
            ORDER BY s.id_servicio DESC
            LIMIT 3";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerDatosDashboard(){

    $datos = [];

    $consultas = [
        "pendientes" => "SELECT COUNT(*) FROM solicitud_empresa WHERE estado = 'pendiente'",

        "pendientes_hoy" => "SELECT COUNT(*) 
                             FROM solicitud_empresa 
                             WHERE estado = 'pendiente' 
                             AND DATE(fecha) = CURDATE()",

        "empresas" => "SELECT COUNT(*) FROM empresa",

        "empresas_mes" => "SELECT COUNT(*) 
                           FROM solicitud_empresa 
                           WHERE estado = 'aprobada'
                           AND MONTH(fecha) = MONTH(CURDATE())
                           AND YEAR(fecha) = YEAR(CURDATE())",

        "actividades" => "SELECT COUNT(*) FROM servicio",

        "actividades_canceladas" => "SELECT COUNT(*) 
                             FROM servicio 
                             WHERE estado = 'cancelado'",

        "usuarios" => "SELECT COUNT(*) FROM usuario",

        "usuarios_semana" => "SELECT COUNT(*) 
                              FROM usuario 
                              WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"
    ];

    foreach($consultas as $clave => $sentencia){
        $ejecucion = $this->pdo->prepare($sentencia);
        $ejecucion->execute();
        $datos[$clave] = $ejecucion->fetchColumn();
    }

    return $datos;
}

public function SuspenderUsuario($idUsuario){

    $sentencia = "UPDATE usuario 
            SET estado = 'suspendido'
            WHERE id_usuario = :id
            AND id_rol != 3";

    $ejecucion = $this->pdo->prepare($sentencia);
    return $ejecucion->execute([":id" => $idUsuario]);
}

public function ActivarUsuario($idUsuario){

    $sentencia = "UPDATE usuario 
            SET estado = 'activo'
            WHERE id_usuario = :id";

    $ejecucion = $this->pdo->prepare($sentencia);
    return $ejecucion->execute([":id" => $idUsuario]);
}

public function ObtenerDatosUsuariosAdmin(){

    $datos = [];

    $consultas = [

        "total" => "
            SELECT 
                (SELECT COUNT(*) FROM usuario)
                +
                (SELECT COUNT(*) FROM empresa)
        ",

        "activos" => "
        SELECT 
        (SELECT COUNT(*) FROM usuario WHERE estado = 'activo')
        +
        (SELECT COUNT(*) FROM empresa WHERE estado IN ('activa'))
        ",

        "suspendidos" => "
    SELECT 
        (SELECT COUNT(*) FROM usuario WHERE estado = 'suspendido')
        +
        (SELECT COUNT(*) FROM empresa WHERE estado IN ('suspendida'))
",

        "nuevos_hoy" => "
            SELECT 
                (SELECT COUNT(*) FROM usuario WHERE DATE(fecha_registro) = CURDATE())
                +
                (SELECT COUNT(*) FROM empresa WHERE DATE(fecha_registro) = CURDATE())
        ",

        "nuevos_semana" => "
            SELECT 
                (SELECT COUNT(*) FROM usuario 
                 WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL 7 DAY))
                +
                (SELECT COUNT(*) FROM empresa 
                 WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL 7 DAY))
        ",

        "con_reservas" => "
            SELECT COUNT(DISTINCT id_usuario) 
            FROM reserva
        "
    ];

    foreach($consultas as $clave => $sentencia){

        $ejecucion = $this->pdo->prepare($sentencia);
        $ejecucion->execute();

        $datos[$clave] = $ejecucion->fetchColumn();
    }

    return $datos;
}

public function ObtenerUsuariosAdmin($buscar = "", $tipo = "", $estado = ""){

    $sentencia = "
        SELECT *
        FROM (
            SELECT 
                u.id_usuario AS id,
                u.id_usuario AS id_usuario,
                u.nombre AS nombre,
                u.apellido AS apellido,
                u.email AS email,
                u.fecha_registro AS fecha_registro,
                u.estado AS estado,
                u.id_rol AS id_rol,
                'usuario' AS tipo_cuenta,

                (
                    SELECT COUNT(*) 
                    FROM reserva r
                    WHERE r.id_usuario = u.id_usuario
                ) AS total_reservas,

                (
                    SELECT COUNT(*) 
                    FROM resena rs
                    WHERE rs.id_usuario = u.id_usuario
                ) AS total_resenas

            FROM usuario u

            UNION ALL

            SELECT 
                e.id_empresa AS id,
                e.id_empresa AS id_usuario,
                e.nombre_empresa AS nombre,
                '' AS apellido,
                e.email AS email,
                e.fecha_registro AS fecha_registro,
                e.estado AS estado,
                2 AS id_rol,
                'empresa' AS tipo_cuenta,

                (
                    SELECT COUNT(*) 
                    FROM reserva r
                    INNER JOIN servicio s ON r.id_servicio = s.id_servicio
                    WHERE s.id_empresa = e.id_empresa
                ) AS total_reservas,

                0 AS total_resenas

            FROM empresa e
        ) cuentas
        WHERE 1=1
    ";

    $parametros = [];

    if($buscar != ""){
        $sentencia .= " AND (
                            nombre LIKE :buscar
                            OR apellido LIKE :buscar
                            OR email LIKE :buscar
                        )";
        $parametros[":buscar"] = "%" . $buscar . "%";
    }

    if($tipo != ""){

        if($tipo == "admin"){
            $sentencia .= " AND tipo_cuenta = 'usuario' AND id_rol = 3";

        }else if($tipo == "cliente"){
            $sentencia .= " AND tipo_cuenta = 'usuario' AND id_rol != 3";

        }else if($tipo == "empresa"){
            $sentencia .= " AND tipo_cuenta = 'empresa'";

        }else{
            $sentencia .= " AND tipo_cuenta = 'usuario' AND id_rol = :tipo";
            $parametros[":tipo"] = $tipo;
        }
    }

    if($estado != ""){
        $sentencia .= " AND estado = :estado";
        $parametros[":estado"] = $estado;
    }

    $sentencia .= " ORDER BY fecha_registro DESC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute($parametros);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerReservasUsuarioAdmin($idUsuario){

    $sentencia = "SELECT 
                    r.*,
                    s.nombre_servicio,
                    s.lugar,
                    u.nombre,
                    u.apellido,
                    u.email,
                    d.fecha,
                    d.hora_inicio,
                    d.hora_fin
                  FROM reserva r
                  INNER JOIN usuario u ON r.id_usuario = u.id_usuario
                  INNER JOIN servicio s ON r.id_servicio = s.id_servicio
                  LEFT JOIN detalle_actividad d ON r.id_detalle_actividad = d.id
                  WHERE r.id_usuario = :id_usuario
                  ORDER BY r.fecha_hora DESC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_usuario" => $idUsuario
    ]);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerResumenReportes(){

    $datos = [];

    $consultas = [
        "total_reservas" => "SELECT COUNT(*) FROM reserva",
        "reservas_confirmadas" => "SELECT COUNT(*) FROM reserva WHERE estado = 'confirmada'",
        "reservas_canceladas" => "SELECT COUNT(*) FROM reserva WHERE estado = 'cancelada'",
        "usuarios_activos" => "SELECT COUNT(*) FROM usuario WHERE estado = 'activo'",
        "empresas_total" => "SELECT COUNT(*) FROM empresa",
        "actividades_activas" => "SELECT COUNT(*) FROM servicio WHERE estado = 'activo'"
    ];

    foreach($consultas as $clave => $sentencia){
        $ejecucion = $this->pdo->prepare($sentencia);
        $ejecucion->execute();
        $datos[$clave] = $ejecucion->fetchColumn();
    }

    return $datos;
}

public function ObtenerActividadesMasReservadas(){

    $sentencia = "SELECT 
                s.id_servicio,
                s.nombre_servicio,
                e.nombre_empresa,
                COUNT(r.id_reserva) AS total_reservas
            FROM servicio s
            LEFT JOIN reserva r ON s.id_servicio = r.id_servicio
            INNER JOIN empresa e ON s.id_empresa = e.id_empresa
            GROUP BY s.id_servicio, s.nombre_servicio, e.nombre_empresa
            ORDER BY total_reservas DESC
            LIMIT 5";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerEmpresasConMasReservas(){

    $sentencia = "SELECT 
                e.id_empresa,
                e.nombre_empresa,
                COUNT(r.id_reserva) AS total_reservas
            FROM empresa e
            LEFT JOIN servicio s ON e.id_empresa = s.id_empresa
            LEFT JOIN reserva r ON s.id_servicio = r.id_servicio
            GROUP BY e.id_empresa, e.nombre_empresa
            ORDER BY total_reservas DESC
            LIMIT 5";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerUsuariosMasActivos(){

    $sentencia = "SELECT 
                u.id_usuario,
                u.nombre,
                u.apellido,
                u.email,
                COUNT(r.id_reserva) AS total_reservas
            FROM usuario u
            LEFT JOIN reserva r ON u.id_usuario = r.id_usuario
            GROUP BY u.id_usuario, u.nombre, u.apellido, u.email
            ORDER BY total_reservas DESC
            LIMIT 5";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerCategoriasMasPopulares(){

    $sentencia = "SELECT 
                COALESCE(cp.nombre, c.nombre) AS categoria,
                COUNT(r.id_reserva) AS total_reservas
            FROM categoria c
            LEFT JOIN categoria cp ON c.id_categoria_padre = cp.id_categoria
            INNER JOIN servicio s ON s.id_categoria = c.id_categoria
            LEFT JOIN reserva r ON s.id_servicio = r.id_servicio
            GROUP BY categoria
            ORDER BY total_reservas DESC
            LIMIT 5";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function SuspenderEmpresa($idEmpresa){

    try{

        $this->pdo->beginTransaction();

        // suspender empresa
        $sentencia = "UPDATE empresa
                SET estado = 'suspendida'
                WHERE id_empresa = :id_empresa";

        $ejecucion = $this->pdo->prepare($sentencia);
        $ejecucion->execute([
            ":id_empresa" => $idEmpresa
        ]);

        // cancelar servicios
        $sentencia2 = "UPDATE servicio
                 SET estado = 'cancelado'
                 WHERE id_empresa = :id_empresa";

        $ejecucion2 = $this->pdo->prepare($sentencia2);
        $ejecucion2->execute([
            ":id_empresa" => $idEmpresa
        ]);

        // cancelar reservas asociadas
        $sentencia3 = "UPDATE reserva r
                 INNER JOIN servicio s 
                 ON r.id_servicio = s.id_servicio
                 SET r.estado = 'cancelada'
                 WHERE s.id_empresa = :id_empresa";

        $ejecucion3 = $this->pdo->prepare($sentencia3);
        $ejecucion3->execute([
            ":id_empresa" => $idEmpresa
        ]);

        $this->pdo->commit();

        return true;

    }catch(Exception $e){

        $this->pdo->rollBack();
        return false;
    }
}

public function ActivarEmpresa($idEmpresa){

    $sentencia = "UPDATE empresa 
            SET estado = 'activa'
            WHERE id_empresa = :id_empresa";

    $ejecucion = $this->pdo->prepare($sentencia);
    return $ejecucion->execute([
        ":id_empresa" => $idEmpresa
    ]);
}

public function ObtenerCategoriasAdmin(){

    $sentencia = "SELECT 
                c.id_categoria,
                c.nombre,
                c.id_categoria_padre,
                cp.nombre AS categoria_padre
            FROM categoria c
            LEFT JOIN categoria cp ON c.id_categoria_padre = cp.id_categoria
            ORDER BY cp.nombre ASC, c.nombre ASC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerCategoriasPadreAdmin(){

    $sentencia = "SELECT * 
            FROM categoria 
            WHERE id_categoria_padre IS NULL
            ORDER BY nombre ASC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ExisteCategoriaAdmin($nombre, $idPadre){

    if($idPadre == null){
        $sentencia = "SELECT id_categoria 
                FROM categoria 
                WHERE nombre = :nombre 
                AND id_categoria_padre IS NULL
                LIMIT 1";

        $ejecucion = $this->pdo->prepare($sentencia);
        $ejecucion->execute([":nombre" => $nombre]);
    }else{
        $sentencia = "SELECT id_categoria 
                FROM categoria 
                WHERE nombre = :nombre 
                AND id_categoria_padre = :id_padre
                LIMIT 1";

        $ejecucion = $this->pdo->prepare($sentencia);
        $ejecucion->execute([
            ":nombre" => $nombre,
            ":id_padre" => $idPadre
        ]);
    }

    return $ejecucion->fetch(PDO::FETCH_ASSOC) != false;
}

public function CrearCategoriaAdmin($nombre, $idPadre){

    $sentencia = "INSERT INTO categoria (nombre, id_categoria_padre)
            VALUES (:nombre, :id_padre)";

    $ejecucion = $this->pdo->prepare($sentencia);

    return $ejecucion->execute([
        ":nombre" => $nombre,
        ":id_padre" => $idPadre
    ]);
}

public function CategoriaTieneHijas($idCategoria){

    $sentencia = "SELECT COUNT(*) 
            FROM categoria 
            WHERE id_categoria_padre = :id";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([":id" => $idCategoria]);

    return $ejecucion->fetchColumn() > 0;
}

public function CategoriaTieneServicios($idCategoria){

    $sentencia = "SELECT COUNT(*) 
            FROM servicio 
            WHERE id_categoria = :id";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([":id" => $idCategoria]);

    return $ejecucion->fetchColumn() > 0;
}

public function EliminarCategoriaAdmin($idCategoria){

    if($this->CategoriaTieneHijas($idCategoria)){
        return "tiene_subcategorias";
    }

    if($this->CategoriaTieneServicios($idCategoria)){
        return "tiene_servicios";
    }

    $sentencia = "DELETE FROM categoria 
            WHERE id_categoria = :id";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([":id" => $idCategoria]);

    return "ok";
}

public function ObtenerEmpresaAprobadaPorId($idEmpresa){

    $sentencia = "SELECT 
                e.*,
                (
                    SELECT COUNT(*) 
                    FROM servicio s
                    WHERE s.id_empresa = e.id_empresa
                ) AS total_servicios,
                (
                    SELECT COUNT(*) 
                    FROM servicio s
                    INNER JOIN reserva r ON s.id_servicio = r.id_servicio
                    WHERE s.id_empresa = e.id_empresa
                ) AS total_reservas
            FROM empresa e
            WHERE e.id_empresa = :id_empresa
            LIMIT 1";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_empresa" => $idEmpresa
    ]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC);
}

public function ObtenerCategoriasFiltroAdmin(){
    $sentencia = "SELECT id_categoria, nombre
            FROM categoria
            WHERE id_categoria_padre IS NULL
            ORDER BY nombre ASC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerSubcategoriasFiltroAdmin(){
    $sentencia = "SELECT id_categoria, nombre
            FROM categoria
            WHERE id_categoria_padre IS NOT NULL
            ORDER BY nombre ASC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerEstadosReservaFiltroAdmin(){
    $sentencia = "SELECT DISTINCT estado
            FROM reserva
            ORDER BY estado ASC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerCiudadesEmpresasPendientes(){

    $sentencia = "SELECT DISTINCT ciudad_empresa
            FROM solicitud_empresa
            WHERE ciudad_empresa IS NOT NULL
            AND ciudad_empresa <> ''
            ORDER BY ciudad_empresa ASC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerEstadosEmpresas(){

    $sentencia = "SELECT DISTINCT estado
            FROM empresa
            WHERE estado IS NOT NULL
            AND estado <> ''
            ORDER BY estado ASC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerEstadosServicios(){

    $sentencia = "SELECT DISTINCT estado
            FROM servicio
            WHERE estado IS NOT NULL
            AND estado <> ''
            ORDER BY estado ASC";

    $ejecucuion = $this->pdo->prepare($sentencia);
    $ejecucuion->execute();

    return $ejecucuion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerTiposUsuario(){

    $sentencia = "SELECT id_rol, nombre
            FROM rol
            ORDER BY nombre ASC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerEstadosUsuario(){

    $sentencia = "SELECT DISTINCT estado
            FROM usuario
            WHERE estado IS NOT NULL
            AND estado <> ''
            ORDER BY estado ASC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function CambiarRolUsuario($idUsuario, $nuevoRol){

    $sentencia = "UPDATE usuario
                  SET id_rol = :nuevoRol
                  WHERE id_usuario = :idUsuario";

    $ejecucion = $this->pdo->prepare($sentencia);
    return $ejecucion->execute([
        ":nuevoRol" => $nuevoRol,
        ":idUsuario" => $idUsuario
    ]);
}

}
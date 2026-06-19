<?php


class bdempresa{

public $pdo = "";

public function __construct($host,$port,$db,$user,$pass) { 
    $this->pdo = new PDO("mysql:host=".$host.";port=".$port.";dbname=".$db.";charset=utf8", $user, $pass);
    }

public function ComprobarLoginEmpresa($email, $contraseña){

    $sentencia = "SELECT * FROM empresa WHERE email = :email LIMIT 1";
    $ejecuccion = $this->pdo->prepare($sentencia);
    $ejecuccion->execute([
        ":email" => $email
    ]);

    $fila = $ejecuccion->fetch(PDO::FETCH_ASSOC);

    //Si no saca resultados es que no existe
    if($fila == false){
        return "empresanoexiste";
    }else{

        $contrabuena = $fila["contrasena"];
        //compruebo que la contraseña es la adecuada con pasword verify y devuelvo el id
        if(password_verify($contraseña, $contrabuena) == true){
            
            return $fila["id_empresa"]; 
        
        }else{
    return "fallocontraseña";
        }
    }
}


//Esto lo hará el administrador
public function AprobarEmpresa($idSolicitud){

    try{
        $this->pdo->beginTransaction();

        $sentencia = "SELECT * FROM solicitud_empresa 
                      WHERE id_solicitud = :id_solicitud 
                      AND estado = 'pendiente'";

        $ejecucion = $this->pdo->prepare($sentencia);
        $ejecucion->execute([
            ":id_solicitud" => $idSolicitud
        ]);

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

        $idEmpresaNueva = $this->pdo->lastInsertId();

        $sentencia3 = "UPDATE solicitud_empresa
                       SET estado = 'aprobada', id_empresa = :id_empresa
                       WHERE id_solicitud = :id_solicitud";

        $ejecucion3 = $this->pdo->prepare($sentencia3);
        $ejecucion3->execute([
            ":id_empresa" => $idEmpresaNueva,
            ":id_solicitud" => $idSolicitud
        ]);

        $this->pdo->commit();
        return true;

    }catch(PDOException $e){
        $this->pdo->rollBack();
        return false;
    }
}

public function RechazarEmpresa($idSolicitud){

    $sentencia = "UPDATE solicitud_empresa 
                  SET estado = 'rechazada' 
                  WHERE id_solicitud = :id";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id" => $idSolicitud
    ]);
}

public function ExisteEmpresa($email){

    $sentencia = "SELECT * FROM empresa WHERE email = :email LIMIT 1";
    $ejecuccion = $this->pdo->prepare($sentencia);
    $ejecuccion->execute([
        ":email" => $email
    ]);

    $fila = $ejecuccion->fetch(PDO::FETCH_ASSOC);

    if($fila == false){
        return false;
    }else{
        return true;
    }
}

public function RegistrarSolicitudEmpresa($nombre, $email, $logo, $ciudad, $telefono, $direccion, $contrasena, $categoria, $descripcion){

    $contraCifrada = password_hash($contrasena, PASSWORD_DEFAULT);

    $sentencia = "INSERT INTO solicitud_empresa 
    (nombre, email, logo_empresa, ciudad_empresa, telefono, direccion, contrasena, categoria_empresa, datos)
    VALUES 
    (:nombre, :email, :logo, :ciudad, :telefono, :direccion, :contra, :categoria, :datos)";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":nombre" => $nombre,
        ":email" => $email,
        ":logo" => $logo,
        ":ciudad" => $ciudad,
        ":telefono" => $telefono,
        ":direccion" => $direccion,
        ":contra" => $contraCifrada,
        ":categoria" => $categoria,
        ":datos" => $descripcion
    ]);
}

public function ObtenerSolicitudEmpresaPorId($idSolicitud){

    $sentencia = "SELECT * FROM solicitud_empresa WHERE id_solicitud = :id";
    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id" => $idSolicitud
    ]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC);
}

public function ModificarPerfilEmpresa($idEmpresa, $nombre, $categoria, $telefono, $ciudad, $direccion, $descripcion, $logo){

    $sentencia = "UPDATE empresa 
                  SET nombre_empresa = :nombre,
                      categoria_empresa = :categoria,
                      telefono = :telefono,
                      ciudad_empresa = :ciudad,
                      direccion = :direccion,
                      descripcion_empresa = :descripcion,
                      logo_empresa = :logo
                  WHERE id_empresa = :idEmpresa";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":nombre" => $nombre,
        ":categoria" => $categoria,
        ":telefono" => $telefono,
        ":ciudad" => $ciudad,
        ":direccion" => $direccion,
        ":descripcion" => $descripcion,
        ":logo" => $logo,
        ":idEmpresa" => $idEmpresa
    ]);
}


public function sacardatosempresa($idEmpresa){

    $sentencia = "SELECT * FROM empresa WHERE id_empresa = :id LIMIT 1";
    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":id" => $idEmpresa
    ]);

    $empresa = $ejecucion->fetch(PDO::FETCH_ASSOC);

    return $empresa;
}

public function ObtenerActividadesPorEmpresa($idEmpresa){

    $sentencia = "SELECT * 
                  FROM servicio 
                  WHERE id_empresa = :idEmpresa";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":idEmpresa" => $idEmpresa
    ]);

    $actividades = $ejecucion->fetchAll(PDO::FETCH_ASSOC);

    return $actividades;
}


public function ObtenerServiciosEmpresa($idEmpresa){

    $sentencia = "SELECT 
                    s.*,
                    c.nombre AS subcategoria,
                    cp.nombre AS categoria_padre,

                    (
                        SELECT i.url_imagen 
                        FROM imagen_servicio i
                        WHERE i.id_servicio = s.id_servicio
                        LIMIT 1
                    ) AS imagen,

                    (
                        SELECT COUNT(*) 
                        FROM detalle_actividad d
                        WHERE d.id_servicio = s.id_servicio
                    ) AS total_sesiones

                  FROM servicio s
                  INNER JOIN categoria c 
                    ON s.id_categoria = c.id_categoria
                  LEFT JOIN categoria cp 
                    ON c.id_categoria_padre = cp.id_categoria
                  WHERE s.id_empresa = :idEmpresa
                  ORDER BY s.id_servicio DESC";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":idEmpresa" => $idEmpresa
    ]);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerSubcategoriasEmpresa($idEmpresa){

    $sentencia = "SELECT DISTINCT c.id_categoria, c.nombre
                  FROM servicio s
                  INNER JOIN categoria c ON s.id_categoria = c.id_categoria
                  WHERE s.id_empresa = :idEmpresa";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":idEmpresa" => $idEmpresa
    ]);

    $subcategorias = $ejecucion->fetchAll(PDO::FETCH_ASSOC);

    return $subcategorias;
}

public function ObtenerServiciosActivos($idEmpresa){

    $sentencia = "SELECT * 
                  FROM servicio 
                  WHERE id_empresa = :idEmpresa AND estado = :estado";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":idEmpresa" => $idEmpresa,
        ":estado" => "activo"
    ]);

    $actividades = $ejecucion->fetchAll(PDO::FETCH_ASSOC);

    return $actividades;
}

public function ObtenerReservasEmpresa($idempresa){

    $sentencia = "SELECT 
                    r.id_reserva,
                    r.fecha_hora,
                    r.estado,
                    r.id_detalle_actividad,

                    u.nombre AS nombre_usuario,
                    u.apellido AS apellido_usuario,
                    u.email AS email_usuario,

                    s.id_servicio,
                    s.nombre_servicio,
                    s.descripcion,
                    s.lugar,

                    c.nombre AS subcategoria,
                    cp.nombre AS categoria_padre

                  FROM reserva r
                  INNER JOIN usuario u ON r.id_usuario = u.id_usuario
                  INNER JOIN servicio s ON r.id_servicio = s.id_servicio
                  INNER JOIN categoria c ON s.id_categoria = c.id_categoria
                  LEFT JOIN categoria cp ON c.id_categoria_padre = cp.id_categoria

                  WHERE s.id_empresa = :idEmpresa
                  ORDER BY r.fecha_hora DESC";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":idEmpresa" => $idempresa
    ]);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function CancelarServicioEmpresa($idServicio, $idEmpresa){

    try{
        $this->pdo->beginTransaction();

        // Cancelar el servicio
        $sentencia = "UPDATE servicio
                      SET estado = 'cancelado'
                      WHERE id_servicio = :idServicio
                      AND id_empresa = :idEmpresa";

        $ejecucion = $this->pdo->prepare($sentencia);
        $ejecucion->execute([
            ":idServicio" => $idServicio,
            ":idEmpresa" => $idEmpresa
        ]);
        //Obtenemos los usuarios con reservas
        $usuariosAfectados = $this->ObtenerUsuariosReservasServicio($idServicio);

        // Cancelar todas las reservas confirmadas de ese servicio
        $sentencia = "UPDATE reserva
                      SET estado = 'cancelada'
                      WHERE id_servicio = :idServicio
                      AND estado = 'confirmada'";

        $ejecucion = $this->pdo->prepare($sentencia);
        $ejecucion->execute([
            ":idServicio" => $idServicio
        ]);

        $this->pdo->commit();

        require_once("../utils/mailer.php");

        foreach($usuariosAfectados as $usuario){

            $datosReserva = [

                "actividad" => $usuario["nombre_servicio"],
                "fecha" => $usuario["fecha"],
                "hora" => $usuario["hora_inicio"],
                "duracion" => $usuario["duracion"],
                "ubicacion" => $usuario["lugar"],
                "empresa" => $usuario["nombre_empresa"],
                "telefono" => $usuario["telefono_empresa"]

            ];

            enviarCorreoCancelacion(
                $usuario["email"],
                $usuario["nombre"],
                $datosReserva
            );
        }

        return true;

    }catch(Exception $e){
        $this->pdo->rollBack();
        return false;
    }
}

public function ObtenerUsuariosReservasServicio($idServicio){

    $sentencia = "SELECT 
                    u.nombre,
                    u.email,

                    s.nombre_servicio,
                    s.lugar,
                    s.duracion,

                    d.fecha,
                    d.hora_inicio,

                    e.nombre_empresa,
                    e.telefono AS telefono_empresa

                  FROM reserva r

                  INNER JOIN usuario u
                    ON r.id_usuario = u.id_usuario

                  INNER JOIN servicio s
                    ON r.id_servicio = s.id_servicio

                  INNER JOIN detalle_actividad d
                    ON r.id_detalle_actividad = d.id

                  INNER JOIN empresa e
                    ON s.id_empresa = e.id_empresa

                  WHERE r.id_servicio = :idServicio
                  AND r.estado = 'confirmada'";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":idServicio" => $idServicio
    ]);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ExisteServicioEmpresa($idEmpresa, $nombreServicio){

    $sql = "SELECT id_servicio 
            FROM servicio 
            WHERE id_empresa = :id_empresa 
            AND nombre_servicio = :nombre_servicio
            LIMIT 1";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        ":id_empresa" => $idEmpresa,
        ":nombre_servicio" => $nombreServicio
    ]);

    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if($fila == false){
        return false;
    }

    return true;
}

public function InsertarServicio($idEmpresa, $nombre, $descripcion, $lugar, $idCategoria, $precio, $duracion, $materiales, $idMunicipio, $codigoPostal, $latitud, $longitud){

    $sentencia = "INSERT INTO servicio 
            (id_empresa, nombre_servicio, descripcion, lugar, id_categoria, precio, duracion, materiales, estado, id_municipio, codigo_postal, latitud, longitud)
            VALUES 
            (:id_empresa, :nombre, :descripcion, :lugar, :id_categoria, :precio, :duracion, :materiales, :estado, :id_municipio, :codigo_postal, :latitud, :longitud)";

    $ejecuccion = $this->pdo->prepare($sentencia);

    $ejecuccion->execute([
        ":id_empresa" => $idEmpresa,
        ":nombre" => $nombre,
        ":descripcion" => $descripcion,
        ":lugar" => $lugar,
        ":id_categoria" => $idCategoria,
        ":precio" => $precio,
        ":duracion" => $duracion,
        ":materiales" => $materiales, 
        ":estado" => "cancelado",
        ":id_municipio" => $idMunicipio,
        ":codigo_postal" => $codigoPostal,
        ":latitud" => $latitud,
        ":longitud" => $longitud
    ]);

    return $this->pdo->lastInsertId();
}

public function InsertarImagenServicio($idServicio, $rutaImagen){

    $sql = "INSERT INTO imagen_servicio (id_servicio, url_imagen)
            VALUES (:id_servicio, :ruta)";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        ":id_servicio" => $idServicio,
        ":ruta" => $rutaImagen
    ]);
}

public function InsertarDetalleActividad($idServicio, $fecha, $horaInicio, $horaFin, $plazasMaximas){

    $sentencia = "INSERT INTO detalle_actividad
                  (id_servicio, fecha, hora_inicio, hora_fin, plazas_maximas)
                  VALUES
                  (:id_servicio, :fecha, :hora_inicio, :hora_fin, :plazas_maximas)";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":id_servicio" => $idServicio,
        ":fecha" => $fecha,
        ":hora_inicio" => $horaInicio,
        ":hora_fin" => $horaFin,
        ":plazas_maximas" => $plazasMaximas
    ]);
}

public function ActivarServicio($idServicio, $idEmpresa){

    $sentencia = "SELECT estado 
                  FROM empresa 
                  WHERE id_empresa = :id_empresa";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_empresa" => $idEmpresa
    ]);

    $empresa = $ejecucion->fetch(PDO::FETCH_ASSOC);

    if($empresa == false || $empresa["estado"] == "suspendida"){
        return false;
    }

    $sentencia = "UPDATE servicio 
                  SET estado = 'activo'
                  WHERE id_servicio = :id_servicio
                  AND id_empresa = :id_empresa";

    $ejecucion = $this->pdo->prepare($sentencia);

    return $ejecucion->execute([
        ":id_servicio" => $idServicio,
        ":id_empresa" => $idEmpresa
    ]);
}

public function ServicioTieneHorarios($idServicio){

    $sentencia = "SELECT id 
                  FROM detalle_actividad 
                  WHERE id_servicio = :id_servicio 
                  LIMIT 1";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":id_servicio" => $idServicio
    ]);

    $fila = $ejecucion->fetch(PDO::FETCH_ASSOC);

    if($fila == false){
        return false;
    }

    return true;
}

public function ActualizarServicio($idServicio, $idEmpresa, $nombre, $descripcion, $lugar, $idCategoria, $precio, $duracion, $materiales, $idMunicipio, $codigoPostal){

    $sentencia = "UPDATE servicio
                  SET nombre_servicio = :nombre,
                      descripcion = :descripcion,
                      lugar = :lugar,
                      id_categoria = :id_categoria,
                      precio = :precio,
                      duracion = :duracion,
                      materiales = :materiales,
                      id_municipio = :id_municipio,
                      codigo_postal = :codigo_postal
                  WHERE id_servicio = :id_servicio
                  AND id_empresa = :id_empresa";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":nombre" => $nombre,
        ":descripcion" => $descripcion,
        ":lugar" => $lugar,
        ":id_categoria" => $idCategoria,
        ":precio" => $precio,
        ":duracion" => $duracion,
        ":materiales" => $materiales,
        ":id_municipio" => $idMunicipio,
        ":codigo_postal" => $codigoPostal,
        ":id_servicio" => $idServicio,
        ":id_empresa" => $idEmpresa
    ]);
}

function DuracionTextoAMinutos($duracion){

    $minutosTotales = 0;

    if(preg_match('/(\d+)\s*hora/', $duracion, $matchHoras)){
        $minutosTotales += ((int)$matchHoras[1]) * 60;
    }

    if(preg_match('/(\d+)\s*minuto/', $duracion, $matchMinutos)){
        $minutosTotales += (int)$matchMinutos[1];
    }

    return $minutosTotales;
}

public function obtenerActividadPorIdempresa($idServicio){
    $sentencia = "SELECT * FROM servicio WHERE id_servicio = :id_servicio";
    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_servicio" => $idServicio
    ]);
    $fila = $ejecucion->fetch(PDO::FETCH_ASSOC);
    return $fila;
}

public function ObtenerProvincias(){

    $sentencia = "SELECT *
                  FROM provincia
                  ORDER BY nombre ASC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute();

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerMunicipiosPorProvincia($idProvincia){

    $sentencia = "SELECT *
                  FROM municipio
                  WHERE id_provincia = :id_provincia
                  ORDER BY nombre ASC";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":id_provincia" => $idProvincia
    ]);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerMunicipioPorId($idMunicipio){

    $sentencia = "SELECT 
                    m.*,
                    p.nombre AS provincia
                  FROM municipio m
                  INNER JOIN provincia p
                    ON m.id_provincia = p.id_provincia
                  WHERE m.id_municipio = :id_municipio";

    $ejecucion = $this->pdo->prepare($sentencia);

    $ejecucion->execute([
        ":id_municipio" => $idMunicipio
    ]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC);
}

public function ObtenerCoordenadas($direccionMapa){

    $url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q=" . urlencode($direccionMapa);

    $opciones = [
        "http" => [
            "header" => "User-Agent: BodyAndSoulTFG/1.0\r\n"
        ]
    ];

    $contexto = stream_context_create($opciones);

    $respuesta = file_get_contents($url, false, $contexto);

    if($respuesta === false){
        return false;
    }

    $datos = json_decode($respuesta, true);

    if(empty($datos)){
        return false;
    }

    return [
        "latitud" => $datos[0]["lat"],
        "longitud" => $datos[0]["lon"]
    ];
}

public function ObtenerHorariosServicioEmpresa($idServicio, $idEmpresa){

    $sentencia = "SELECT d.*
                  FROM detalle_actividad d
                  INNER JOIN servicio s ON d.id_servicio = s.id_servicio
                  WHERE d.id_servicio = :id_servicio
                  AND s.id_empresa = :id_empresa
                  ORDER BY d.fecha ASC, d.hora_inicio ASC";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_servicio" => $idServicio,
        ":id_empresa" => $idEmpresa
    ]);

    return $ejecucion->fetchAll(PDO::FETCH_ASSOC);
}

public function ObtenerHorarioPorIdEmpresa($idHorario, $idEmpresa){

    $sentencia = "SELECT d.*
                  FROM detalle_actividad d
                  INNER JOIN servicio s ON d.id_servicio = s.id_servicio
                  WHERE d.id = :id_horario
                  AND s.id_empresa = :id_empresa
                  LIMIT 1";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_horario" => $idHorario,
        ":id_empresa" => $idEmpresa
    ]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC);
}

public function ActualizarHorarioEmpresa($idHorario, $idEmpresa, $fecha, $horaInicio, $horaFin, $plazasMaximas){

    $sentencia = "UPDATE detalle_actividad d
                  INNER JOIN servicio s ON d.id_servicio = s.id_servicio
                  SET d.fecha = :fecha,
                      d.hora_inicio = :hora_inicio,
                      d.hora_fin = :hora_fin,
                      d.plazas_maximas = :plazas_maximas
                  WHERE d.id = :id_horario
                  AND s.id_empresa = :id_empresa";

    $ejecucion = $this->pdo->prepare($sentencia);

    return $ejecucion->execute([
        ":fecha" => $fecha,
        ":hora_inicio" => $horaInicio,
        ":hora_fin" => $horaFin,
        ":plazas_maximas" => $plazasMaximas,
        ":id_horario" => $idHorario,
        ":id_empresa" => $idEmpresa
    ]);
}

public function EliminarHorarioEmpresa($idHorario, $idEmpresa){

    $sentencia = "DELETE d
                  FROM detalle_actividad d
                  INNER JOIN servicio s ON d.id_servicio = s.id_servicio
                  WHERE d.id = :id_horario
                  AND s.id_empresa = :id_empresa";

    $ejecucion = $this->pdo->prepare($sentencia);

    return $ejecucion->execute([
        ":id_horario" => $idHorario,
        ":id_empresa" => $idEmpresa
    ]);
}

public function HorarioTieneReservasConfirmadas($idHorario){

    $sentencia = "SELECT id_reserva
                  FROM reserva
                  WHERE id_detalle_actividad = :id_horario
                  AND estado = 'confirmada'
                  LIMIT 1";

    $ejecucion = $this->pdo->prepare($sentencia);
    $ejecucion->execute([
        ":id_horario" => $idHorario
    ]);

    return $ejecucion->fetch(PDO::FETCH_ASSOC) != false;
}

}

?>
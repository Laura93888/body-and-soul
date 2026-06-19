<?php
session_start();

// Borrar sesión
session_unset();
session_destroy();

// Redirigir a la HOME (index público)
header("Location: ../publico/index.php");
exit();

?>
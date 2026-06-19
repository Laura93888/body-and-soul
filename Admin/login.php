<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Body and Soul | Acceso administrador</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Overpass:wght@300;400;500;600;700&family=Sansita:wght@700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/admin-styles/admin-login.css">
</head>
<body class="admin-login-body">

  <main class="admin-login-page">
    <section class="admin-login-section">
      <div class="admin-login-wrapper">

        <div class="admin-login-card">
          <div class="admin-login-glow"></div>

          <div class="admin-login-header">
            <img src="../assets/logo-body-and-soul.png" alt="Logo Body and Soul" class="admin-login-logo">
            <span class="admin-badge">Acceso interno</span>
            <h1>Panel de administración</h1>
            <p>
              Inicia sesión para gestionar empresas, actividades y validaciones de la plataforma.
            </p>
          </div>

          <form action="#" method="post" class="admin-login-form">
            <div class="form-group">
              <label for="email">Correo electrónico</label>
              <input
                type="email"
                id="email"
                name="email"
                placeholder="Introduce tu correo"
                required
              >
            </div>

            <div class="form-group">
              <label for="password">Contraseña</label>
              <input
                type="password"
                id="password"
                name="password"
                placeholder="Introduce tu contraseña"
                required
              >
            </div>

            <button type="submit" class="admin-login-btn">
              Entrar al panel
            </button>
          </form>

          <p class="admin-login-help">
            Acceso exclusivo para administración.
          </p>
        </div>

      </div>
    </section>
  </main>

</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Body and Soul | Historial de reservas</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Overpass:wght@300;400;500;600;700&family=Sansita:wght@700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/public-styles/historial.css">
</head>
<body>

  <header class="main-header">
    <div class="container header-container">

      <div class="header-left">
        <a href="index.html" class="logo-link" aria-label="Ir al inicio">
          <img src="assets/logo-body-and-soul.png" class="logo" alt="Body and Soul">
        </a>

        <div class="nav-categories">
          <label for="categorias" class="sr-only">Categorías</label>
          <select id="categorias" class="categories-select">
            <option value="">Categorías</option>
            <option value="deporte">Deporte</option>
            <option value="bienestar">Bienestar</option>
          </select>
        </div>
      </div>

      <div class="header-title">
        <h1>Bienvenida a Body and Soul</h1>
      </div>

      <div class="header-right">
        <a href="perfil.html" class="btn btn-outline">Mi perfil</a>
      </div>

    </div>
  </header>

  <main class="history-page">
    <section class="history-section">
      <div class="container">

        <div class="history-intro">
          <span class="section-tag">Área personal</span>
          <h2>Historial de reservas</h2>
          <p>
            Consulta las actividades que ya has realizado, revisa sus detalles y vuelve a reservar
            aquellas experiencias que más te hayan gustado.
          </p>
        </div>

        <div class="history-filters">
          <button class="history-chip active" type="button">Todas</button>
          <button class="history-chip" type="button">Bienestar</button>
          <button class="history-chip" type="button">Deporte</button>
          <button class="history-chip" type="button">Valoradas</button>
        </div>

        <div class="history-list">

          <!-- RESERVA 1 -->
          <article class="history-card">
            <div class="history-image">
              <img src="assets/yoga-historial.jpg" alt="Actividad Yoga Flow">
            </div>

            <div class="history-content">
              <div class="history-top">
                <div>
                  <p class="history-category">Bienestar · Yoga</p>
                  <h3>Yoga Flow</h3>
                </div>

                <span class="history-status completed">Realizada</span>
              </div>

              <p class="history-description">
                Sesión de yoga enfocada en respiración, movilidad y relajación final en un ambiente tranquilo.
              </p>

              <div class="history-info-grid">
                <div class="history-info-item">
                  <span class="info-label">Fecha</span>
                  <span class="info-value">02/03/2026</span>
                </div>

                <div class="history-info-item">
                  <span class="info-label">Hora</span>
                  <span class="info-value">18:00</span>
                </div>

                <div class="history-info-item">
                  <span class="info-label">Duración</span>
                  <span class="info-value">60 min</span>
                </div>

                <div class="history-info-item">
                  <span class="info-label">Ubicación</span>
                  <span class="info-value">Madrid Centro</span>
                </div>
              </div>

              <div class="history-actions">
                <a href="actividad.html" class="btn btn-outline">Ver actividad</a>
                <a href="reserva.html" class="btn btn-secondary">Reservar de nuevo</a>
                <a href="mis-valoraciones.html" class="btn btn-primary">Valorar</a>
              </div>
            </div>
          </article>

          <!-- RESERVA 2 -->
          <article class="history-card">
            <div class="history-image">
              <img src="assets/spa-historial.jpg" alt="Actividad circuito termal premium">
            </div>

            <div class="history-content">
              <div class="history-top">
                <div>
                  <p class="history-category">Bienestar · Spa</p>
                  <h3>Circuito termal premium</h3>
                </div>

                <span class="history-status reviewed">Valorada</span>
              </div>

              <p class="history-description">
                Experiencia wellness con piscina climatizada, sauna, jacuzzi y zona de descanso.
              </p>

              <div class="history-info-grid">
                <div class="history-info-item">
                  <span class="info-label">Fecha</span>
                  <span class="info-value">24/02/2026</span>
                </div>

                <div class="history-info-item">
                  <span class="info-label">Hora</span>
                  <span class="info-value">12:30</span>
                </div>

                <div class="history-info-item">
                  <span class="info-label">Duración</span>
                  <span class="info-value">90 min</span>
                </div>

                <div class="history-info-item">
                  <span class="info-label">Ubicación</span>
                  <span class="info-value">Pozuelo</span>
                </div>
              </div>

              <div class="history-actions">
                <a href="actividad.html" class="btn btn-outline">Ver actividad</a>
                <a href="reserva.html" class="btn btn-secondary">Reservar de nuevo</a>
                <a href="mis-valoraciones.html" class="btn btn-primary">Ver valoración</a>
              </div>
            </div>
          </article>

          <!-- RESERVA 3 -->
          <article class="history-card">
            <div class="history-image">
              <img src="assets/padel-historial.jpg" alt="Actividad pista de pádel indoor">
            </div>

            <div class="history-content">
              <div class="history-top">
                <div>
                  <p class="history-category">Deporte · De raqueta</p>
                  <h3>Pista de pádel indoor</h3>
                </div>

                <span class="history-status completed">Realizada</span>
              </div>

              <p class="history-description">
                Reserva de pista cubierta para partido de pádel con acceso a vestuarios y descanso.
              </p>

              <div class="history-info-grid">
                <div class="history-info-item">
                  <span class="info-label">Fecha</span>
                  <span class="info-value">18/02/2026</span>
                </div>

                <div class="history-info-item">
                  <span class="info-label">Hora</span>
                  <span class="info-value">19:30</span>
                </div>

                <div class="history-info-item">
                  <span class="info-label">Duración</span>
                  <span class="info-value">90 min</span>
                </div>

                <div class="history-info-item">
                  <span class="info-label">Ubicación</span>
                  <span class="info-value">Alcobendas</span>
                </div>
              </div>

              <div class="history-actions">
                <a href="actividad.html" class="btn btn-outline">Ver actividad</a>
                <a href="reserva.html" class="btn btn-secondary">Reservar de nuevo</a>
                <a href="mis-valoraciones.html" class="btn btn-primary">Valorar</a>
              </div>
            </div>
          </article>

        </div>
      </div>
    </section>
  </main>

<?php
require_once("footer.php");
?>
</body>
</html>

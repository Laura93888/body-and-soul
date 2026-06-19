<footer class="main-footer" style="
background:#1b4965;
padding:2% 0;
margin-top:2%;
">

  <div class="container footer-container" style="
  width:90%;
  max-width:1200px;
  margin:auto;
  display:grid;
  grid-template-columns:40% 30% 20%;
  gap:5%;
  align-items:center;
  ">

    <!-- IZQUIERDA -->
    <div style="max-width:100%;">

      <div style="
      display:flex;
      align-items:center;
      gap:3%;
      margin-bottom:3%;
      ">

        <div style="
        width:9%;
        min-width:42px;
        max-width:52px;
        aspect-ratio:1/1;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.3rem;
        ">
            <img 
    src="../img/logounico.png"
    alt="Logo Body and Soul"
    style="
    width:100%;
    height:100%;
    object-fit:cover;
    "
  >
        </div>

        <div style="width:auto;">

          <h3 style="
          color:white;
          margin:0;
          font-size:clamp(1.1rem, 2vw, 1.3rem);
          font-weight:800;
          letter-spacing:-0.5px;
          text-transform:uppercase;
          ">
            Body & Soul
          </h3>

          <div style="
          width:100%;
          height:4px;
          background:#cc4c66;
          border-radius:999px;
          margin:8% 0 0 0;
          "></div>

        </div>

      </div>

      <p style="
      color:rgba(255,255,255,0.86);
      line-height:1.7;
      margin:0 0 3% 0;
      font-size:clamp(0.85rem, 1.2vw, 1rem);
      max-width:100%;
      ">
        Descubre, reserva y disfruta actividades deportivas y de bienestar desde
        una única plataforma sencilla, intuitiva y accesible.
      </p>

    </div>

    <!-- CONTACTO -->
    <div style="
    margin-left:30%;
    min-width:0;
    color:white;
    ">

      <h3 style="
      color:white;
      font-size:clamp(1.1rem, 2vw, 1.3rem);
      font-weight:800;
      margin:0;
      text-transform:uppercase;
      ">
        Contacto
      </h3>

      <div style="
      width:100%;
      max-width:130px;
      height:4px;
      background:#cc4c66;
      border-radius:999px;
      margin:4% 0 6% 0;
      "></div>

      <p style="
      color:rgba(255,255,255,0.92);
      margin:0 0 2% 0;
      font-size:clamp(0.85rem, 1.2vw, 1rem);
      display:flex;
      align-items:center;
      gap:2%;
      white-space:nowrap;
      ">
        <span style="color:#cc4c66; font-size:1.2rem;">✉</span>
        admin@bodyandsoul.com
      </p>

      <p style="
      color:rgba(255,255,255,0.92);
      margin:0;
      font-size:clamp(0.85rem, 1.2vw, 1rem);
      display:flex;
      align-items:center;
      gap:2%;
      white-space:nowrap;
      ">
        <span style="color:#cc4c66; font-size:1.2rem;">☎</span>
        +34 600 123 456
      </p>

    </div>

    <!-- REDES -->
    <div class="footer-social" style="
    display:flex;
    justify-content:flex-end;
    gap:10%;
    flex-wrap:wrap;
    ">

      <a href="https://www.instagram.com/bodyandsoules/" 
         aria-label="Instagram" 
         class="social-link">
         IG
      </a>

      <a href="https://x.com/BodyAndSoul26" 
         aria-label="X" 
         class="social-link">
         X
      </a>

    </div>
    <p style="
      color:rgba(255,255,255,0.65);
      font-size:clamp(0.75rem, 1vw, 0.88rem);
      margin:0;
      ">
        © 2026 Body and Soul. Todos los derechos reservados.
      </p>

  </div>
</footer>
  <script src="../js/favoritos.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const stars = document.querySelectorAll(".stars-input span");
      const input = document.getElementById("puntuacion");

      if(stars.length > 0 && input){

        stars.forEach(star => {

          // Hover
          star.addEventListener("mouseover", function () {
            const val = this.dataset.value;

            stars.forEach(s => {
              s.classList.remove("hover");
              if (s.dataset.value <= val) {
                s.classList.add("hover");
              }
            });
          });

          // Quitar hover
          star.addEventListener("mouseout", function () {
            stars.forEach(s => s.classList.remove("hover"));
          });

          // Click
          star.addEventListener("click", function () {
            const val = this.dataset.value;
            input.value = val;

            stars.forEach(s => {
              s.classList.remove("active");
              if (s.dataset.value <= val) {
                s.classList.add("active");
              }
            });
          });

        });
      }
    });
  </script>


document.addEventListener("DOMContentLoaded", function () {
  let carruseles = document.querySelectorAll(".carousel-wrapper");

  carruseles.forEach(function (carrusel) {
    let botonIzq = carrusel.querySelector(".carousel-btn-left");
    let botonDer = carrusel.querySelector(".carousel-btn-right");
    let track = carrusel.querySelector(".carousel-track");
    let card = carrusel.querySelector(".subcategory-card");

    if (!botonIzq || !botonDer || !track || !card) return;

    let scrollAmount = card.offsetWidth + 20;

    botonDer.addEventListener("click", function () {
      track.scrollBy({ left: scrollAmount, behavior: "smooth" });
    });

    botonIzq.addEventListener("click", function () {
      track.scrollBy({ left: -scrollAmount, behavior: "smooth" });
    });
  });
});


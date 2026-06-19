document.addEventListener("DOMContentLoaded", function () {
  const filtroServicio = document.getElementById("filtroServicio");
  const tarjetas = document.querySelectorAll(".reservation-company-card");
  const mensaje = document.getElementById("mensajeVacio");

  filtroServicio.addEventListener("change", function () {
    const servicioSeleccionado = this.value;
    let visibles = 0;

    tarjetas.forEach(function (tarjeta) {
      const servicioTarjeta = tarjeta.dataset.servicio;

      if (servicioSeleccionado === "" || servicioTarjeta === servicioSeleccionado) {
        tarjeta.style.display = "";
        visibles++;
      } else {
        tarjeta.style.display = "none";
      }
    });

    mensaje.style.display = visibles === 0 ? "block" : "none";
  });
});
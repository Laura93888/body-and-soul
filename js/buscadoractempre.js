document.addEventListener("DOMContentLoaded", function(){

  const buscador = document.getElementById("buscar-servicio");
  const filtroCategoria = document.getElementById("filtro-categoria");
  const filtroEstado = document.getElementById("filtro-estado");
  const tarjetas = document.querySelectorAll(".service-company-card");
  const lista = document.querySelector(".services-list");

  const mensajeVacio = document.createElement("p");
  mensajeVacio.textContent = "No se han encontrado servicios con esos filtros.";
  mensajeVacio.classList.add("services-empty-message");
  mensajeVacio.style.display = "none";

  lista.appendChild(mensajeVacio);

  function filtrarServicios(){

    const texto = buscador.value.toLowerCase().trim();
    const categoria = filtroCategoria.value.toLowerCase();
    const estado = filtroEstado.value.toLowerCase();

    let visibles = 0;

    tarjetas.forEach(function(tarjeta){

      const nombre = tarjeta.dataset.nombre || "";
      const subcategoria = tarjeta.dataset.subcategoria || "";
      const lugar = tarjeta.dataset.lugar || "";
      const estadoServicio = tarjeta.dataset.estado || "";
      const descripcion = tarjeta.dataset.descripcion || "";

      const coincideTexto =
        texto === "" ||
        nombre.includes(texto) ||
        lugar.includes(texto) ||
        descripcion.includes(texto);

      const coincideCategoria =
        categoria === "" || subcategoria === categoria;

      const coincideEstado =
        estado === "" || estadoServicio === estado;

      if(coincideTexto && coincideCategoria && coincideEstado){

        tarjeta.style.display = "";
        visibles++;

      }else{

        tarjeta.style.display = "none";

      }

    });

    mensajeVacio.style.display = visibles === 0 ? "block" : "none";

  }

  buscador.addEventListener("input", filtrarServicios);
  filtroCategoria.addEventListener("change", filtrarServicios);
  filtroEstado.addEventListener("change", filtrarServicios);

});
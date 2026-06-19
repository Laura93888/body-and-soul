document.addEventListener("DOMContentLoaded", function () {

    let categoriaFiltro = document.getElementById("categoria_filtro");
    let subcategoriaFiltro = document.getElementById("subcategoria_filtro");
    let filtrosBuscar = document.getElementById("botonFiltros");
    let buscador = document.getElementById("buscador");
    let precio = document.getElementById("precio");

    categoriaFiltro.addEventListener("change", function () {
        let nombrecat = this.value;

        subcategoriaFiltro.innerHTML = '<option value="">Selecciona una subcategoría</option>';

        if (nombrecat !== "" && subcategoriasPorPadre[nombrecat]) {
            subcategoriasPorPadre[nombrecat].forEach(function(subcat) {
                let option = document.createElement("option");
                option.value = subcat.id_categoria;
                option.textContent = subcat.nombre;
                subcategoriaFiltro.appendChild(option);
            });
        }
    });

    
    filtrosBuscar.addEventListener("click", function () {
        let parametros = [];

      if (buscador === "" && categoriaFiltro === "" && subcategoriaFiltro === "" && precio === "") {
            alert("Debes seleccionar al menos un filtro o escribir una búsqueda.");
            return;
        }

    if (buscador.value.trim() !== "") {
        parametros.push("buscador=" + encodeURIComponent(buscador.value.trim()));
    }

    if (categoriaFiltro.value !== "") {
        parametros.push("categoria=" + encodeURIComponent(categoriaFiltro.value));
    }

    if (subcategoriaFiltro.value !== "") {
        parametros.push("subcategoria=" + encodeURIComponent(subcategoriaFiltro.value));
    }

    if (precio.value !== "") {
        parametros.push("precio=" + encodeURIComponent(precio.value));
    }

    window.location.href = "resultados.php?" + parametros.join("&");
});

});

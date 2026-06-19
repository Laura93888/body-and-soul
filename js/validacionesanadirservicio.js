

document.addEventListener("DOMContentLoaded", function(){

  const formulario = document.querySelector(".company-service-form");
// VALIDACIÓN FORMULARIO
  if(formulario){
    formulario.addEventListener("submit", function(e){

      let errores = [];

      let horas = document.getElementById("horas").value.trim();
      let minutos = document.getElementById("minutos").value.trim();
      let precio = document.getElementById("precio").value.trim();
      let direccion = document.getElementById("direccion_lugar").value.trim();
      let imagen = document.getElementById("imagen").files[0];

      let horasNumero = parseInt(horas || 0);
      let minutosNumero = parseInt(minutos || 0);

      if(horasNumero === 0 && minutosNumero === 0){
        errores.push("Debes indicar una duración mayor que 0.");
      }

      if(minutosNumero % 5 !== 0){
        errores.push("Los minutos deben ir de 5 en 5.");
      }

      if(precio !== "" && !/^(0|[1-9]\d*)(\.\d{1,2})?$/.test(precio)){
        errores.push("El precio solo puede tener hasta 2 decimales.");
      }

      if(direccion !== "" && !/\d/.test(direccion)){
        errores.push("La dirección debe incluir calle y número.");
      }

      if(imagen){
        let tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];

        if(!tiposPermitidos.includes(imagen.type)){
          errores.push("La imagen debe ser JPG, PNG o WEBP.");
        }

        if(imagen.size > 2 * 1024 * 1024){
          errores.push("La imagen no puede superar 2MB.");
        }
      }

      if(errores.length > 0){
        e.preventDefault();
        alert(errores.join("\n"));
      }

    });
  }

  //COntador descripcion
  const textarea = document.getElementById("descripcion");
  const contador = document.getElementById("contadorDescripcion");

  if(textarea && contador){
    contador.textContent = textarea.value.length + " / 400";

    textarea.addEventListener("input", function(){
      contador.textContent = textarea.value.length + " / 400";
    });
  }

  //contador materiales
  const textareaMateriales = document.getElementById("materiales");
const contadorMateriales = document.getElementById("contadorMateriales");

if(textareaMateriales && contadorMateriales){
  contadorMateriales.textContent = textareaMateriales.value.trim().length + " / 200";

  textareaMateriales.addEventListener("input", function(){
    contadorMateriales.textContent = textareaMateriales.value.trim().length + " / 200";
  });
}

});

document.addEventListener("DOMContentLoaded", function () {
    let formulario = document.querySelector(".login-form");

    let nombre = document.getElementById("nombre");
    let apellido = document.getElementById("apellido");
    let email = document.getElementById("email");
    let contraseña = document.getElementById("contraseña");
    let confirmPassword = document.getElementById("confirm-password");
    let telefono = document.getElementById("telefono");

    formulario.addEventListener("submit", function (e) {
        let banderaerror = false;

        limpiarErrores();

        let valorNombre = nombre.value.trim();
        let valorApellido = apellido.value.trim();
        let valorEmail = email.value.trim();
        let valorContraseña = contraseña.value.trim();
        let valorConfirmPassword = confirmPassword.value.trim();
        let telefonoValor = telefono.value.trim();


        // Expresión regular email
        let regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

        // Al menos un número y un carácter especial, mínimo 6 caracteres
        let regexContraseña = /^(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>_\-\\\/\[\]+;']).{6,}$/;

        // Expresión regular teléfono: exactamente 9 dígitos
        let regexTelefono = /^\d{9}$/;

        // comprobación de valores del nombre
        if (valorNombre === "") {
            mostrarError(nombre, "El nombre no puede estar vacío");
            banderaerror = true;
        } else if (valorNombre.length > 50) {
            mostrarError(nombre, "El nombre no puede tener más de 50 caracteres");
            banderaerror = true;
        }

        // comprobación de valores del apellido
        if (valorApellido === "") {
            mostrarError(apellido, "El apellido no puede estar vacío");
            banderaerror = true;
        } else if (valorApellido.length > 30) {
            mostrarError(apellido, "El apellido no puede tener más de 30 caracteres");
            banderaerror = true;
        }

        // comprobación de valores del email
        if (valorEmail === "") {
            mostrarError(email, "El email no puede estar vacío");
            banderaerror = true;
        } else if (!regexEmail.test(valorEmail)) {
            mostrarError(email, "Introduce un email válido");
            banderaerror = true;
        }

         // comprobación de valores del teléfono
        if (telefonoValor === "") {
            mostrarError(telefono, "El teléfono no puede estar vacío");
            banderaerror = true;
        } else if (!regexTelefono.test(telefonoValor)) {
            mostrarError(telefono, "Introduce un teléfono válido de 9 dígitos");
            banderaerror = true;
        }

        // comprobación de valores de la contraseña
        if (valorContraseña === "") {
            mostrarError(contraseña, "La contraseña no puede estar vacía");
            banderaerror = true;
        } else if (valorContraseña.length < 6) {
            mostrarError(contraseña, "La contraseña debe tener al menos 6 caracteres");
            banderaerror = true;
        } else if (!regexContraseña.test(valorContraseña)) {
            mostrarError(contraseña, "Debe tener al menos un número y un carácter especial");
            banderaerror = true;
        }

        // Comprobar confirmación de contraseña
        if (valorConfirmPassword === "") {
            mostrarError(confirmPassword, "Debes repetir la contraseña");
            banderaerror = true;
        } else if (valorContraseña !== valorConfirmPassword) {
            mostrarError(confirmPassword, "Las contraseñas no coinciden");
            banderaerror = true;
        }

        //Si hay errores no envia el formulario
        if (banderaerror) {
            e.preventDefault();
        }
    });

    function mostrarError(input, mensaje) {
        let small = input.parentElement.querySelector("small");
        if (small) {
            small.textContent = mensaje;
        }
    }

    //limpio los errores siempre que le doy a submit
    function limpiarErrores() {
        let errores = document.querySelectorAll(".login-form small");
        errores.forEach(function (small) {
            small.textContent = "";
        });
    }
});

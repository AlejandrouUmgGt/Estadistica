// Validación visual sencilla del formulario, sin cálculos estadísticos.
// La validación definitiva la hace Joel en PHP; este filtro usa la misma regla.
document.addEventListener("DOMContentLoaded", function () {
    const formulario = document.getElementById("formulario_analisis");
    const campo_datos = document.getElementById("datos");
    const mensaje = document.getElementById("mensaje_validacion");
    const detalle = document.getElementById("detalle_validacion");
    formulario.addEventListener("submit", function (evento) {
        const texto = campo_datos.value.trim();
        const partes = texto.split(/[\s,;]+/);
        const invalidos = [];
        for (let i = 0; i < partes.length; i++) {
            // Igual que Joel: enteros y decimales con punto, signo opcional. Sin notación científica.
            const numero_valido = /^[+-]?(?:\d+(?:\.\d*)?|\.\d+)$/.test(partes[i]);
            if (!numero_valido || !Number.isFinite(Number(partes[i]))) {
                invalidos.push(partes[i]);
            }
        }
        if (texto === "" || invalidos.length > 0) {
            evento.preventDefault();
            mensaje.hidden = false;
            detalle.textContent = texto === "" ? "Ingresa al menos un número." : "No se pudo leer: " + invalidos.join(", ") + ". Corrige esos valores y vuelve a generar.";
        }
        else {
            // Guardar temporalmente el formulario y abrir la pantalla de resultados.
            evento.preventDefault();
            sessionStorage.setItem("analisis_titulo", document.getElementById("titulo").value);
            sessionStorage.setItem("analisis_datos", campo_datos.value);
            window.location.href = "/resultados";
            mensaje.hidden = true;
        }
    });
    formulario.addEventListener("reset", function () {
        mensaje.hidden = true;
        detalle.textContent = "";
    });
});
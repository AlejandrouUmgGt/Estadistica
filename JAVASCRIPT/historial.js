// CAMBIO: consultar la API de Diego desde el navegador, visible en F12 > Red.
document.addEventListener("DOMContentLoaded", async function () {
    const lista = document.getElementById("lista_historial");
    const mensaje = document.getElementById("mensaje_historial");

    try {
        const respuesta = await fetch("/api/diego-alejandro-vasquez-yol?operacion=listar");
        const json = await respuesta.json();

        if (!respuesta.ok || json.correcto !== true) {
            throw new Error(json.error || "No se pudo consultar el historial.");
        }

        mensaje.hidden = true;
        if (json.analisis.length === 0) {
            mensaje.hidden = false;
            mensaje.textContent = "Todavía no hay análisis guardados.";
            return;
        }

        for (const analisis of json.analisis) {
            const tarjeta = document.createElement("article");
            tarjeta.className = "registro";
            const informacion = document.createElement("div");
            const titulo = document.createElement("strong");
            titulo.textContent = analisis.titulo;
            const detalle = document.createElement("small");
            const n = analisis.N === null ? "No disponible" : analisis.N;
            const k = analisis.k === null ? "No disponible" : analisis.k;
            detalle.textContent = analisis.fecha + " · N = " + n + " · k = " + k;
            informacion.append(titulo, detalle);

            const enlace = document.createElement("a");
            enlace.className = "boton";
            enlace.href = "/resultados?id=" + encodeURIComponent(analisis.id);
            enlace.textContent = "Consultar";
            enlace.setAttribute("aria-label", "Consultar " + analisis.titulo);
            tarjeta.append(informacion, enlace);
            lista.appendChild(tarjeta);
        }
    } catch (error) {
        mensaje.hidden = false;
        mensaje.textContent = "No se pudo recuperar el historial: " + error.message;
    }
});

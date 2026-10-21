// Peticiones visibles y separadas a Joel, Obdulio y William. Diego solo guarda y consulta.
document.addEventListener("DOMContentLoaded", async function () {
    const api_joel = "/api/joel-adrian-galego-lopez";
    const api_obdulio = "/api/obdulio-augusto-fuentes-fuentes";
    const api_william = "/api/william-eliazar-mateo-devian";
    const api_diego = "/api/diego-alejandro-vasquez-yol";
    let resultados_actuales = null;
    const mensaje = document.getElementById("mensaje_estado");
    const contenido = document.getElementById("contenido_resultado");
    const boton_guardar = document.getElementById("guardar_analisis");
    const parametros = new URLSearchParams(window.location.search);
    let titulo = "";
    let datos = "";

    function mostrar_error(texto) {
        mensaje.hidden = false;
        mensaje.textContent = texto;
        contenido.hidden = true;
    }

    function numero(valor) {
        if (valor === null || valor === undefined) {
            return "No definido";
        }
        return Number(Number(valor).toFixed(4)).toString();
    }

    function tarjeta(nombre, valor, formula) {
        const elemento = document.createElement("div");
        elemento.className = "tarjeta";
        const etiqueta = document.createElement("small");
        etiqueta.textContent = nombre;
        const cantidad = document.createElement("strong");
        cantidad.textContent = numero(valor);
        elemento.append(etiqueta, cantidad);
        if (formula) {
            const detalle = document.createElement("div");
            detalle.className = "formula";
            // CAMBIO: MathJax reconoce el LaTeX entre \\( y \\).
            detalle.textContent = formula;
            elemento.appendChild(detalle);
        }
        return elemento;
    }

    function normalizar_historico(original) {
        // Adaptar solo la presentación de JSON anteriores, sin recalcular.
        if (Array.isArray(original.tabla)) {
            return original;
        }
        if (!Array.isArray(original.tabla_frecuencias)) {
            throw new Error("El análisis guardado no contiene frecuencias.");
        }
        const tabla = original.tabla_frecuencias;
        let n = 0;
        const limites = [];
        for (const clase of tabla) {
            n += Number(clase.fi);
            limites.push(Number(clase.li));
        }
        limites.push(Number(tabla[tabla.length - 1].ls));
        return {
            N: n,
            rango: original.rango,
            k: original.k_usado,
            k_teorico: original.k_teorico,
            amplitud: original.amplitud,
            tabla: tabla,
            limites: limites,
            media: original.medidas?.media,
            varianza: original.medidas?.varianza,
            desviacion: original.medidas?.desviacion_estandar,
            asimetria: original.forma?.asimetria,
            curtosis: original.forma?.curtosis,
            exceso: original.forma?.exceso_curtosis
        };
    }

    function presentar(titulo_recibido, texto_datos, resultados) {
        if (!Array.isArray(resultados.tabla) || resultados.tabla.length === 0) {
            throw new Error("La API no devolvió una tabla válida.");
        }
        document.getElementById("titulo_resultado").textContent = titulo_recibido;
        document.getElementById("datos_originales").textContent = texto_datos;
        const resumen = document.getElementById("resumen_resultado");
        resumen.replaceChildren(
            tarjeta("N (datos)", resultados.N),
            tarjeta("Rango R", resultados.rango),
            tarjeta("Clases k", resultados.k, "teórico " + numero(resultados.k_teorico) + " → usado " + resultados.k),
            tarjeta("Amplitud A", resultados.amplitud)
        );

        const cuerpo = document.getElementById("cuerpo_frecuencias");
        cuerpo.replaceChildren();
        let suma_hi = 0;
        for (let i = 0; i < resultados.tabla.length; i++) {
            const clase = resultados.tabla[i];
            suma_hi += Number(clase.hi);
            const fila = document.createElement("tr");
            const intervalo = "[" + numero(clase.li) + ", " + numero(clase.ls) + (i === resultados.tabla.length - 1 ? "]" : ")");
            for (const valor of [intervalo, numero(clase.xi), clase.fi, clase.Fi, numero(clase.hi), numero(clase.Hi)]) {
                const celda = document.createElement("td");
                celda.textContent = valor;
                fila.appendChild(celda);
            }
            cuerpo.appendChild(fila);
        }

        // Fila de totales: N viene de la API; Σhi solo se suma para mostrarlo.
        const pie = document.getElementById("pie_frecuencias");
        if (pie) {
            const fila_total = document.createElement("tr");
            for (const valor of ["Total (N)", "", resultados.N, "", numero(suma_hi), ""]) {
                const celda = document.createElement("td");
                celda.textContent = valor;
                fila_total.appendChild(celda);
            }
            pie.replaceChildren(fila_total);
        }

        const medidas = document.getElementById("medidas_resultado");
        // CAMBIO: fórmulas en LaTeX, pero los valores siguen llegando de las APIs.
        medidas.replaceChildren(
            tarjeta("Rango R", resultados.rango, "\\(R = x_{\\max} - x_{\\min}\\)"),
            tarjeta("Clases teóricas", resultados.k_teorico, "\\(k_t = 1 + 3.322 \\cdot \\log_{10}(N)\\)"),
            tarjeta("Amplitud A", resultados.amplitud, "\\(A = \\frac{R}{k}\\)"),
            tarjeta("Media (μ)", resultados.media, "\\(\\mu = \\frac{\\sum f_i x_i}{N}\\)"),
            tarjeta("Varianza (σ²)", resultados.varianza, "\\(\\sigma^2 = \\frac{\\sum f_i(x_i-\\mu)^2}{N}\\)"),
            tarjeta("Desviación estándar (σ)", resultados.desviacion, "\\(\\sigma = \\sqrt{\\sigma^2}\\)"),
            tarjeta("Asimetría (g1)", resultados.asimetria, "\\(g_1 = \\frac{\\sum f_i(x_i-\\mu)^3}{N \\sigma^3}\\)"),
            tarjeta("Curtosis (K)", resultados.curtosis, "\\(K = \\frac{\\sum f_i(x_i-\\mu)^4}{N \\sigma^4}\\)"),
            tarjeta("Exceso de curtosis (Ke)", resultados.exceso, "\\(K_e = K - 3\\)")
        );

        contenido.hidden = false;
        mensaje.hidden = true;
        // CAMBIO: las tarjetas se agregan dinámicamente; MathJax debe procesarlas después.
        if (window.MathJax && typeof window.MathJax.typesetPromise === "function") {
            window.MathJax.typesetPromise([medidas, document.querySelector("section.panel:nth-of-type(3)")]).catch(function (error) {
                console.error("No se pudieron dibujar las fórmulas:", error);
            });
        }
        if (typeof dibujar_histograma_kevin === "function" && typeof Chart !== "undefined") {
            dibujar_histograma_kevin(resultados.tabla);
        } else {
            mensaje.hidden = false;
            mensaje.textContent = "No se pudo cargar el módulo del histograma.";
        }
    }

    async function solicitar(url, opciones) {
        const respuesta = await fetch(url, opciones);
        const json = await respuesta.json();
        if (!respuesta.ok || json.correcto === false) {
            const errores = json.errores || [];
            throw new Error(json.error || errores.join(", ") || "La API no pudo completar la solicitud.");
        }
        return json;
    }

    try {
        if (parametros.has("id")) {
            const id = parametros.get("id");
            const json = await solicitar(api_diego + "?operacion=consultar&id=" + encodeURIComponent(id));
            titulo = json.analisis.titulo;
            datos = json.analisis.datos_originales;
            presentar(titulo, datos, normalizar_historico(json.analisis.resultados));
            boton_guardar.hidden = true;
        } else {
            titulo = parametros.get("titulo") || sessionStorage.getItem("analisis_titulo") || "";
            datos = parametros.get("datos") || sessionStorage.getItem("analisis_datos") || "";
            if (!titulo.trim() || !datos.trim()) {
                throw new Error("Ingresa un título y números desde Nuevo análisis.");
            }
            const cuerpo = new URLSearchParams();
            cuerpo.set("titulo", titulo);
            cuerpo.set("datos", datos);
            // Petición HTTP 1: Joel valida la entrada.
            const validacion = await solicitar(api_joel, {
                method: "POST",
                body: cuerpo
            });

            // Petición HTTP 2: Obdulio recibe los números validados.
            const cuerpo_obdulio = new URLSearchParams();
            cuerpo_obdulio.set("numeros", JSON.stringify(validacion.numeros));
            const intervalos = await solicitar(api_obdulio, {
                method: "POST",
                body: cuerpo_obdulio
            });

            // Petición HTTP 3: William recibe los intervalos ya calculados.
            const cuerpo_william = new URLSearchParams();
            cuerpo_william.set("intervalos", JSON.stringify(intervalos));
            const medidas = await solicitar(api_william, {
                method: "POST",
                body: cuerpo_william
            });

            // Combinar respuestas para dibujar, sin calcular estadísticas en JS.
            resultados_actuales = Object.assign({}, intervalos, medidas);
            presentar(validacion.titulo, datos, resultados_actuales);
        }
    } catch (error) {
        mostrar_error(error.message);
        return;
    }

    boton_guardar.addEventListener("click", async function () {
        boton_guardar.disabled = true;
        try {
            const cuerpo = new URLSearchParams();
            cuerpo.set("titulo", titulo);
            cuerpo.set("datos", datos);
            // Diego recibe resultados ya calculados, no los recalcula.
            cuerpo.set("resultados", JSON.stringify(resultados_actuales));
            const json = await solicitar(api_diego + "?operacion=guardar", {
                method: "POST",
                body: cuerpo
            });
            mensaje.hidden = false;
            mensaje.textContent = "Análisis guardado correctamente. ID: " + json.id;
            boton_guardar.hidden = true;
            sessionStorage.removeItem("analisis_titulo");
            sessionStorage.removeItem("analisis_datos");
        } catch (error) {
            mensaje.hidden = false;
            mensaje.textContent = "No se pudo guardar: " + error.message;
            boton_guardar.disabled = false;
        }
    });
});
<?php
// CAMBIO: página de entrada PHP para la pantalla Nuevo análisis.
// Esta pantalla no consulta la base de datos, por eso no necesita conexion.php.
// Los cálculos y la validación definitiva se realizarán en resultados.php.
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            Nuevo análisis | Estadística I
        </title>
        <link rel="stylesheet" href="../CSS/index.css">
        <script src="../JAVASCRIPT/index.js" defer>
        </script>
    </head>
    <body>
        <header class="barra">
            <div class="marca">
                <span class="isotipo">
                    P
                </span>
                <strong>
                    Proyecto
                </strong>
                <span>
                    Estadística I
                </span>
            </div>
            <nav aria-label="Navegación principal">
                <a class="activo" href="/index.html">
                    Nuevo análisis
                </a>
                <a href="/historial">
                    Historial
                </a>
            </nav>
        </header>
        <main class="contenedor">
            <h1>
                Nuevo análisis
            </h1>
            <p class="descripcion">
                Ingresa una serie de números para agruparlos en intervalos y calcular sus medidas.
            </p>
            <form id="formulario_analisis" action="/resultados" method="get">
                <label for="titulo">
                    Título del análisis
                </label>
                <input id="titulo" name="titulo" type="text" placeholder="Notas parcial 1 — Sección A" required>
                <label for="datos">
                    Datos
                </label>
                <textarea id="datos" name="datos" placeholder="0 1 2 3 4 5 6 7 8 10" required>
                </textarea>
                <p class="ayuda">
                    Separadores válidos: espacios, saltos de línea, coma o punto y coma. La coma NO es separador decimal: usa punto (3.5).
                </p>
                <div id="mensaje_validacion" class="mensaje" role="alert" hidden>
                    <strong>
                        Hay valores no válidos
                    </strong>
                    <p id="detalle_validacion">
                    </p>
                </div>
                <div class="acciones">
                    <button type="submit" class="generar">
                        Generar análisis
                    </button>
                    <button type="reset" class="limpiar">
                        Limpiar
                    </button>
                </div>
            </form>
        </main>
    </body>
</html>

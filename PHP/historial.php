<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            Historial | Estadística I
        </title>
        <link rel="stylesheet" href="/CSS/historial.css?v=presentacion-20261020">
        <script src="/JAVASCRIPT/historial.js" defer>
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
                <small>
                    Estadística I
                </small>
            </div>
            <nav>
                <a href="/index.html">
                    Nuevo análisis
                </a>
                <a class="activo" href="/historial">
                    Historial
                </a>
            </nav>
        </header>
        <main>
            <h1>
                Historial
            </h1>
            <p class="subtitulo">
                Análisis guardados, del más reciente al más antiguo.
            </p>
            <!-- CAMBIO: el historial se llena exclusivamente con la API de Diego. -->
            <div class="aviso" id="mensaje_historial">Consultando el historial...</div>
            <section id="lista_historial"></section>
        </main>
    </body>
</html>

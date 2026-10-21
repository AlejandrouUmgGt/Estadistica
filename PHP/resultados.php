<?php
// Esta página solo presenta resultados. Los cálculos están en las APIs.
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultados | Estadística I</title>
    <link rel="stylesheet" href="/CSS/resultados.css?v=presentacion-20261020">
    <!-- Chart.js 4.4.7 local: el histograma funciona sin internet. -->
    <script src="/JAVASCRIPT/chart.umd.js" defer></script>
    <!-- CAMBIO: MathJax muestra fracciones, raíces y sumatorias con formato matemático. -->
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js" defer></script>
    <script src="/JAVASCRIPT/KevinDaniloMoralesMejia.js" defer></script>
    <script src="/JAVASCRIPT/resultados.js" defer></script>
</head>

<body>
    <header class="barra">
        <div class="marca">
            <span class="isotipo">P</span>
            <strong>Proyecto</strong>
            <small>Estadística I</small>
        </div>
        <nav>
            <a href="/index.html">Nuevo análisis</a>
            <a href="/historial">Historial</a>
        </nav>
    </header>
    <main>
        <h1 id="titulo_resultado">Resultados del análisis</h1>
        <div class="aviso" id="mensaje_estado">Consultando las APIs...</div>
        <div id="contenido_resultado" hidden>
            <div class="resumen" id="resumen_resultado"></div>
            <section class="panel">
                <h2>Datos originales</h2>
                <p class="datos" id="datos_originales"></p>
            </section>
            <section class="panel">
                <h2>Distribución de frecuencias (datos agrupados)</h2>
                <div class="tabla-contenedor">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Intervalo</th>
                                <th>xi</th>
                                <th>fi</th>
                                <th>Fi</th>
                                <th>hi</th>
                                <th>Hi</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpo_frecuencias"></tbody>
                        <tfoot id="pie_frecuencias"></tfoot>
                    </table>
                </div>
                <p class="formula">Intervalos [LI, LS): incluyen el límite inferior y excluyen el superior; el último intervalo es [LI, LS] e incluye el máximo.</p>
            </section>
            <section class="panel">
                <h2>Medidas estadísticas poblacionales (datos agrupados)</h2>
                <div class="medidas" id="medidas_resultado"></div>
                <!-- CAMBIO: fórmulas estáticas escritas en LaTeX para MathJax. -->
                <p class="formula">\(x_i = \frac{LI + LS}{2}\) · \(h_i = \frac{f_i}{N}\) · \(F_i = \sum_{j=1}^{i} f_j\) · \(H_i = \frac{F_i}{N}\).</p>
                <p class="formula"> <strong>Criterio de redondeo:</strong>  \(k_t = 1 + 3.322 \cdot \log_{10}(N)\)(redondeo hacia arriba); \(A = \frac{R}{k}\) sin redondear. Muestro hasta 4 decimales, pero calculo con todos los decimales. Si \(\sigma = 0\), asimetría y curtosis quedan sin definir.</p>
            </section>
            <section class="panel">
                <h2>Histograma</h2>
                <div class="grafica">
                    <canvas id="histograma"></canvas>
                </div>
            </section>
            <div class="acciones">
                <button type="button" class="boton primario" id="guardar_analisis">Guardar análisis</button>
                <a class="boton" href="/index.html">Nuevo análisis</a>
                <a class="boton" href="/historial">Historial</a>
            </div>
        </div>
    </main>
</body>

</html>
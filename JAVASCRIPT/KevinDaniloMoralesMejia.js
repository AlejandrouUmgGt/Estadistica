// CAMBIO: Kevin dibuja el histograma con Chart.js. No calcula frecuencias.
function dibujar_histograma_kevin(tabla) {
    const lienzo = document.getElementById("histograma");
    if (!lienzo || typeof Chart === "undefined") {
        throw new Error("Chart.js no está disponible.");
    }

    const puntos = [];
    for (const clase of tabla) {
        puntos.push({
            x: (Number(clase.li) + Number(clase.ls)) / 2,
            y: Number(clase.fi)
        });
    }

    const minimo = Number(tabla[0].li);
    const maximo = Number(tabla[tabla.length - 1].ls);
    const unico = minimo === maximo;
    const grafica = new Chart(lienzo, {
        type: "bar",
        data: {
            datasets: [{
                data: puntos,
                backgroundColor: "#299d95",
                borderColor: "#1b3c61",
                borderWidth: 1,
                barPercentage: 1,
                categoryPercentage: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            parsing: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: function (elementos) {
                            const indice = elementos[0].dataIndex;
                            const clase = tabla[indice];
                            return "[" + clase.li + ", " + clase.ls + (indice === tabla.length - 1 ? "]" : ")");
                        }
                    }
                }
            },
            scales: {
                x: {
                    type: "linear",
                    min: unico ? minimo - 0.5 : minimo,
                    max: unico ? maximo + 0.5 : maximo,
                    title: {
                        display: true,
                        text: "Intervalos"
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Frecuencia"
                    },
                    ticks: {
                        precision: 0
                    }
                }
            }
        },
        plugins: [{
            id: "anchos_kevin",
            afterDatasetsUpdate: function (grafico) {
                const barras = grafico.getDatasetMeta(0).data;
                const eje = grafico.scales.x;
                for (let i = 0; i < barras.length; i++) {
                    const clase = tabla[i];
                    const izquierda = eje.getPixelForValue(unico ? minimo - 0.5 : Number(clase.li));
                    const derecha = eje.getPixelForValue(unico ? maximo + 0.5 : Number(clase.ls));
                    barras[i].x = (izquierda + derecha) / 2;
                    barras[i].width = Math.max(1, derecha - izquierda);
                }
            }
        }]
    });
    return grafica;
}

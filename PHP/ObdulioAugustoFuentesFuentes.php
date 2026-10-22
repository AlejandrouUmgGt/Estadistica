<?php
// API de Obdulio: agrupa los números en intervalos con la regla de Sturges.
header("Content-Type: application/json; charset=utf-8");

// Recibe los números que ya validó Joel, como arreglo JSON.
$numeros = json_decode($_POST["numeros"] ?? "", true);

$valido = is_array($numeros) && count($numeros) >= 1 && count($numeros) <= 10000;

if ($valido) {
    foreach ($numeros as $numero) {
        if (!is_int($numero) && !is_float($numero)) {
            $valido = false;
            break;
        }
    }
}

if (!$valido) {
    http_response_code(422);
    echo json_encode(["correcto" => false, "error" => "Envía numeros como arreglo JSON válido, previamente validado por Joel."], JSON_UNESCAPED_UNICODE);
    exit;
}

// Datos básicos
$n = count($numeros);
$minimo = min($numeros);
$maximo = max($numeros);
$rango = $maximo - $minimo;

// Sturges: k = 1 + 3.322 * log10(N). Criterio de redondeo: k = techo(k teórico).
// La amplitud A = R / k NO se redondea. Si todos son iguales, una sola clase.
$k_teorico = 1 + 3.322 * log10($n);
if ($rango == 0) {
    $k = 1;
    $amplitud = 0;
} else {
    $k = (int)ceil($k_teorico);
    $amplitud = $rango / $k;
}

if (!is_finite($rango) || !is_finite($amplitud) || ($rango > 0 && $amplitud <= 0)) {
    http_response_code(422);
    echo json_encode(["correcto" => false, "error" => "No se pueden formar intervalos con estos valores."], JSON_UNESCAPED_UNICODE);
    exit;
}

// Límites de cada clase, calculados una sola vez.
// Se usan tanto para contar como para mostrar la tabla, así ambos coinciden siempre.
$limites = [];
for ($i = 0; $i <= $k; $i++) {
    $limites[$i] = $minimo + $i * $amplitud;
}
$limites[$k] = $maximo;

// Contar cuántos datos caen en cada clase: [li, ls) y la última [li, ls].
$frecuencias = array_fill(0, $k, 0);
foreach ($numeros as $numero) {
    if ($rango == 0) {
        $posicion = 0;
    } else {
        $posicion = (int)floor(($numero - $minimo) / $amplitud);
        if ($posicion > $k - 1) {
            $posicion = $k - 1;
        }
        if ($posicion < 0) {
            $posicion = 0;
        }
        // Corrección por coma flotante: el dato debe respetar los límites mostrados.
        while ($posicion < $k - 1 && $numero >= $limites[$posicion + 1]) {
            $posicion++;
        }
        while ($posicion > 0 && $numero < $limites[$posicion]) {
            $posicion--;
        }
    }

    $frecuencias[$posicion]++;
}

// Construir la tabla de frecuencias.
$tabla = [];
$acumulada = 0;
for ($i = 0; $i < $k; $i++) {
    $li = $limites[$i];
    $ls = $limites[$i + 1];

    $fi = $frecuencias[$i];
    $acumulada = $acumulada + $fi;

    $tabla[] = [
        "li" => $li,
        "ls" => $ls,
        "xi" => ($li + $ls) / 2,
        "fi" => $fi,
        "Fi" => $acumulada,
        "hi" => $fi / $n,
        "Hi" => $acumulada / $n
    ];
}

echo json_encode([
    "correcto" => true,
    "N" => $n,
    "rango" => $rango,
    "k_teorico" => $k_teorico,
    "k" => $k,
    "amplitud" => $amplitud,
    "tabla" => $tabla
], JSON_UNESCAPED_UNICODE);

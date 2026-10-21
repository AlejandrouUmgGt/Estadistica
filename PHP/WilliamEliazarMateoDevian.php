<?php
// API de William: calcula las medidas poblacionales de datos agrupados.
header("Content-Type: application/json; charset=utf-8");

// Recibe el JSON completo que devolvió Obdulio.
$intervalos = json_decode($_POST["intervalos"] ?? "", true);

$valido = is_array($intervalos)
    && isset($intervalos["N"], $intervalos["tabla"])
    && is_int($intervalos["N"])
    && $intervalos["N"] >= 1
    && $intervalos["N"] <= 10000
    && is_array($intervalos["tabla"])
    && count($intervalos["tabla"]) >= 1;

// Revisar cada clase y sumar las frecuencias.
$suma_frecuencias = 0;
if ($valido) {
    foreach ($intervalos["tabla"] as $clase) {
        if (
            !is_array($clase)
            || !isset($clase["fi"], $clase["xi"])
            || !is_int($clase["fi"])
            || $clase["fi"] < 0
            || !is_numeric($clase["xi"])
        ) {
            $valido = false;
            break;
        }
        $suma_frecuencias = $suma_frecuencias + $clase["fi"];
    }
}

// Las frecuencias deben sumar N.
if (!$valido || $suma_frecuencias !== $intervalos["N"]) {
    http_response_code(422);
    echo json_encode(["correcto" => false, "error" => "La tabla de Obdulio es inválida o no suma N."], JSON_UNESCAPED_UNICODE);
    exit;
}

$n = $intervalos["N"];

// Media = suma(fi * xi) / N
$suma = 0;
foreach ($intervalos["tabla"] as $clase) {
    $suma = $suma + $clase["fi"] * $clase["xi"];
}
$media = $suma / $n;

// Sumas de las potencias de (xi - media)
$suma2 = 0;
$suma3 = 0;
$suma4 = 0;
foreach ($intervalos["tabla"] as $clase) {
    $diferencia = $clase["xi"] - $media;
    $suma2 = $suma2 + $clase["fi"] * pow($diferencia, 2);
    $suma3 = $suma3 + $clase["fi"] * pow($diferencia, 3);
    $suma4 = $suma4 + $clase["fi"] * pow($diferencia, 4);
}

// Varianza poblacional: se divide entre N, no entre N - 1.
$varianza = $suma2 / $n;
$desviacion = sqrt($varianza);

// Si la desviación es 0 no se puede dividir: asimetría y curtosis quedan sin definir.
$asimetria = null;
$curtosis = null;
$exceso = null;
if ($desviacion > 0) {
    $asimetria = $suma3 / ($n * pow($desviacion, 3));
    $curtosis = $suma4 / ($n * pow($desviacion, 4));
    $exceso = $curtosis - 3;
}

echo json_encode([
    "correcto" => true,
    "media" => $media,
    "varianza" => $varianza,
    "desviacion" => $desviacion,
    "asimetria" => $asimetria,
    "curtosis" => $curtosis,
    "exceso" => $exceso
], JSON_UNESCAPED_UNICODE);

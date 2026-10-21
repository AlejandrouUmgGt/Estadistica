<?php
// API de Joel: valida el título y la serie de números.
header("Content-Type: application/json; charset=utf-8");

$titulo = trim($_POST["titulo"] ?? $_GET["titulo"] ?? "");
$datos = trim($_POST["datos"] ?? $_GET["datos"] ?? "");
$errores = [];
$numeros = [];

// El título es obligatorio y máximo 200 caracteres (mb_strlen cuenta caracteres, no bytes).
if ($titulo === "" || mb_strlen($titulo, "UTF-8") > 200) {
    $errores[] = "El título es obligatorio y admite hasta 200 caracteres.";
}

if ($datos === "") {
    $errores[] = "Ingresa al menos un número.";
} else {
    // Separadores válidos: espacios, saltos de línea, coma o punto y coma.
    $partes = preg_split('/[\s,;]+/', $datos, -1, PREG_SPLIT_NO_EMPTY);

    if (count($partes) > 10000) {
        $errores[] = "Solo se permiten hasta 10000 datos.";
    } else {
        foreach ($partes as $parte) {
            // Acepta enteros y decimales con punto, con signo opcional. No acepta notación científica.
            $es_numero = preg_match('/^[+-]?([0-9]+(\.[0-9]*)?|\.[0-9]+)$/', $parte);

            if ($es_numero && is_finite((float)$parte)) {
                $numeros[] = (float)$parte;
            } else {
                $errores[] = "Valor no válido: " . $parte;
            }
        }
    }
}

$correcto = count($errores) === 0;

// Si hay errores, no se devuelven números.
if (!$correcto) {
    $numeros = [];
    http_response_code(422);
}

echo json_encode([
    "correcto" => $correcto,
    "titulo" => $titulo,
    "cantidad" => count($numeros),
    "numeros" => $numeros,
    "errores" => $errores
], JSON_UNESCAPED_UNICODE);

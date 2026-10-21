<?php
// API de Diego: guarda, lista y consulta análisis en MySQL. No calcula estadísticas.
header("Content-Type: application/json; charset=utf-8");

// Envía una respuesta JSON y termina el script.
function responder($datos, $codigo = 200)
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

$operacion = $_GET["operacion"] ?? "";
$metodo = $_SERVER["REQUEST_METHOD"];

// ---------- GUARDAR ----------
if ($operacion === "guardar" && $metodo === "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $datos = trim($_POST["datos"] ?? "");
    $resultados = json_decode($_POST["resultados"] ?? "", true);
    $mensaje_error = "El análisis recibido está incompleto o no es válido.";

    // Revisar título y datos.
    if ($titulo === "" || strlen($titulo) > 200 || $datos === "" || strlen($datos) > 650000) {
        responder(["correcto" => false, "error" => $mensaje_error], 422);
    }

    // Revisar la estructura de los resultados.
    if (
        !is_array($resultados)
        || !isset($resultados["N"], $resultados["k"], $resultados["tabla"])
        || !is_int($resultados["N"])
        || $resultados["N"] < 1
        || $resultados["N"] > 10000
        || !is_int($resultados["k"])
        || $resultados["k"] < 1
        || !is_array($resultados["tabla"])
        || count($resultados["tabla"]) !== $resultados["k"]
    ) {
        responder(["correcto" => false, "error" => $mensaje_error], 422);
    }

    // Las frecuencias deben ser enteros y sumar N.
    $suma_fi = 0;
    foreach ($resultados["tabla"] as $clase) {
        if (!is_array($clase) || !isset($clase["fi"]) || !is_int($clase["fi"]) || $clase["fi"] < 0) {
            responder(["correcto" => false, "error" => $mensaje_error], 422);
        }
        $suma_fi = $suma_fi + $clase["fi"];
    }
    if ($suma_fi !== $resultados["N"]) {
        responder(["correcto" => false, "error" => $mensaje_error], 422);
    }

    $json = json_encode($resultados, JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        responder(["correcto" => false, "error" => "No se pudo convertir el resultado a JSON."], 422);
    }

    // Insertar en MySQL con consulta preparada.
    $criterio = "sturges";
    require_once __DIR__ . "/conexion.php";

    $consulta = mysqli_prepare($cn, "INSERT INTO analisis (titulo, fecha, datos_originales, criterio_clases, resultados) VALUES (?, NOW(), ?, ?, ?)");
    if (!$consulta) {
        responder(["correcto" => false, "error" => "No se pudo preparar el guardado."], 500);
    }

    mysqli_stmt_bind_param($consulta, "ssss", $titulo, $datos, $criterio, $json);
    $guardado = mysqli_stmt_execute($consulta);
    $id = mysqli_insert_id($cn);
    mysqli_stmt_close($consulta);

    if (!$guardado) {
        responder(["correcto" => false, "error" => "MySQL rechazó el guardado. Revisa los registros del servidor."], 500);
    }

    responder(["correcto" => true, "id" => $id, "mensaje" => "Análisis guardado."]);
}

// ---------- LISTAR ----------
if ($operacion === "listar" && $metodo === "GET") {
    require_once __DIR__ . "/conexion.php";

    $consulta = mysqli_query($cn, "SELECT id, titulo, fecha, resultados FROM analisis ORDER BY fecha DESC, id DESC LIMIT 100");
    if (!$consulta) {
        responder(["correcto" => false, "error" => "No se pudo consultar el historial."], 500);
    }

    $analisis = [];
    while ($fila = mysqli_fetch_array($consulta, MYSQLI_ASSOC)) {
        $guardado = json_decode($fila["resultados"], true);

        // Registros nuevos guardan N y k; los antiguos guardan k_usado y tabla_frecuencias.
        $fila["N"] = $guardado["N"] ?? null;
        $fila["k"] = $guardado["k"] ?? $guardado["k_usado"] ?? null;

        if ($fila["N"] === null && isset($guardado["tabla_frecuencias"])) {
            $fila["N"] = array_sum(array_column($guardado["tabla_frecuencias"], "fi"));
        }

        // El JSON completo no se envía en la lista.
        unset($fila["resultados"]);
        $analisis[] = $fila;
    }

    responder(["correcto" => true, "analisis" => $analisis]);
}

// ---------- CONSULTAR ----------
if ($operacion === "consultar" && $metodo === "GET") {
    $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        responder(["correcto" => false, "error" => "ID no válido."], 422);
    }

    require_once __DIR__ . "/conexion.php";

    $consulta = mysqli_prepare($cn, "SELECT id, titulo, fecha, datos_originales, criterio_clases, resultados FROM analisis WHERE id = ? LIMIT 1");
    if (!$consulta) {
        responder(["correcto" => false, "error" => "No se pudo preparar la consulta."], 500);
    }

    mysqli_stmt_bind_param($consulta, "i", $id);
    mysqli_stmt_execute($consulta);
    $respuesta = mysqli_stmt_get_result($consulta);
    $fila = mysqli_fetch_array($respuesta, MYSQLI_ASSOC);
    mysqli_stmt_close($consulta);

    if (!$fila) {
        responder(["correcto" => false, "error" => "Análisis no encontrado."], 404);
    }

    // El campo resultados se guardó como texto JSON; se convierte de nuevo a arreglo.
    $fila["resultados"] = json_decode($fila["resultados"], true);
    responder(["correcto" => true, "analisis" => $fila]);
}

// ---------- Cualquier otra cosa ----------
header("Allow: GET, POST");
responder([
    "correcto" => false,
    "error" => "Operación o método no permitido.",
    "operaciones" => ["guardar (POST)", "listar (GET)", "consultar (GET con id)"]
], 405);

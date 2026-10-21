# Proyecto Estadística I

Sistema web de análisis de datos agrupados. Universidad Mariano Gálvez de Guatemala.

## Etapa actual: pantalla Nuevo análisis

Se creó únicamente la primera pantalla del diseño de Figma. La pantalla Resultados, el historial y el histograma con Chart.js están pendientes. **No se han implementado todavía los cálculos estadísticos.**

### HTML/index.html
**Qué hace:** muestra una maqueta estática de Nuevo análisis, con título, datos, navegación y botones.
**Cómo funciona:** las etiquetas `form`, `input` y `textarea` permiten escribir información. `action="../PHP/resultados.php"` indica la futura página receptora.
**Cómo probarlo:** abrir HTML/index.html en un navegador y comprobar que se ve el formulario y que Limpiar borra los campos. Generar todavía no puede mostrar resultados.

### CSS/index.css
**Qué hace:** define colores, tamaños, espacios y distribución.
**Cómo funciona:** las clases `.barra`, `.contenedor`, `.acciones` y otras aplican el diseño de Figma. `@media` adapta el contenido a pantallas pequeñas.
**Cómo probarlo:** abrir la pantalla en PC y reducir el ancho de la ventana; el formulario debe seguir siendo legible y los botones deben acomodarse.

### JAVASCRIPT/index.js
**Qué hace:** comprueba visualmente que los datos tengan formato numérico y limpia los mensajes al pulsar Limpiar.
**Cómo funciona:** espera a que cargue el HTML; escucha los eventos `submit` y `reset`. Usa una expresión regular para revisar cada valor y `Number.isFinite` para rechazar infinitos.
**Cómo probarlo:** escribir `0 1 2 3 4 5 6 7 8 10` y pulsar Generar: no debe aparecer error de formato. Escribir `2 abc 5`: debe aparecer un mensaje que señale `abc`. Pulsar Limpiar: desaparecen los datos y el mensaje.
**Importante:** la validación final deberá hacerse también en PHP; JavaScript puede desactivarse en el navegador. La coma separa números: `3,5` se interpreta como dos valores, no como decimal.

### PHP/index.php
**Qué hace:** ofrece la misma pantalla como entrada PHP real.
**Cómo funciona:** envía `titulo` y `datos` por GET a `PHP/resultados.php` cuando esa página exista. No necesita conectarse a MySQL.
**Cómo probarlo:** visitar `https://viuai.qd.je/` cuando Nginx esté configurado para abrir `PHP/index.php`. Escribir datos y probar Limpiar y los mensajes. Generar no completará el análisis hasta crear `PHP/resultados.php`.

### PHP/conexion.php (existente, sin modificar)
**Qué hace:** establece la conexión con la base de datos.
**Cómo se utilizará:** los PHP que consulten o guarden análisis lo incluirán mediante `require_once 'conexion.php';`. La pantalla de entrada no necesita conexión.
**Cómo probarlo:** se verificará en la etapa de base de datos sin mostrar credenciales.

### Prueba de datos prevista para Resultados
Entrada: `0 1 2 3 4 5 6 7 8 10`.
Resultado esperado una vez implementados los cálculos: N=10, R=10, k usado=5, amplitud=2 y cinco clases con frecuencia 2.

### Pendientes
- Crear `PHP/resultados.php` con validación del servidor y las 12 fórmulas.
- Crear la pantalla de resultados y su histograma con Chart.js.
- Crear la pantalla de historial y el guardado en MySQL.

No se ha probado el proyecto en el VPS desde esta sesión.


## Etapa Resultados e Historial (octubre 2026)

### HTML/resultados.html (ELIMINADO: maqueta antigua)
Archivo eliminado porque la pantalla real está en `PHP/resultados.php`, accesible por `/resultados`.

### CSS/resultados.css
Aplica los colores azul #1b3c61 y verde #299d95 de Figma, tarjetas, tabla desplazable y adaptación móvil. Prueba: abre Resultados y reduce el ancho de la ventana.

### JAVASCRIPT/resultados.js
`JAVASCRIPT/resultados.js` recibe la tabla de Obdulio y llama a `dibujar_histograma_kevin(tabla)`; el archivo de Kevin utiliza Chart.js (CDN) y un eje horizontal lineal. Prueba: abre `/resultados` desde Nuevo análisis y comprueba cinco barras de altura 2 con los datos `0 1 2 3 4 5 6 7 8 10`. Necesita internet para el CDN.

### PHP/resultados.php
Valida título y números del formulario; divide la serie en clases de Sturges, calcula R, k, A, xi, fi, Fi, hi, Hi, media, varianza poblacional, desviación, asimetría, curtosis y exceso. También guarda al pulsar Guardar, usando conexion.php, y consulta un registro con ?id=... sin recalcularlo. Prueba: en /index.html introduce título y 0 1 2 3 4 5 6 7 8 10, pulsa Generar y verifica N=10, R=10, k=5, A=2, media=5, varianza=8, desviación=2.8284, curtosis=1.7 y exceso=-1.3. Prueba con todos iguales: una clase, varianza cero y curtosis no definida. Prueba abc: debe rechazarse. Las consultas GET largas pueden superar el límite de URL: en ese caso hay que cambiar a POST previo aviso.

### HTML/historial.html (ELIMINADO: maqueta antigua)
Maqueta estática con ejemplos ilustrativos. Para registros reales se utiliza /historial.

### CSS/historial.css
Presenta filas blancas con título, fecha y botón Consultar, además de estado vacío y error. Prueba en móvil y PC.

### JAVASCRIPT/historial.js
Agrega nombres accesibles a los enlaces Consultar. No accede a MySQL. Prueba: inspecciona el atributo aria-label del botón.

### PHP/historial.php
Incluye conexion.php y consulta SELECT de la tabla analisis, del más reciente al más antiguo. Muestra lista o estado vacío/error y abre /resultados?id=N. Prueba: abre /historial antes y después de guardar un análisis. Necesita la tabla analisis en MySQL.

### Tabla requerida en MySQL (en el servidor, desde tu cliente SQL)
```sql
CREATE TABLE IF NOT EXISTS analisis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    fecha DATETIME NOT NULL,
    datos_originales TEXT NOT NULL,
    criterio_clases TEXT NOT NULL,
    resultados TEXT NOT NULL
);
```

### Rutas públicas
`/` y `/index.html` muestran el formulario HTML; `/resultados` ejecuta `PHP/resultados.php` y `/historial` ejecuta `PHP/historial.php`. Las rutas antiguas `/resultados.html` y `/historial.html` ya no están disponibles. `/PHP/*.php` se bloquea desde el navegador.

### Activar y comprobar Nginx
En el servidor (SSH), tras sincronizar GitHub con el directorio del VPS: `docker compose exec nginx nginx -t`, después `docker compose restart nginx`. Comprueba con `curl -I http://127.0.0.1:8089/resultados` y `curl -I http://127.0.0.1:8089/historial` (200 esperado), y `curl -I http://127.0.0.1:8089/resultados.html`, `curl -I http://127.0.0.1:8089/historial.html` y `curl -I http://127.0.0.1:8089/PHP/conexion.php` (404 esperado). No se ha probado desde esta sesión en el VPS.


## Compatibilidad con registros anteriores (octubre 2026)

Los análisis antiguos guardan `tabla_frecuencias`, `k_usado`, `medidas.media`, `medidas.varianza`, `medidas.desviacion_estandar` y `forma`, mientras los nuevos usan `tabla`, `k` y las medidas en el nivel superior. PHP/resultados.php adapta el formato antiguo solo en memoria, sin modificar MySQL ni recalcular los valores históricos. PHP/historial.php muestra N como suma de fi cuando el JSON antiguo tiene `total_datos: null` y toma k de `k_usado`.

**Cómo probar:** abre `/historial`: el registro Hola debe mostrar N=14 y k=5; Patoo debe mostrar N=8 y k=5. Pulsa Consultar en ambos: deben aparecer tabla, medidas y gráfica. Comprueba también un registro nuevo guardado desde `/resultados`. Si la columna `datos_originales` difiere del arreglo en JSON, se muestra un aviso; no se altera ningún registro.

**Precaución:** algunos registros antiguos pueden tener medidas incompatibles con sus tablas. Esta compatibilidad respeta exactamente las medidas almacenadas, sin presentarlas como cálculos nuevos ni corregirlas automáticamente.


### Corrección de pantalla blanca al consultar (octubre 2026)
En `PHP/resultados.php`, la función anónima de `array_map()` debe declarar `use ($resultados)` para poder leer los límites del análisis y formar la etiqueta de la última clase. Sin ello se produce un error de PHP. **Prueba:** después de sincronizar el archivo, abrir `/resultados?id=2` y `/resultados?id=1`; deben aparecer tarjetas, tabla e histograma. Si persiste una pantalla blanca, ejecutar en el servidor (SSH) `docker compose logs --tail=80 app` y revisar el error real. Los cambios PHP no requieren reiniciar Nginx.

## APIs independientes por integrante (20 de octubre de 2026)

Cada archivo PHP se ejecuta mediante una ruta Nginx pública sin exponer `/PHP/`. Los archivos son independientes como endpoints, pero la API de Diego reutiliza sus funciones con `require_once`.

| Integrante | Archivo | Ruta | Método |
| --- | --- | --- | --- |
| Joel Adrian Galego López | `PHP/JoelAdrianGalegoLopez.php` | `/api/joel-adrian-galego-lopez` | GET o POST |
| Obdulio Augusto Fuentes Fuentes | `PHP/ObdulioAugustoFuentesFuentes.php` | `/api/obdulio-augusto-fuentes-fuentes` | GET o POST |
| William Eliazar Mateo Devian | `PHP/WilliamEliazarMateoDevian.php` | `/api/william-eliazar-mateo-devian` | GET o POST |
| Diego Alejandro Vásquez Yol | `PHP/DiegoAlejandroVasquezYol.php` | `/api/diego-alejandro-vasquez-yol?operacion=...` | Depende de la operación |
| Kevin Danilo Morales Mejía | `JAVASCRIPT/KevinDaniloMoralesMejia.js` | `/JAVASCRIPT/KevinDaniloMoralesMejia.js` | Recurso JS |

### Qué hace cada API y cómo probarla

**Joel:** lee `titulo` y `datos`; comprueba título, números, separadores, errores y máximo 10000 valores. Devuelve `correcto`, `cantidad`, `numeros` y `errores`. En el servidor (SSH):

```bash
curl -G --data-urlencode 'titulo=Prueba' --data-urlencode 'datos=1 -2 3.5' http://127.0.0.1:8089/api/joel-adrian-galego-lopez
```

**Obdulio:** usa la validación de Joel y calcula rango, k de Sturges, amplitud, límites, xi, fi, Fi, hi, Hi. Prueba:

```bash
curl -G --data-urlencode 'titulo=Prueba' --data-urlencode 'datos=0 1 2 3 4 5 6 7 8 10' http://127.0.0.1:8089/api/obdulio-augusto-fuentes-fuentes
```

**William:** usa los intervalos de Obdulio y calcula media, varianza poblacional, desviación, asimetría, curtosis y exceso. Prueba:

```bash
curl -G --data-urlencode 'titulo=Prueba' --data-urlencode 'datos=0 1 2 3 4 5 6 7 8 10' http://127.0.0.1:8089/api/william-eliazar-mateo-devian
```

**Diego:** coordina `analizar`, `guardar`, `listar` y `consultar`. Para guardar y consultar usa `PHP/conexion.php`. Prueba:

```bash
curl -X POST -d 'titulo=Prueba' --data-urlencode 'datos=0 1 2 3 4 5 6 7 8 10' 'http://127.0.0.1:8089/api/diego-alejandro-vasquez-yol?operacion=analizar'
curl -X POST -d 'titulo=Prueba' --data-urlencode 'datos=0 1 2 3 4 5 6 7 8 10' 'http://127.0.0.1:8089/api/diego-alejandro-vasquez-yol?operacion=guardar'
curl 'http://127.0.0.1:8089/api/diego-alejandro-vasquez-yol?operacion=listar'
curl 'http://127.0.0.1:8089/api/diego-alejandro-vasquez-yol?operacion=consultar&id=1'
```

**Kevin:** `JAVASCRIPT/KevinDaniloMoralesMejia.js` está **conectado y en uso** desde `PHP/resultados.php`. Recibe la tabla de frecuencias mediante `dibujar_histograma_kevin(resultados.tabla)` llamada por `JAVASCRIPT/resultados.js` y dibuja el histograma con Chart.js. No utiliza `window.datos_histograma`, no recalcula frecuencias y no es una copia provisional.

### Activación

En el servidor (SSH), una vez sincronizado el repositorio:

```bash
cd /home/alejo/Almacenamiento/Paginas/Proyecto_estadistica
docker compose exec nginx nginx -t
docker compose restart nginx
```

Las rutas nuevas no estarán disponibles hasta que Nginx recargue su configuración. No se modificó `docker-compose.yml`.

**Pendiente de integración:** las páginas existentes `/resultados` y `/historial` siguen usando su lógica PHP previa. La API de Diego es un endpoint independiente y aún no sustituye el flujo de esas páginas. No se han ejecutado pruebas en el VPS.

## Integración real de la pantalla Resultados con las APIs (20/10/2026)

**Flujo nuevo:** `HTML/index.html` y `JAVASCRIPT/index.js` guardan temporalmente el título y los datos en `sessionStorage` y abren `/resultados`, sin incluir la serie en la URL. `PHP/resultados.php` solo entrega la estructura HTML. `JAVASCRIPT/resultados.js` hace `POST /api/diego-alejandro-vasquez-yol?operacion=analizar`; la API de Diego llama las funciones de Joel (validación), Obdulio (intervalos) y William (medidas). Kevin dibuja el histograma con Chart.js usando el resultado. El botón Guardar llama `POST ...?operacion=guardar`, que inserta en MySQL a través de `conexion.php`. La consulta de un historial usa `GET ...?operacion=consultar&id=...` y muestra el JSON guardado, incluidos los formatos antiguos.

**Pruebas por archivo:**
- `HTML/index.html`: abre `/index.html`, introduce título y datos, pulsa Generar; la dirección debe cambiar a `/resultados` sin datos en la URL.
- `JAVASCRIPT/index.js`: prueba `1 2 3` y luego `abc`; el segundo caso debe mostrar error y no navegar.
- `PHP/resultados.php`: abre `/resultados` directamente en una pestaña nueva; debe mostrar un aviso si no hay datos guardados en esa pestaña.
- `JAVASCRIPT/resultados.js`: en las herramientas del navegador, pestaña Red, comprueba la petición `POST /api/diego-alejandro-vasquez-yol?operacion=analizar`. Con `0 1 2 3 4 5 6 7 8 10`, N=10, k=5, R=10, A=2, media=5, varianza=8.
- `JAVASCRIPT/KevinDaniloMoralesMejia.js`: comprueba que aparecen cinco barras contiguas; prueba también todos los números iguales.
- `PHP/DiegoAlejandroVasquezYol.php`: pulsa Guardar y comprueba la petición `POST ...?operacion=guardar`; después consulta el ID desde el historial y revisa `GET ...?operacion=consultar&id=...`.

**Importante:** `PHP/historial.php` todavía obtiene la lista directamente de MySQL. Esta etapa cambia la generación y consulta de resultados, no la pantalla Historial. No se han ejecutado pruebas en el VPS. Se requiere JavaScript y almacenamiento de sesión del navegador para generar desde el formulario. Chart.js se carga desde CDN.


## Cambio de arquitectura: peticiones HTTP por compañero (20/10/2026)

**Este apartado reemplaza el flujo anterior de la API coordinadora de Diego.** La pantalla `/resultados` realiza tres peticiones visibles en F12 > Red > Fetch/XHR, en orden: `POST /api/joel-adrian-galego-lopez` con `titulo` y `datos`; `POST /api/obdulio-augusto-fuentes-fuentes` con `numeros` (JSON del arreglo validado); `POST /api/william-eliazar-mateo-devian` con `intervalos` (JSON devuelto por Obdulio). JavaScript únicamente reúne las respuestas y muestra la tabla, tarjetas e histograma con el módulo de Kevin. **No recalcula las estadísticas.**

La API de Diego **ya no tiene operación analizar** y no incluye archivos de Joel, Obdulio ni William. Solo gestiona `POST ...?operacion=guardar` (recibe `titulo`, `datos`, `resultados` JSON), `GET ...?operacion=listar` y `GET ...?operacion=consultar&id=2`. El historial ahora se construye con JavaScript después de llamar a la operación `listar`. Los registros antiguos siguen consultables con la adaptación de presentación en `resultados.js`.

### Pruebas por archivo

- `PHP/JoelAdrianGalegoLopez.php`: envía título y datos, verifica la respuesta `numeros` y prueba valores inválidos.
- `PHP/ObdulioAugustoFuentesFuentes.php`: envía `numeros=[0,1,2,3,4,5,6,7,8,10]`; verifica N=10, k=5, R=10, amplitud=2 y cinco frecuencias de 2.
- `PHP/WilliamEliazarMateoDevian.php`: envía el JSON de Obdulio en `intervalos`; verifica media=5, varianza=8, desviación≈2.8284, asimetría=0, curtosis=1.7, exceso=-1.3.
- `JAVASCRIPT/resultados.js`: genera un análisis en el navegador y comprueba las tres solicitudes POST independientes en la pestaña Red; el botón Guardar genera una cuarta solicitud a Diego.
- `PHP/DiegoAlejandroVasquezYol.php`: guarda un análisis y consulta la lista y su ID; verifica que no aparezca la operación `analizar`.
- `PHP/historial.php` y `JAVASCRIPT/historial.js`: abre `/historial`; en Red debe aparecer `GET ...?operacion=listar`; Consultar abre `/resultados?id=...` y solicita `consultar`.

No hay cambios en Nginx ni en Docker Compose porque las rutas ya existían. **Pendiente:** ejecutar pruebas en el VPS y verificar que las APIs y la conexión MySQL responden correctamente. No usar los ejemplos antiguos de este README que envían `datos` directamente a Obdulio o William.


## Limpieza de archivos y módulo de Kevin (20/10/2026)

- **En uso:** `HTML/index.html`; `PHP/resultados.php`, `PHP/historial.php`, `PHP/conexion.php` y las cuatro APIs por compañero; `CSS/index.css`, `CSS/resultados.css`, `CSS/historial.css`; `JAVASCRIPT/index.js`, `JAVASCRIPT/resultados.js`, `JAVASCRIPT/historial.js` y `JAVASCRIPT/KevinDaniloMoralesMejia.js`.
- **Eliminados:** `HTML/resultados.html` y `HTML/historial.html`, porque eran maquetas duplicadas sin participación en el flujo actual.
- **Kevin:** `PHP/resultados.php` carga Chart.js, luego `KevinDaniloMoralesMejia.js` y `resultados.js`. Cuando llegan las respuestas de las APIs, `resultados.js` invoca `dibujar_histograma_kevin(resultados.tabla)`. El histograma muestra barras contiguas con límites de clase en el eje X. El código de Kevin no calcula estadísticas.
- **Prueba:** abre `/index.html`, introduce `0 1 2 3 4 5 6 7 8 10` y pulsa Generar. En `/resultados` comprueba cinco barras contiguas con frecuencias 2, y en F12 > Red comprueba la carga de `KevinDaniloMoralesMejia.js` y las tres peticiones de Joel, Obdulio y William.
- **Nginx:** se retiraron solo las dos rutas de las maquetas eliminadas. Tras sincronizar los cambios, en el servidor (SSH), desde la carpeta del proyecto, ejecuta `docker compose exec nginx nginx -t` y, si pasa, `docker compose restart nginx`. No se modificó `docker-compose.yml`.

Las descripciones históricas de etapas anteriores en este README pueden referirse a implementaciones reemplazadas; la sección **Cambio de arquitectura: peticiones HTTP por compañero** y esta sección describen el funcionamiento actual.
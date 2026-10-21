# Proyecto Estadística I

Sistema web para analizar **datos agrupados**. Proyecto académico de la Universidad Mariano Gálvez de Guatemala.

**Sitio:** https://estadistica.viuai.qd.je/  
**Diseño:** [Figma](https://www.figma.com/design/jE09iair5o1Q2ZeRImNVTG/Proyecto-Estadiditica)  
**Última revisión del repositorio:** 20 de octubre de 2026.

> **Estado importante:** este README describe la arquitectura prevista y lo que se observa en el código de la rama `master`. En la revisión actual **no aparecen** los archivos PHP de Joel, Obdulio y William, ni `JAVASCRIPT/KevinDaniloMoralesMejia.js`, aunque Nginx y la interfaz los referencian. Hasta restaurarlos o integrarlos, no puedo confirmar que la generación completa y el histograma funcionen en una instalación nueva. No he probado el VPS desde esta sesión.

## 1. Qué hace mi proyecto

Ingreso un título y una serie de números. La aplicación valida los datos, los agrupa en intervalos, muestra una tabla de frecuencias y calcula medidas estadísticas poblacionales. También ofrece un histograma y opciones para guardar y consultar análisis en MySQL.

Las pantallas públicas son:

- **`/` o `/index.html`:** formulario de Nuevo análisis.
- **`/resultados`:** tabla, medidas, fórmulas e histograma.
- **`/historial`:** lista de análisis guardados y botón Consultar.

No utilizo `HTML/resultados.html` ni `HTML/historial.html`: esas maquetas se eliminaron. Las pantallas correspondientes se sirven con PHP.

## 2. Tecnologías

| Parte | Tecnología | Para qué la utilizo |
| --- | --- | --- |
| Estructura | HTML | Formularios y contenido de las pantallas |
| Diseño | CSS | Colores, tarjetas, tamaños grandes y adaptación móvil |
| Interacción | JavaScript puro | Validación inicial, peticiones HTTP y presentación |
| Servidor | PHP | APIs y páginas que entregan HTML |
| Base de datos | MySQL/MariaDB con `mysqli` | Guardar y consultar análisis |
| Histograma | Chart.js 4.4.7 | Dibujar barras de frecuencia |
| Fórmulas | MathJax 3 | Mostrar fracciones, sumatorias, raíces y exponentes |
| Despliegue | Docker Compose, PHP-FPM y Nginx | Publicar la aplicación |

**Dependencias del navegador:** `PHP/resultados.php` incluye MathJax 3 y Chart.js 4.4.7 mediante CDN. En el código revisado también sigue apareciendo una referencia a `/JAVASCRIPT/chart.umd.js`, archivo que no figura en el directorio actual. Debo revisar esa referencia duplicada; no considero resuelto el histograma solo por haber añadido el CDN.

## 3. Estructura actual del repositorio

```text
Proyecto_estadistica/
├── HTML/
│   └── index.html
├── CSS/
│   ├── index.css
│   ├── resultados.css
│   └── historial.css
├── JAVASCRIPT/
│   ├── index.js
│   ├── resultados.js
│   └── historial.js
├── PHP/
│   ├── index.php
│   ├── resultados.php
│   ├── historial.php
│   ├── conexion.php
│   └── DiegoAlejandroVasquezYol.php
├── docker/
│   └── nginx.conf
├── Dockerfile
├── docker-compose.yml
├── README.md
└── Datos prueba .md
```

**Archivos referenciados, pero ausentes en la revisión de `master`:**

- `PHP/JoelAdrianGalegoLopez.php`
- `PHP/ObdulioAugustoFuentesFuentes.php`
- `PHP/WilliamEliazarMateoDevian.php`
- `JAVASCRIPT/KevinDaniloMoralesMejia.js`
- `JAVASCRIPT/chart.umd.js`

La carpeta `.env` no forma parte del código público: la configuración de la base de datos debe mantenerse privada. Los manuales `MANUAL_USUARIO.md` y `MANUAL_TECNICO.md` tampoco aparecen actualmente en la raíz de esta rama; no los presento como archivos existentes.

## 4. Qué hace cada archivo

### HTML y CSS

| Archivo | Qué hace |
| --- | --- |
| `HTML/index.html` | Muestra título, campo de datos, botones Generar/Limpiar y navegación. |
| `CSS/index.css` | Estilos de Nuevo análisis, formulario amplio, entradas grandes y reglas para móviles. |
| `CSS/resultados.css` | Estilos de las tarjetas, tabla de frecuencias, fórmulas y área del histograma. |
| `CSS/historial.css` | Estilos de las tarjetas del historial, títulos, fechas y botón Consultar. |

### JavaScript

| Archivo | Qué hace |
| --- | --- |
| `JAVASCRIPT/index.js` | Comprueba los valores ingresados, muestra errores, guarda temporalmente los datos en `sessionStorage` y abre Resultados. |
| `JAVASCRIPT/resultados.js` | Solicita validación, intervalos y medidas a las APIs; presenta los datos, tarjetas y tabla; intenta llamar al módulo de Kevin; permite guardar y consultar registros. |
| `JAVASCRIPT/historial.js` | Pide la lista a la API de Diego y construye los registros visibles del historial. |
| `JAVASCRIPT/KevinDaniloMoralesMejia.js` | **Previsto, pero ausente:** función `dibujar_histograma_kevin(tabla)` para dibujar con Chart.js barras contiguas y escala X lineal. |

### PHP

| Archivo | Qué hace |
| --- | --- |
| `PHP/index.php` | Conserva una versión PHP de la pantalla de entrada; la ruta pública principal sirve `HTML/index.html`. |
| `PHP/resultados.php` | Entrega el HTML de Resultados, carga CSS, Chart.js, MathJax y los scripts de la pantalla. No debe calcular las medidas. |
| `PHP/historial.php` | Entrega el HTML de Historial; la consulta se realiza desde JavaScript. |
| `PHP/conexion.php` | Crea la conexión MySQL mediante `mysqli` y variables de entorno. |
| `PHP/DiegoAlejandroVasquezYol.php` | API para guardar, listar y consultar análisis de MySQL. |
| `PHP/JoelAdrianGalegoLopez.php` | **Previsto, ausente:** validación de título y serie numérica. |
| `PHP/ObdulioAugustoFuentesFuentes.php` | **Previsto, ausente:** intervalos de Sturges y frecuencias. |
| `PHP/WilliamEliazarMateoDevian.php` | **Previsto, ausente:** medidas estadísticas agrupadas. |

### Servidor

| Archivo | Qué hace |
| --- | --- |
| `Dockerfile` | Prepara la imagen PHP-FPM y sus extensiones. |
| `docker-compose.yml` | Define los servicios `app` y `nginx` y sus volúmenes. |
| `docker/nginx.conf` | Publica las rutas autorizadas, sirve CSS/JS y envía las solicitudes PHP a `app:9000`. |

## 5. Flujo de las APIs por compañero

La arquitectura que utiliza `JAVASCRIPT/resultados.js` está pensada para que las llamadas sean **visibles por separado** en **F12 → Red → Fetch/XHR**:

```text
Nuevo análisis (HTML/index.html)
          |
          v
JAVASCRIPT/index.js
          |
          v
/resultados
          |
          v
JAVASCRIPT/resultados.js
          |
          +-- POST /api/joel-adrian-galego-lopez
          |         Valida título y números
          |
          +-- POST /api/obdulio-augusto-fuentes-fuentes
          |         Agrupa los números y crea frecuencias
          |
          +-- POST /api/william-eliazar-mateo-devian
          |         Calcula las medidas poblacionales
          |
          +-- JAVASCRIPT/KevinDaniloMoralesMejia.js
          |         Dibuja el histograma (archivo ausente)
          |
          +-- POST /api/diego-alejandro-vasquez-yol?operacion=guardar
                    Guarda en MySQL

/historial
    |
    +-- GET /api/diego-alejandro-vasquez-yol?operacion=listar
    |
    +-- Consultar -> /resultados?id=...
                          |
                          +-- GET /api/diego-alejandro-vasquez-yol?operacion=consultar&id=...
```

La API de Diego **no realiza el análisis matemático**. El historial lee el JSON guardado; no recalcula los resultados.

**Rutas configuradas en Nginx:** las cuatro rutas `/api/...` aparecen en `docker/nginx.conf`. Configurar una ruta no significa que el archivo PHP correspondiente exista: los tres endpoints matemáticos necesitan sus archivos para responder correctamente.

## 6. Estadísticas que quiero mostrar

Para `N` datos agrupados y una tabla de frecuencias con `fi` y marcas `xi`:

| Medida | Fórmula |
| --- | --- |
| Rango | `R = máximo - mínimo` |
| Clases teóricas | `1 + 3.322 × log10(N)` |
| Clases utilizadas | `ceil(k_teórico)` cuando el rango es mayor que cero |
| Amplitud | `A = R / k` |
| Marca de clase | `xi = (LI + LS) / 2` |
| Frecuencia relativa | `hi = fi / N` |
| Frecuencia acumulada | `Fi = suma de fi hasta esa clase` |
| Frecuencia relativa acumulada | `Hi = Fi / N` |
| Media agrupada | `μ = Σ(fi × xi) / N` |
| Varianza poblacional | `σ² = Σ[fi × (xi − μ)²] / N` |
| Desviación estándar | `σ = √σ²` |
| Asimetría | `g1 = Σ[fi × (xi − μ)³] / (N × σ³)` |
| Curtosis | `K = Σ[fi × (xi − μ)⁴] / (N × σ⁴)` |
| Exceso de curtosis | `Ke = K − 3` |

Uso intervalos `[LI, LS)`, excepto el último `[LI, LS]`. Si todos los datos son iguales, corresponde una sola clase, amplitud cero y varianza cero; las medidas que dividen entre `σ` quedan sin definir.

### Fórmulas con MathJax

En `PHP/resultados.php` cargué **MathJax 3** para que las fórmulas aparezcan con fracciones, raíces, exponentes y sumatorias. En `JAVASCRIPT/resultados.js` agrego las fórmulas de las tarjetas dinámicamente y solicito su renderizado después de insertar los elementos. MathJax solo cambia la **presentación**, no el cálculo.

## 7. Diseño grande para la exposición

Las tres hojas CSS tienen reglas para que la presentación sea legible en un monitor o proyector. En escritorio el contenido puede ocupar hasta el **96 % del ancho**, con un límite de **1900 px**.

| Elemento | Tamaño de presentación aproximado |
| --- | --- |
| Títulos principales | 54 px |
| Subtítulos | 27–29 px |
| Texto de entrada | 25 px |
| Valores de resultados | 38 px |
| Texto de tablas y fórmulas | 22 px |
| Botones | 23–24 px |
| Altura del histograma | 520 px |

Las reglas para pantallas pequeñas conservan una disposición adaptable. Las páginas incluyen parámetros de versión en los enlaces CSS, como `?v=presentacion-20261020`, para ayudar a evitar estilos antiguos en la caché.

**Importante:** estos son cambios en el código. No he comprobado visualmente el despliegue actual en el VPS.

## 8. Datos de prueba

Para comprobar el cálculo agrupado, utilizo:

```text
0 1 2 3 4 5 6 7 8 10
```

**Resultados matemáticos esperados**, cuando los módulos estén completos:

| Resultado | Valor |
| --- | ---: |
| N | 10 |
| Rango | 10 |
| k teórico | 4.322 |
| k usado | 5 |
| Amplitud | 2 |
| Frecuencia por clase | 2 |
| Media | 5 |
| Varianza poblacional | 8 |
| Desviación estándar | 2.8284 |
| Asimetría | 0 |
| Curtosis | 1.7 |
| Exceso de curtosis | -1.3 |

También debo probar números negativos, decimales con punto, entradas inválidas, datos idénticos y análisis históricos. **Esta tabla es una referencia matemática, no el resultado de una prueba ejecutada en el servidor.**

## 9. Cómo comprobar el sistema

### En el navegador

1. Abro `/index.html` y escribo un título y los datos de prueba.
2. Pulso **Generar análisis**.
3. En **F12 → Red → Fetch/XHR**, compruebo las peticiones a Joel, Obdulio y William. Si faltan los PHP correspondientes, debo resolverlo antes de dar por terminada la integración.
4. Compruebo la tabla y las medidas.
5. En **F12 → Red**, compruebo la carga de Chart.js, MathJax y del archivo de Kevin. Si falta Kevin, el histograma puede aparecer vacío.
6. Pulso **Guardar análisis** y compruebo la petición a Diego.
7. Abro **Historial** y compruebo `listar` y luego `consultar`.
8. Reduzco el ancho del navegador para revisar el diseño móvil.

### En el servidor (SSH)

Desde la carpeta real del proyecto en el VPS:

```bash
cd /home/alejo/Almacenamiento/Paginas/Proyecto_estadistica
git status
docker compose ps
docker compose exec nginx nginx -t
docker compose logs --tail=50 app
docker compose logs --tail=50 nginx
```

Si la copia de trabajo está limpia y necesito sincronizar `master`:

```bash
git pull origin master
```

Después de modificar `docker/nginx.conf`, verifico la sintaxis y reinicio el contenedor Nginx:

```bash
docker compose exec nginx nginx -t
docker compose restart nginx
```

Si solo modifico HTML, PHP, CSS o JavaScript, normalmente basta con recargar la página. Si modifico el `Dockerfile`, necesito reconstruir la imagen correspondiente.

**Mi PC y el VPS:** el directorio SSHFS montado en mi PC corresponde a los mismos archivos del servidor; no debo tratarlos como dos copias independientes.

## 10. Guardado y seguridad

La tabla de MySQL se llama `analisis` y utiliza los campos `id`, `titulo`, `fecha`, `datos_originales`, `criterio_clases` y `resultados`. El último guarda un JSON con los resultados para consultarlos posteriormente.

La conexión se configura mediante las variables de entorno `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`. Nunca debo escribir contraseñas reales en el repositorio.

Nginx bloquea el acceso directo a `/PHP/*.php` y a archivos internos. Para ejecutar PHP desde el navegador uso exclusivamente las rutas públicas configuradas.

## 11. Qué falta verificar

- [ ] Confirmar o restaurar los tres PHP de Joel, Obdulio y William.
- [ ] Confirmar o restaurar `JAVASCRIPT/KevinDaniloMoralesMejia.js`.
- [ ] Resolver la referencia antigua `/JAVASCRIPT/chart.umd.js` y comprobar la carga efectiva de Chart.js.
- [ ] Probar el renderizado dinámico de MathJax.
- [ ] Comprobar que el diseño grande se vea en el VPS y en el proyector.
- [ ] Probar el guardado y la consulta real en MySQL.
- [ ] Confirmar si los manuales Markdown deben restaurarse en `master`.

No modifico las APIs ni el diseño al actualizar este documento: solo describo lo que existe y lo que todavía necesita verificación.

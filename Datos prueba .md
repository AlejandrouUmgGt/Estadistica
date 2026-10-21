# Datos de prueba — Proyecto Estadística I

Resultados esperados calculados con las mismas APIs del proyecto (varianza poblacional, marcas de clase, k = ⌈Sturges⌉). Los valores se muestran con hasta 4 decimales.

Cada caso sirve para comprobar algo distinto: entero simple, σ = 0, decimales y negativos, asimetría y un conjunto grande.

## A. Base (10 datos enteros)

```text
0 1 2 3 4 5 6 7 8 10
```

**N = 10**, **R = 10**, k teórico = 4.322, **k = 5**, **A = 2**

Media = **5** · Varianza = **8** · σ = **2.8284** · g1 = **0** · K = **1.7** · Ke = **-1.3**

| Intervalo | xi | fi | Fi | hi | Hi |
|---|---|---|---|---|---|
| [0, 2) | 1 | 2 | 2 | 0.2 | 0.2 |
| [2, 4) | 3 | 2 | 4 | 0.2 | 0.4 |
| [4, 6) | 5 | 2 | 6 | 0.2 | 0.6 |
| [6, 8) | 7 | 2 | 8 | 0.2 | 0.8 |
| [8, 10] | 9 | 2 | 10 | 0.2 | 1 |
| **Total** | | **10** | | **1** | |

## B. Todos iguales (σ = 0)

```text
7 7 7 7 7 7
```

**N = 6**, **R = 0**, k teórico = 3.585, **k = 1**, **A = 0**

Media = **7** · Varianza = **0** · σ = **0** · g1 = **No definido** · K = **No definido** · Ke = **No definido**

| Intervalo | xi | fi | Fi | hi | Hi |
|---|---|---|---|---|---|
| [7, 7] | 7 | 6 | 6 | 1 | 1 |
| **Total** | | **6** | | **1** | |

## C. Decimales y negativos

```text
-2.5 -1 0 0.5 1.25 3 4.75 6 7.5 9
```

**N = 10**, **R = 11.5**, k teórico = 4.322, **k = 5**, **A = 2.3**

Media = **3.02** · Varianza = **11.0561** · σ = **3.3251** · g1 = **0.1747** · K = **1.5993** · Ke = **-1.4007**

| Intervalo | xi | fi | Fi | hi | Hi |
|---|---|---|---|---|---|
| [-2.5, -0.2) | -1.35 | 2 | 2 | 0.2 | 0.2 |
| [-0.2, 2.1) | 0.95 | 3 | 5 | 0.3 | 0.5 |
| [2.1, 4.4) | 3.25 | 1 | 6 | 0.1 | 0.6 |
| [4.4, 6.7) | 5.55 | 2 | 8 | 0.2 | 0.8 |
| [6.7, 9] | 7.85 | 2 | 10 | 0.2 | 1 |
| **Total** | | **10** | | **1** | |

## D. Asimétrico (g1 ≠ 0)

```text
1 1 1 2 2 2 2 3 3 4 5 8 13 21
```

**N = 14**, **R = 20**, k teórico = 4.8074, **k = 5**, **A = 4**

Media = **5.5714** · Varianza = **24.2449** · σ = **4.9239** · g1 = **1.8521** · K = **4.9659** · Ke = **1.9659**

| Intervalo | xi | fi | Fi | hi | Hi |
|---|---|---|---|---|---|
| [1, 5) | 3 | 10 | 10 | 0.7143 | 0.7143 |
| [5, 9) | 7 | 2 | 12 | 0.1429 | 0.8571 |
| [9, 13) | 11 | 0 | 12 | 0 | 0.8571 |
| [13, 17) | 15 | 1 | 13 | 0.0714 | 0.9286 |
| [17, 21] | 19 | 1 | 14 | 0.0714 | 1 |
| **Total** | | **14** | | **1** | |

## E. Grande (120 datos)

```text
47 60 72 100 72 81 95 100 46 96 54 97 78 79 75 66 90 76 75 93 86 89 89 71 88 89 77 68 55 40 79 45 47 58 92 46 68 40 92 97 83 71 83 60 53 65 56 62 99 62 91 64 87 72 80 44 86 61 45 75 74 58 58 69 49 96 81 85 85 76 59 41 94 85 63 63 69 67 45 65 97 77 75 71 95 47 67 72 91 88 95 78 96 71 65 98 73 56 94 66 76 99 70 72 73 91 41 76 54 89 48 92 43 85 73 96 82 46 85 79
```

**N = 120**, **R = 60**, k teórico = 7.907, **k = 8**, **A = 7.5**

Media = **73.375** · Varianza = **282.984** · σ = **16.8221** · g1 = **-0.3319** · K = **2.0273** · Ke = **-0.9727**

| Intervalo | xi | fi | Fi | hi | Hi |
|---|---|---|---|---|---|
| [40, 47.5) | 43.75 | 15 | 15 | 0.125 | 0.125 |
| [47.5, 55) | 51.25 | 5 | 20 | 0.0417 | 0.1667 |
| [55, 62.5) | 58.75 | 12 | 32 | 0.1 | 0.2667 |
| [62.5, 70) | 66.25 | 14 | 46 | 0.1167 | 0.3833 |
| [70, 77.5) | 73.75 | 24 | 70 | 0.2 | 0.5833 |
| [77.5, 85) | 81.25 | 11 | 81 | 0.0917 | 0.675 |
| [85, 92.5) | 88.75 | 21 | 102 | 0.175 | 0.85 |
| [92.5, 100] | 96.25 | 18 | 120 | 0.15 | 1 |
| **Total** | | **120** | | **1** | |

## F. Entradas inválidas (deben rechazarse)

| Entrada | Resultado esperado |
|---|---|
| `2 abc 5` | Error: no se pudo leer `abc` |
| `1e3 5 7` | Error: no se pudo leer `1e3` (no se admite notación científica) |
| `3,5` | Se interpreta como dos datos: 3 y 5 (la coma separa valores) |
| (vacío) | Error: ingresa al menos un número |
| Título con más de 200 caracteres | Error de título (se cuentan caracteres, no bytes) |
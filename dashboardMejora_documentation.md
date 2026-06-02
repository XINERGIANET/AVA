# Documentación: Dashboard Financiero Nativo AVA

Este documento resume la migración, diseño y arquitectura del nuevo Dashboard Financiero implementado en el sistema AVA, reemplazando la antigua integración de Power BI por una solución nativa de alto rendimiento construida con Laravel y Chart.js.

## 1. Arquitectura de Datos (Backend)

La lógica de recolección de datos se ha centralizado en el `DashboardController`. Se han reemplazado los datos simulados por consultas reales a la base de datos `ava`.

**Modelos y Tablas Utilizadas:**
- **Ventas (`sales`):** Se suma el total de las ventas procesadas para calcular los ingresos mensuales y diarios.
- **Egresos (`transactions`):** Se filtran las transacciones cuyo tipo sea `'scc'` (salida de caja chica/gastos) para calcular los egresos.
- **Cierres de Caja (`cash_closes`):** Se utiliza la columna `real_cash_amount` para determinar el efectivo real reportado en bóveda.
- **Métodos de Pago (`payments` y `payment_methods`):** Se realiza un cruce relacional (JOIN) para categorizar los ingresos en: Efectivo, Yape/Plin, Transferencias y Créditos.
- **Venta Real vs Teórica:** Se compara el monto total real cobrado (`sales`) versus las mediciones físicas teóricas registradas en tanque (`measurements.amount_theorical`).

**Filtros Globales:**
Todas las consultas respetan un filtro de eliminación lógica (`deleted = 0`) para no sumar registros anulados. Además, el panel permite la filtración dinámica por:
- **Sede (`location_id`)**
- **Año (`year`)**
- **Mes (`month`):** Soporta tanto meses específicos como la opción `"Todos los meses"`, la cual omite la restricción mensual para mostrar el acumulado anual completo.

---

## 2. UI / UX: Diseño Corporativo "Glassmorphism"

El diseño visual ha sido reconstruido desde cero priorizando la limpieza, la legibilidad y el enfoque netamente financiero (omitiendo operativas como galonajes).

### Características Visuales:
- **Paleta de Colores Moderna:** Se abandonaron los rojos agresivos en fondos grandes. Ahora predomina el azul corporativo (`#1d4ed8`), blancos limpios y grises claros (`#f8fafc`).
- **Sombras y Bordes Suaves:** Se utilizan sombras muy ligeras (`box-shadow: 0 4px 6px rgba(0,0,0,0.05)`) y bordes redondeados (`border-radius: 12px`) para dar un efecto de profundidad tipo *Glassmorphism*.
- **Iconografía (FontAwesome):** Integración de iconos representativos (billeteras, gráficos, celulares, balanzas) acompañados de fondos de colores pastel (fondos semi-transparentes o aclarados) en lugar de bloques sólidos.

### Disposición del Layout (Grid System):
1. **Columna Izquierda (Filtros):** Tarjeta blanca limpia con desplegables estilizados y el botón azul principal de "Actualizar".
2. **Top KPIs (Derecha Superior):**
   - **Tarjeta Hero (Azul):** El "Total de Ingresos" destaca inmediatamente gracias a su fondo azul sólido, texto blanco y semicírculo verde brillante.
   - **Tarjetas Complementarias:** "Balance Actual" (con un mini-gráfico de área verde) y "Gastos Totales" (con un icono rojo destacado).
3. **Cinta de Resumen (Ribbon):** Barra horizontal consolidada que resume Ventas, Ingresos a Caja, Balances, Rentabilidad y Gastos en bloques compactos con iconos.
4. **Métodos de Pago (Grid 2x2):** Cuadrícula inferior izquierda que desglosa el ingreso según la pasarela (Efectivo, Transferencias, etc.).
5. **Gráfico Comparativo:** Gráfico de barras de "Venta Real vs Teórica" alineado a la derecha.

---

## 3. Funcionalidades de Interacción

- **Pantalla Completa (Fullscreen):** Se añadió un botón en la esquina superior derecha que permite expandir el área del Dashboard al 100% de la pantalla. Al activarse, esta función **oculta inteligentemente el menú lateral y la cabecera del sistema**, ofreciendo una experiencia inmersiva ideal para proyecciones en televisores o monitores de monitoreo.

## 4. Archivos Modificados

- `app/Http/Controllers/DashboardController.php`: Lógica de recolección, sumatorias y preparación de arrays para Chart.js.
- `resources/views/reports/alternativo.blade.php`: Vista frontal con el diseño UI/UX (CSS integrado) y scripts de Chart.js.
- `routes/web.php`: Redireccionamiento de la ruta `/main`.

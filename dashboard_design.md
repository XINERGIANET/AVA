# Diseño de Panel de Indicadores (Dashboard)

Este documento detalla la estructura visual y de experiencia de usuario (UI/UX) del panel de indicadores, omitiendo la lógica de datos subyacente. El objetivo es proporcionar una guía clara para replicar este diseño en cualquier otro sistema, independientemente del rubro comercial.

## 1. Estructura General y Layout

El diseño utiliza un **enfoque de cuadrícula (Grid Layout)** o flexbox, muy común en frameworks como Bootstrap o Tailwind CSS. Se divide en dos secciones principales a nivel de pantalla:

*   **Barra Lateral (Sidebar - opcional/implícita):** A la izquierda, se intuye una barra de navegación (indicada por la línea azul en el borde izquierdo).
*   **Contenido Principal (Main Wrapper):** Ocupa la mayor parte de la pantalla, con un fondo gris muy claro (casi blanco) para resaltar los elementos de las tarjetas (cards) blancas.

### Jerarquía Visual
El tablero está diseñado para leerse de **arriba hacia abajo** y de **izquierda a derecha**, destacando primero los filtros, luego los KPIs (Key Performance Indicators) más importantes, y finalmente el desglose detallado.

---

## 2. Paleta de Colores

El diseño se apoya en colores semánticos (colores con significado) para comunicar el estado financiero rápidamente:

*   **Rojo intenso (Peligro/Gastos o Alerta):** Usado para los Egresos/Gastos, botones de acción primaria y gráficos relacionados con salidas de dinero.
*   **Verde esmeralda (Éxito/Ingresos/Rentabilidad):** Usado para métricas positivas, balances a favor y mensajes de éxito.
*   **Azul corporativo:** Utilizado en gráficos (para diferenciar categorías) y títulos principales.
*   **Gris claro y oscuro:** Para fondos, bordes de tarjetas, textos secundarios y métricas neutras (como porcentajes).
*   **Blanco:** Para el fondo de todas las tarjetas (Cards), creando un contraste limpio contra el fondo gris del contenedor principal.
*   **Marrón claro / Beige:** Usado en tarjetas específicas como "Ventas" para diferenciarlo de ingresos netos o gastos.

---

## 3. Componentes Principales

Para replicar este diseño, debes construir los siguientes componentes reutilizables:

### A. Panel de Control y Filtros (Columna Izquierda)
Una columna estrecha (aprox. 20-25% del ancho) fijada a la izquierda o al principio del contenido:
*   **Selectores (Dropdowns):** Con estilo de botones en bloque. El fondo rojo indica la acción de selección.
*   **Botón de Acción Primaria:** "Actualizar", manteniendo el mismo estilo de bloque rojo.
*   **Alerta de Estado (Alert):** Un mensaje de texto verde con fondo verde muy claro para dar feedback al usuario ("Excelente...").

### B. Gráficos de Resumen Rápido (Gauge & Pie Charts)
Ubicados en la misma columna izquierda inferior y centro superior:
*   **Gráfico de Medidor (Gauge / Semi-Doughnut):** Se usa para mostrar el % de Rentabilidad Bruta y el Total de Ingresos (con el semicírculo rojo masivo). Son excelentes para llamar la atención sobre un solo número clave.
*   **Gráfico Circular (Doughnut Chart):** Muestra la proporción entre dos variables (Ej. Ingresos vs Egresos) con una leyenda simple abajo.

### C. Tarjetas de KPI (Key Performance Indicator Cards)
Son los bloques rectangulares blancos con sombra sutil y bordes redondeados.
*   **Tarjetas Superiores:** Muestran el Balance Actual y los Gastos Totales de forma limpia, centrando el valor numérico (en verde o rojo según corresponda) y el título abajo en letras pequeñas y en mayúsculas.
*   **Gráfico de Área (Minichart):** Una tarjeta que en lugar de un número estático muestra una tendencia simple (un gráfico de líneas relleno de color, sin ejes complejos).

### D. Cinta de Resumen (Summary Ribbon)
Una barra horizontal continua dividida en varios bloques de colores.
*   Actúa como un separador visual entre la parte superior (resumen macro) y la parte inferior (desglose detallado).
*   Cada bloque tiene un color de fondo distinto (Beige, Verde, Rojo, Gris) con texto en blanco, mostrando el concepto arriba y el monto abajo.

### E. Cuadrícula de Métricas Detalladas (Grid de 3x2)
Una sección de tarjetas blancas pequeñas con bordes grises.
*   Distribución en columnas (Ej. 3 columnas en pantallas grandes).
*   Diseño minimalista: Número grande y audaz en la parte superior, subtítulo descriptivo (en rojo, mayúsculas) en la parte inferior.

### F. Gráfico de Barras Principal (Bar Chart)
Ocupa el espacio inferior derecho, siendo el elemento más grande para análisis de datos históricos.
*   **Ejes limpios:** Líneas de cuadrícula (grid lines) muy suaves solo horizontales.
*   **Barras agrupadas:** Dos barras por mes (Ej. Verde y Roja) para comparar dos métricas a lo largo del tiempo.
*   **Leyenda:** En la parte superior del gráfico, indicando claramente qué representa cada color.

---

## 4. Tipografía y Estilo

*   **Fuentes:** Se utiliza una fuente Sans-Serif moderna (como Roboto, Open Sans o Inter).
*   **Pesos (Font Weight):** Uso intensivo del peso en negrita (Bold o ExtraBold) para los números/montos y títulos importantes.
*   **Alineación:** La mayoría del texto en las tarjetas y encabezados está centrado (Text-Center) para dar una apariencia equilibrada.
*   **Bordes:** Bordes sutiles (1px sólido gris claro) y un radio de borde pequeño (border-radius: 4px a 8px) en todas las tarjetas para suavizar la apariencia.

## Resumen para Replicar:
Si vas a construir esto en otro sistema, tus tareas front-end serían:
1. Configurar un **Grid CSS** o **Flexbox** para las columnas principales.
2. Crear un componente `Card` base con fondo blanco, padding interior y borde redondeado.
3. Integrar una librería de gráficos (como Chart.js, ApexCharts o Recharts) para los semicírculos, el gráfico de líneas suavizado y el gráfico de barras.
4. Aplicar variables CSS para los colores principales (Rojo Peligro, Verde Éxito, Gris Fondo).

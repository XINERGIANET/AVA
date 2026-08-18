# Documentación y Resumen de Cambios: Módulo de Planificación de Compras de Combustible (Actualizado al 18 de Agosto de 2026)

## 📌 Contexto y Objetivo
Se implementó en el sistema **AVA** el módulo integral de **Planificación de Compras de Combustible**, diseñado para coordinar el abastecimiento entre los Administradores de Sede y la Gerencia General (Usuario Maestro), permitiendo monitorear existencias físicas en tanques, fondos líquidos disponibles (bóveda, cajas y métodos de pago), aprobación/ajuste gerencial de galones, generación de órdenes en PDF, cálculo de eficacia de compra real y registro de justificaciones ante compras parciales.

---

## 🛠️ Componentes Desarrollados

### 1. Base de Datos & Migraciones
* **Archivo de Migración**: `database/migrations/2026_08_17_000001_create_purchase_plans_tables.php`
* **Tablas Creadas**:
  * **`purchase_plans`**:
    * `id`: Identificador único de la solicitud de planificación.
    * `location_id`: Sede solicitante vinculada a `locations`.
    * `user_id`: Usuario administrador que genera la solicitud.
    * `scheduled_date`: Fecha proyectada en que se requiere la compra/descarga.
    * `available_money`: Dinero total disponible reportado (Bóveda + Todos los métodos de pago).
    * `status`: Estado del pedido (`pending`, `approved`, `completed`, `partially_completed`, `rejected`).
    * `reviewed_by`: Usuario Gerente/Master que evalúa la solicitud.
    * `reviewed_at`: Timestamp de la aprobación o rechazo.
    * `notes`: Observaciones y requerimientos del administrador de sede.
    * `manager_notes`: Observaciones, órdenes o motivos de ajuste dictados por Gerencia.
    * `compliance_percentage`: Porcentaje de eficacia real de compra calculado automáticamente.
    * `justification_notes`: Justificación obligatoria en caso de atención parcial (< 100%).
    * `deleted`: Flag de borrado lógico (0 activo, 1 anulado).
  * **`purchase_plan_details`**:
    * `purchase_plan_id`: Relación con la cabecera `purchase_plans`.
    * `product_id`: Tipo de combustible (Diesel B5, Gasohol Regular, Gasohol Premium, GLP, etc.).
    * `tank_id`: Tanque asignado en la sede (opcional).
    * `current_stock`: Stock físico en galones existente al momento de solicitar.
    * `requested_quantity`: Galones solicitados inicialmente por la sede.
    * `approved_quantity`: Galones autorizados por Gerencia (base de cálculo de la meta).
    * `purchased_quantity`: Galones efectivamente comprados y descargados.
    * `unit_price_estimate`: Precio estimado unitario por galón.
    * `estimated_total`: Monto total presupuestado.

---

### 2. Modelos Eloquent & Lógica de Negocio
* **`app/Models/PurchasePlan.php`**:
  * Relaciones: `location()`, `user()`, `reviewer()`, `details()`.
  * Accesores:
    * `total_requested_gallons`: Suma de galones solicitados.
    * `total_approved_gallons`: Suma de galones autorizados por gerencia.
    * `total_purchased_gallons`: Suma de galones comprados en la realidad.
    * `effective_compliance`: Cálculo global de la eficacia tomando como base de cálculo los **galones autorizados** por Gerencia.
* **`app/Models/PurchasePlanDetail.php`**:
  * Relaciones: `purchase_plan()`, `product()`, `tank()`.
  * Accesores:
    * `compliance_rate`: Porcentaje individual de cumplimiento por combustible $(\text{Galones Comprados} / \text{Galones Autorizados}) \times 100$.

---

### 3. Controlador Backend (`PurchasePlanController.php`)
* **`index(Request $request)`**:
  * **Filtrado Dinámico por Sede Activa**: Vinculado automáticamente a la sede elegida en el selector de la cabecera del sistema (`auth()->user()->location_id`), impidiendo que se mezclen registros de sedes distintas.
  * **Tarjetas de KPIs**:
    * *Total Solicitudes* y *Pendientes*.
    * *Tasa de Aprobación de Gerencia (%)*.
    * *Eficacia de Compra Promedio (%)*.
    * *Rechazadas*.
* **`create(Request $request)` / `getSedeInfo(Request $request)`**:
  * Detección y carga en tiempo real por AJAX del stock de cada tanque.
  * **Desglose Financiero Integral de la Sede**:
    1. **En Bóveda**: Saldo acumulado en la bóveda de la sede.
    2. **En Cajas (Efectivo Turnos/General)**: Saldo físico en efectivo en turnos activos de islas y caja general.
    3. **Por Métodos de Pago**: Desglose de cada método configurado en la sede (*Efectivo, Tarjeta, Yape, Plin, Transferencias, BBVA, BCP, etc.*).
    4. **Dinero Total Disponible**: Suma consolidada de la Bóveda + Todos los Métodos de Pago.
* **`store(Request $request)`**: Registro seguro bajo transacción con validaciones.
* **`show($id)`**: Vista completa de trazabilidad, stock inicial, cantidades aprobadas y justificaciones.
* **`review(Request $request, $id)`**: Modal para usuario Gerente/Master que permite **Aprobar**, **Ajustar galones autorizados** (ej. si solicitan 100 Gls pero se aprueban 50 Gls) o **Rechazar**.
* **`updatePurchased(Request $request, $id)`**: Registro de compras reales y cálculo inmediato del porcentaje de eficacia contra la meta autorizada, exigiendo justificación si el cumplimiento es $< 100\%$.
* **`pdf($id)`**: Generación de orden y reporte formal con `DomPDF`.

---

### 4. Vistas Blade & Experiencia de Usuario
* **`resources/views/purchase_plans/index.blade.php`**:
  * Bandeja interactiva con KPIs ejecutivos, barras de progreso de eficacia y modales de revisión gerencial y registro de compras situados fuera de la tabla para garantizar un renderizado HTML impecable.
* **`resources/views/purchase_plans/create.blade.php`**:
  * Campos **Sede Solicitante** y **Dinero Total Disponible** en **Modo Solo Lectura (`readonly`)** para garantizar que los fondos provengan estrictamente de la suma en vivo de la base de datos (Bóveda + Cajas + Métodos de Pago) sin manipulación manual.
  * Tarjeta de desglose financiero en vivo y tabla dinámica de requerimiento de combustibles con cálculo automático de totales.
* **`resources/views/purchase_plans/show.blade.php`**:
  * Ficha de auditoría con comparativa visual (Solicitado vs. Autorizado vs. Comprado).
* **`resources/views/purchase_plans/pdf.blade.php`**:
  * Documento formal con membrete de la empresa, datos de sede, dinero disponible, detalle de tanques/combustibles y líneas de firma para el Administrador de Sede y Gerencia.

---

### 5. Rutas Registradas (`routes/web.php`)
* `GET  /purchase-plans`: Bandeja principal (`purchase_plans.index`).
* `GET  /purchase-plans/create`: Formulario de solicitud (`purchase_plans.create`).
* `POST /purchase-plans`: Almacenamiento (`purchase_plans.store`).
* `GET  /purchase-plans/sede-info`: Endpoint AJAX para carga de datos de sede (`purchase_plans.sede_info`).
* `GET  /purchase-plans/{id}`: Detalle de la solicitud (`purchase_plans.show`).
* `GET  /purchase-plans/{id}/pdf`: Descarga de reporte PDF (`purchase_plans.pdf`).
* `POST /purchase-plans/{id}/review`: Evaluación de Gerencia (`purchase_plans.review`).
* `POST /purchase-plans/{id}/purchased`: Actualización de compra real (`purchase_plans.purchased`).

---

### 6. Acceso en el Menú Principal
* Se encuentra integrado en el Sidebar en:  
  **Abastecimiento $\rightarrow$ Planificación Compras**

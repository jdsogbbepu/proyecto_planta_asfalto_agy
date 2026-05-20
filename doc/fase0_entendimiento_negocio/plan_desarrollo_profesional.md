# Plan de Desarrollo Web Profesional (9 Fases)
## Proyecto: Sistema de Control de Materiales e Inventarios (Asphalt-AGY)
**Cliente/Área:** Planta de Asfalto - Gobierno Autónomo Municipal de El Alto (GAMEA)  
**Autor:** Estudiante de Ingeniería de Sistemas (UPEA)

---

## █ Fase 0 – Entendimiento del Negocio
* **Problema:** La auditoría interna UAI/ACR/001/2025 del GAMEA reportó un control inadecuado de existencias de materiales mediante planillas de Excel informales, las cuales carecen de control cronológico de ingresos, salidas y saldos. Se exige la implementación de un sistema formal bajo la normativa del Decreto Supremo N° 181 (Art. 136 y 137) y la Norma de Control Interno Gubernamental 2411.
* **Usuarios y Roles del Sistema:**
  1. **Administrador ("Ajustador del Sistema"):** Encargado de la configuración técnica, alta/baja de usuarios, CRUD de materiales base (catálogo general) y funcionarios oficiales.
  2. **Operador (Ingeniero Civil, Industrial o Economista):** Usuario de negocio principal. Tiene permisos para CRUD de proyectos, registro de transacciones (ingresos de compras asociadas a proyectos y egresos/consumos PEPS) y generación de reportes. No puede modificar materiales ni funcionarios para proteger la integridad de futuras auditorías.
  3. **Visor (Supervisor o Auditor):** Rol de visualización y control. Puede consultar y exportar/imprimir reportes del estado de proyectos y consumos de forma anual o mensual.
* **Top 3 Funcionalidades:**
  1. **Registro de Adquisiciones (Ingresos):** Registro de ingresos de materiales asociados a una ODC (Orden de Compra) y a un proyecto de destino específico (sin campos monetarios de precio/costo).
  2. **Despacho con Algoritmo PEPS Automático (Backend):** Cálculo automático en el backend que consume de los lotes más antiguos disponibles al registrar un egreso de obra, asegurando la inmutabilidad y la norma PEPS física.
  3. **Reportes y Kardex Físico PEPS:** Emisión del Kardex PEPS oficial por material (Entrada, Salida y Saldo en cantidad física) e impresión de consolidados de consumos por proyecto, rango de fechas, mes o año.
* **Restricciones Técnicas:**
  * Despliegue en red Intranet local offline (dentro del servidor PC de la planta con XAMPP).
  * Seguridad a través de autenticación encriptada por el Administrador.

---

## █ Fase 1 – Requisitos
* **Requisitos Funcionales (RF):**
  * **RF-01 (Seguridad y Acceso):** Inicio de sesión por roles (Administrador, Operador, Visor). Cierre de sesión y protección de rutas.
  * **RF-02 (CRUD Usuarios):** El Administrador crea, edita y desactiva cuentas.
  * **RF-03 (CRUD Catálogo de Materiales):** El Administrador gestiona el catálogo maestro de materiales (Nombre, descripción, unidad de medida, stock mínimo).
  * **RF-04 (CRUD Proyectos):** El Operador registra proyectos (Nombre de la obra, ubicación, encargado, fecha de inicio/fin, estado).
  * **RF-05 (Registro de Ingreso/Compra):** El Operador registra ingresos de materiales cargando la cantidad adquirida, ODC, proveedor, proyecto al que se asigna y fecha.
  * **RF-06 (Registro de Egreso/Consumo PEPS):** El Operador registra la salida de materiales ingresando la cantidad, el funcionario solicitante, el proyecto de destino y el uso. El backend calcula y descuenta automáticamente de los lotes activos según PEPS (FIFO) de forma física.
  * **RF-07 (Generación de Reportes):** Todos los roles pueden consultar e imprimir reportes de stock, balances de proyectos y Kardex Físico PEPS (por mes, año o rango de fechas).
* **Requisitos No Funcionales (RNF):**
  * **RNF-01 (Seguridad):** Encriptación de contraseñas con el algoritmo Bcrypt.
  * **RNF-02 (Usabilidad/Aesthetics):** Interfaz premium y responsiva adaptada a dispositivos de planta utilizando Tailwind CSS y diseño semi-oscuro industrial.
  * **RNF-03 (Despliegue Offline):** El sistema debe funcionar al 100% sin conexión a internet en la Intranet local.
  * **RNF-04 (Consistencia de Datos):** Transacciones de base de datos seguras (`BEGIN TRANSACTION` y `COMMIT/ROLLBACK`) para evitar pérdidas o inconsistencias al procesar salidas que afecten a múltiples lotes de ingreso.

---

## █ Fase 2 – Diseño de la Solución
* **2.1 Arquitectura de Software:**
  * Patrón: Arquitectura Monolítica Híbrida usando **Laravel 12 + Inertia.js + Vue.js 3 (Composition API / Setup)**. Esto nos permite tener la robustez y seguridad del backend en Laravel con la interactividad dinámica y fluidez de una Single Page Application (SPA) en Vue.js, todo compilado bajo Vite.
* **2.2 Base de Datos:**
  * Motor: MySQL (a través de phpMyAdmin de XAMPP) con estructura relacional limpia y posibilidad de migración rápida a PostgreSQL.
  * Tablas principales:
    * `users` (id, name, username, password, role, active)
    * `unidad_medida` (id, nombre, abreviacion)
    * `material` (id, nombre, descripcion, id_medida, stock_minimo)
    * `proveedor` (id, razon_social, nit, telefono, direccion)
    * `proyecto` (id, nombre, ubicacion, encargado, fecha_inicio, fecha_fin, estado)
    * `funcionario` (id, nombre, cargo, area, activo)
    * `ingreso` (id, nro_ticket, odc, id_proveedor, id_usuario, fecha_adquirida, observaciones)
    * `detalle_ingreso` (id, id_ingreso, id_material, cantidad_adquirida, cantidad_actual_lote) *(cantidad_actual_lote es el saldo disponible en el lote para PEPS)*.
    * `salida` (id, id_funcionario, id_proyecto, id_usuario, uso, fecha_salida)
    * `detalle_salida` (id, id_salida, id_detalle_ingreso, cantidad_salida)
* **2.3 Interfaz de Usuario (UI/UX):**
  * Paleta de colores semi-oscura industrial con tonalidades HSL y detalles naranja/amarillo asfalto.
  * Dashboards interactivos con gráficos en el frontend (usando Chart.js o similar) y semáforos de stock crítico.
* **2.4 Modelado Dinámico:**
  * Diagramas de actividades que explican cómo el backend recorre los lotes de `detalle_ingreso` en orden ascendente (`id_detalle_ingreso ASC`) para descontar cantidades de forma secuencial.

---

## █ Fase 3 – Planificación del Proyecto
* **Metodología:** Kanban adaptado con sprints rápidos para asegurar entregas periódicas.
* **Backlog del Producto (Sprints):**
  * **Sprint 1 (Base y Catálogos):** Instalación de Laravel 12 + Inertia + Vue 3, base de datos con migraciones, autenticación y CRUD de usuarios, materiales (catálogo), proveedores y funcionarios.
  * **Sprint 2 (Proyectos e Ingresos):** CRUD de proyectos y módulo de registro de ingresos de material vinculados a proyectos con asignación de ODC y lote inicial.
  * **Sprint 3 (Egresos PEPS y Reportes):** Desarrollo del algoritmo PEPS automático en el backend, vistas de consumos de proyectos, y reportes descargables/imprimibles de Kardex Físico Valorado.

---

## █ Fase 4 – Stack Tecnológico (Elegido y Validado)
* **Backend:** Laravel 12 (PHP)
* **Frontend:** Vue.js 3 (Composition API / Script Setup) vía Inertia.js
* **CSS Framework:** Tailwind CSS
* **Base de Datos:** MySQL (con opción a PostgreSQL en fase de producción)
* **Gestor de Paquetes y Compilación:** Composer, NPM y Vite
* **Control de Versiones:** Git (GitHub) con commits semánticos y ramas ordenadas (`main`, `develop`, `feature/*`)

---

## █ Fase 5 – Documentación Técnica y de Usuario
* Wiki técnica interactiva dentro de la carpeta `doc/` en archivos Markdown.
* Manuales de instalación para XAMPP y manuales de operador con capturas de pantalla integradas.

---

## █ Fase 6 – Desarrollo y Pruebas
* **Pruebas Unitarias:** Validación del enrutamiento y del algoritmo PEPS.
* **Pruebas de Integración:** Registro simulado de compras y consumos, asegurando la consistencia transaccional y que los saldos de lotes y el Kardex muestren cálculos exactos.

---

## █ Fase 7 – Despliegue
* Servidor local Apache y MySQL en XAMPP.
* Configuración de variables de entorno `.env` en producción.
* Configuración de copias de seguridad de la base de datos (Dump SQL).

---

## █ Fase 8 – Cierre y Presentación Profesional
* Armado del informe de pasantía para la UPEA y preparación de la defensa.

# Backlog de Producto y Planificación de Sprints (Fase 3)
## Proyecto: Sistema de Control de Materiales e Inventarios (Asphalt-AGY)

Este documento detalla las Historias de Usuario (US), la estimación de esfuerzo en Puntos de Historia (SP) y la organización del desarrollo en 3 Sprints de trabajo ágil.

---

## 1. Definición de Hecho (Definition of Done - DoD)
Para considerar una Historia de Usuario (US) como finalizada, debe cumplir con los siguientes criterios:
1. El código no contiene advertencias de compilación de Vite o PHP.
2. La interfaz de usuario es responsiva, cumple con la paleta de colores oscura industrial y está maquetada con Tailwind CSS.
3. Se han implementado las validaciones del lado del servidor (Laravel Request Validation) y del cliente (Vue HTML5/JS validation).
4. El código se encuentra integrado en la rama `develop` de Git.
5. Se han actualizado las especificaciones técnicas correspondientes en la Wiki del proyecto.

---

## 2. Pila de Producto (Product Backlog)

### Sprint 1: Base de la Aplicación y Mantenimiento de Catálogos (Fase de Estructura)
*Enfoque: Inicialización del framework, seguridad, control de usuarios y catálogos maestros.*
* **US-01: Control de Acceso y Sesión (8 SP)**
  * *Como* funcionario de la planta, *quiero* iniciar y cerrar sesión mediante mis credenciales *para* resguardar la información de auditoría del sistema.
  * *Criterios de Aceptación:* Autenticación Bcrypt encriptada, redirección automática según rol al dashboard, expiración de sesión por inactividad.
* **US-02: Administración de Usuarios (Administrador) (5 SP)**
  * *Como* Administrador, *quiero* crear, editar y desactivar usuarios *para* controlar quién accede al sistema y con qué privilegios.
  * *Criterios de Aceptación:* CRUD completo en interfaz Vue, validación de nombre de usuario único, imposibilidad de auto-eliminarse.
* **US-03: Catálogo General de Materiales (Administrador) (5 SP)**
  * *Como* Administrador, *quiero* gestionar la lista de materiales e indicar su stock mínimo de seguridad *para* que los operadores puedan registrar movimientos.
  * *Criterios de Aceptación:* CRUD de materiales y vinculación a unidades de medida, alerta visual inmediata si el stock actual está por debajo del stock mínimo.
* **US-04: Catálogo de Funcionarios y Proveedores (5 SP)**
  * *Como* Operador/Administrador, *quiero* gestionar la lista de funcionarios autorizados y de empresas proveedoras *para* asociarlos a los ingresos y egresos.
  * *Criterios de Aceptación:* CRUD de funcionarios con estado (activo/inactivo) y CRUD de proveedores con validación de NIT.

---

### Sprint 2: Control de Proyectos y Registro Transaccional (Fase de Operación)
*Enfoque: Gestión de obras y entrada/salida de stock físico asignado.*
* **US-05: Control de Proyectos (Obras viales) (5 SP)**
  * *Como* Operador, *quiero* registrar y controlar los proyectos de pavimentación *para* conocer las obras vigentes que demandan materiales.
  * *Criterios de Aceptación:* Registro con rango de fechas, estados del proyecto (activo, finalizado, pausado).
* **US-06: Módulo de Registro de Ingresos de Materiales (Balanza) (8 SP)**
  * *Como* Operador, *quiero* registrar la entrada física de materiales asociándolos a una ODC y a un proyecto específico *para* que ingresen al inventario disponible.
  * *Criterios de Aceptación:* Formulario de ingreso de ticket de balanza, ODC, material, cantidad y proyecto. Creación automática del lote en `detalle_ingresos` con su saldo actual inicializado.
* **US-07: Módulo de Despacho PEPS Automático (Egresos) (13 SP)**
  * *Como* Operador, *quiero* registrar la salida de material para una obra *para* que el sistema descuente el stock del proyecto siguiendo el método PEPS de forma automática.
  * *Criterios de Aceptación:* Formulario de egreso. El backend debe ejecutar el descuento secuencial por lotes en orden cronológico (`ASC`). Transaccionalidad asegurada (`DB::transaction`). Error inmediato si el stock total del proyecto es insuficiente.

---

### Sprint 3: Visualización, Reportes y Auditoría (Fase de Control)
*Enfoque: Paneles interactivos, semaforización de alertas y generación de PDF para impresión.*
* **US-08: Dashboard y Semáforos de Alerta (5 SP)**
  * *Como* Operador/Administrador, *quiero* ver un resumen visual del stock de planta y proyectos *para* tomar decisiones rápidas de reabastecimiento.
  * *Criterios de Aceptación:* Tarjetas de stock consolidado, semaforización de estado (Normal, Crítico, Agotado), listado rápido de últimos movimientos.
* **US-09: Generación de Kardex Físico PEPS por Material (8 SP)**
  * *Como* Operador/Visor, *quiero* ver el historial cronológico de entradas, salidas y saldos de un material *para* responder a los requerimientos de auditorías internas del GAMEA.
  * *Criterios de Aceptación:* Filtro obligatorio por material, filtros opcionales por proyecto y rango de fechas. Tabla dinámica que expone el flujo cronológico exacto de stock.
* **US-10: Impresión y Exportación a PDF (5 SP)**
  * *Como* Operador/Visor, *quiero* imprimir los reportes de Kardex y consumos de obras *para* presentarlos como descargo en los informes físicos municipales.
  * *Criterios de Aceptación:* Integración de hojas de estilo `@media print` para generar archivos limpios PDF listos para firmar y archivar desde el navegador, sin necesidad de librerías externas pesadas que dificulten el modo offline.

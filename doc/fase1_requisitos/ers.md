# Especificación de Requisitos de Software (ERS)
## Proyecto: Sistema de Control de Materiales e Inventarios (Asphalt-AGY)

Este documento detalla los requisitos funcionales y no funcionales del sistema, y define el modelado de negocio mediante casos de uso para guiar el desarrollo de la aplicación.

---

## 1. Requisitos Funcionales (RF)

### 1.1. Gestión de Seguridad y Acceso
* **RF-01 (Autenticación):** El sistema permitirá el ingreso de usuarios registrados mediante su nombre de usuario (`username`) y contraseña.
* **RF-02 (Control de Acceso por Roles):** El sistema limitará el acceso a las vistas y acciones según el rol asignado al usuario:
  * **Administrador:** Acceso completo a configuraciones, usuarios, materiales y funcionarios.
  * **Operador:** Acceso a la gestión de proyectos, registro de ingresos/salidas de almacén y reportes. Sin permisos para alterar el catálogo de materiales ni funcionarios.
  * **Visor:** Acceso de sólo lectura para consultar reportes y el stock actual.
* **RF-03 (Administración de Cuentas):** El Administrador podrá crear, editar datos y desactivar cuentas de usuarios. No existirá autoregistro de usuarios.

### 1.2. Módulo de Catálogos (Paramétricas)
* **RF-04 (CRUD de Materiales):** El Administrador gestionará el catálogo de materiales (Nombre, descripción, unidad de medida: KG, M3, etc., y stock mínimo de seguridad).
* **RF-05 (CRUD de Funcionarios):** El Administrador gestionará el registro de los trabajadores y supervisores (Nombre, cargo, área).
* **RF-06 (CRUD de Proveedores):** El Operador podrá gestionar los proveedores que entregan material a la planta.

### 1.3. Módulo de Operaciones (Transaccional)
* **RF-07 (CRUD de Proyectos):** El Operador podrá gestionar los proyectos de pavimentación/mantenimiento (Nombre de la obra, ubicación, encargado, fecha de inicio, fecha de fin, estado).
* **RF-08 (Registro de Ingreso de Materiales):** El Operador registrará la llegada de materias primas vinculadas a un proyecto. Se capturará: Nro. de Ticket de balanza física, ODC (Orden de Compra), Proveedor, Material, Cantidad Física ingresada, Fecha de adquisición y observaciones.
* **RF-09 (Registro de Salida/Consumo PEPS):** El Operador registrará los despachos de material para obras. Se capturará: Proyecto de destino, Material, Cantidad física a descontar, Funcionario solicitante y uso específico. El backend procesará automáticamente el descuento según el orden de entrada (PEPS/FIFO).

### 1.4. Módulo de Consultas y Reportes
* **RF-10 (Dashboard Informativo):** Visualización en tiempo real del stock total consolidado de materiales y alertas mediante semaforización (Normal, Crítico, Sin Stock).
* **RF-11 (Kardex Físico PEPS):** Reporte histórico de movimientos de un material (Entradas, Salidas y Saldos con fechas y documentos asociados) filtrado por rango de fechas, mes o año.
* **RF-12 (Consumo por Proyecto):** Consolidado de los materiales utilizados en una obra específica durante un periodo de tiempo.
* **RF-13 (Exportación e Impresión):** Todos los reportes podrán ser exportados a formato PDF optimizado para impresión.

---

## 2. Requisitos No Funcionales (RNF)

* **RNF-01 (Seguridad de Datos):** Las contraseñas almacenadas en la base de datos MySQL se encriptarán utilizando hashing Bcrypt (nativo en Laravel).
* **RNF-02 (Rendimiento):** El cálculo del algoritmo PEPS y la respuesta de consultas de Kardex deben resolverse en menos de 2 segundos para transacciones estándar.
* **RNF-03 (Operación Offline/Intranet):** La aplicación no debe requerir conexión a internet para funcionar, ejecutándose en la red local de la Planta de Asfalto sobre Apache/MySQL en XAMPP.
* **RNF-04 (Diseño Premium e Industrial):** La interfaz gráfica implementará Tailwind CSS con una estética oscura premium, limpia, fluida y con micro-animaciones en botones e indicadores de stock.

---

## 3. Diagrama de Casos de Uso UML

```mermaid
usecaseDiagram
    actor Administrador as "Administrador"
    actor Operador as "Operador (Ingeniero)"
    actor Visor as "Visor (Inspector)"

    %% Casos de Uso del Administrador
    Administrador --> (Gestionar Usuarios)
    Administrador --> (Gestionar Materiales)
    Administrador --> (Gestionar Funcionarios)

    %% Casos de Uso Comunes
    Administrador --> (Consultar Reportes y Kardex)
    Operador --> (Consultar Reportes y Kardex)
    Visor --> (Consultar Reportes y Kardex)

    %% Casos de Uso del Operador
    Operador --> (Gestionar Proyectos)
    Operador --> (Gestionar Proveedores)
    Operador --> (Registrar Ingreso de Material)
    Operador --> (Registrar Salida PEPS)
```

---

## 4. Especificación Detallada de Casos de Uso Críticos

### Caso de Uso: Registrar Salida de Material (PEPS Automático)
| Elemento | Descripción |
| :--- | :--- |
| **Actor Principal** | Operador (Ingeniero) |
| **Precondiciones** | El Operador está autenticado en el sistema. Hay proyectos registrados activos y stock físico disponible del material. |
| **Flujo Básico** | 1. El Operador ingresa al módulo de "Egresos".<br>2. Selecciona el proyecto de destino, el material, la cantidad a retirar, el funcionario receptor y detalla el uso.<br>3. Presiona "Registrar Salida".<br>4. El sistema inicia una transacción de base de datos.<br>5. El sistema consulta los lotes de ingreso (`detalle_ingreso`) del material en orden cronológico (`ASC`).<br>6. El sistema descuenta la cantidad lote por lote actualizando `cantidad_actual_lote`. (Si un lote se agota, pasa al siguiente más antiguo).<br>7. El sistema registra el egreso en la tabla `salida` y los detalles vinculados a los lotes consumidos en `detalle_salida`.<br>8. El sistema confirma la transacción (`COMMIT`) y muestra mensaje de éxito. |
| **Flujo Alternativo (Stock Insuficiente)** | 4a. Si la cantidad a retirar supera el stock total acumulado de todos los lotes del material:<br>&nbsp;&nbsp;&nbsp;&nbsp;i. El sistema cancela la transacción (`ROLLBACK`).<br>&nbsp;&nbsp;&nbsp;&nbsp;ii. Muestra un mensaje de error alertando sobre el stock insuficiente.<br>&nbsp;&nbsp;&nbsp;&nbsp;iii. Retorna al formulario con los datos cargados para corrección. |

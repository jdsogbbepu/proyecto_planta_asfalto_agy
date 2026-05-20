# CAPÍTULO 3: INGENIERÍA DE REQUISITOS

La definición precisa de los requisitos de software constituye la base fundamental para el desarrollo exitoso del sistema Asphalt-AGY. En este capítulo, se detallan las necesidades operativas de la Planta de Asfalto del Gobierno Autónomo Municipal de El Alto (GAMEA), clasificadas en requisitos funcionales y no funcionales. Adicionalmente, se presenta el modelado del negocio a través de diagramas UML de Casos de Uso y la especificación detallada de los escenarios críticos de control Físico PEPS.

---

## 3.1. Requisitos Funcionales (RF)

Los requisitos funcionales definen los servicios, procesos y comportamiento que el sistema Asphalt-AGY debe ejecutar de manera obligatoria para satisfacer las necesidades del personal de la Planta de Asfalto y cumplir con las recomendaciones de la auditoría UAI/ACR/001/2025.

A continuación, se presenta la especificación detallada de los requisitos funcionales organizados por módulos:

### 3.1.1. Módulo de Seguridad y Control de Acceso
* **RF-01 (Autenticación de Usuarios):**
  * *Descripción:* El sistema debe permitir el acceso exclusivo a los usuarios registrados mediante la verificación de sus credenciales de seguridad (`username` y contraseña).
  * *Actor:* Administrador, Operador, Visor.
* **RF-02 (Control de Acceso basado en Roles):**
  * *Descripción:* El sistema debe restringir el acceso a los módulos, pantallas y acciones de base de datos según el rol asignado al perfil del usuario autenticado:
    * **Administrador (Jefe de Planta):** Acceso irrestricto a todos los módulos, incluyendo la configuración general de cuentas de usuario, catálogo de materiales y registro de funcionarios.
    * **Operador (Operador de Balanza / Almacenero):** Permiso para gestionar proyectos viales, registrar transacciones de ingresos de balanza, procesar egresos PEPS y visualizar reportes. Carece de autorización para dar de alta o modificar catálogos iniciales de materiales o funcionarios.
    * **Visor (Residente de Obra / Supervisor):** Acceso limitado en modo de solo lectura para consultar existencias físicas en el dashboard y emitir reportes consolidados.
  * *Actor:* Sistema.
* **RF-03 (Administración de Cuentas de Usuario):**
  * *Descripción:* El sistema debe permitir al Administrador la creación, edición de perfiles y desactivación temporal o definitiva de cuentas de usuario. No se permitirá el autoregistro público de usuarios para garantizar la seguridad perimetral de la intranet.
  * *Actor:* Administrador.

### 3.1.2. Módulo de Catálogos (Paramétricas del Sistema)
* **RF-04 (Gestión del Catálogo de Materiales):**
  * *Descripción:* El sistema debe permitir al Administrador realizar el alta, lectura, edición y baja lógica (CRUD) de los materiales manejados en planta. Cada registro de material debe capturar el nombre, descripción técnica, unidad de medida oficial (Toneladas, Metros Cúbicos, Kilogramos, Litros, Unidades) y el límite de stock mínimo de seguridad.
  * *Actor:* Administrador.
* **RF-05 (Gestión del Registro de Funcionarios):**
  * *Descripción:* El sistema debe permitir al Administrador gestionar el catálogo de los funcionarios y técnicos de planta autorizados para retirar material. Se registrará el nombre completo, el cargo ocupado y la sección o área correspondiente dentro de la Dirección de Obras Municipales.
  * *Actor:* Administrador.
* **RF-06 (Gestión del Registro de Proveedores):**
  * *Descripción:* El sistema debe permitir al Operador realizar el CRUD de los proveedores que suministran insumos a la planta, almacenando la Razón Social, el NIT (Número de Identificación Tributaria), número telefónico de contacto y dirección física.
  * *Actor:* Operador.

### 3.1.3. Módulo de Operaciones (Transaccional)
* **RF-07 (Gestión de Proyectos Viales):**
  * *Descripción:* El sistema debe permitir al Operador registrar y actualizar los proyectos de pavimentación, bacheo y mejoramiento vial ejecutados por el GAMEA. Se almacenará el nombre oficial de la obra, ubicación geográfica, ingeniero supervisor a cargo, fecha programada de inicio y finalización, y el estado actual del proyecto (Activo, Pausado, Concluido).
  * *Actor:* Operador.
* **RF-08 (Registro de Ingresos de Materiales - Balanza):**
  * *Descripción:* El sistema debe permitir al Operador registrar formalmente el ingreso de camiones con materias primas destinadas a la producción. El formulario transaccional debe recopilar: número de Ticket de balanza física, número de Orden de Compra (ODC), selección del proveedor, selección del material, fecha y hora de adquisición, y observaciones adicionales. El sistema registrará el Peso Bruto (camión cargado) y el Peso Tara (camión vacío), calculando automáticamente la cantidad de Peso Neto ingresada al stock físico de planta.
  * *Actor:* Operador.
* **RF-09 (Registro de Egresos de Materiales - PEPS):**
  * *Descripción:* El sistema debe permitir al Operador procesar los despachos de insumos destinados a obras. Al registrar el egreso, el operador ingresará el proyecto de destino, el material solicitado, la cantidad física a descontar, el funcionario solicitante y la descripción del uso específico. El backend procesará de forma automática y transparente el descuento en los lotes de ingreso siguiendo estrictamente el orden cronológico (PEPS/FIFO), dividiendo el egreso entre los lotes necesarios hasta cubrir la cantidad requerida.
  * *Actor:* Operador.

### 3.1.4. Módulo de Consultas y Reportes
* **RF-10 (Dashboard Informativo en Tiempo Real):**
  * *Descripción:* El sistema debe mostrar en su pantalla principal un resumen visual del stock total consolidado por material de planta. Incorporará alertas dinámicas mediante semaforización de stock crítico (Verde: Stock Normal, Amarillo: Cercano al límite de seguridad, Rojo: Stock Crítico / Requiere reposición inmediata).
  * *Actor:* Administrador, Operador, Visor.
* **RF-11 (Generación de Kardex Físico PEPS):**
  * *Descripción:* El sistema debe generar un reporte cronológico detallado de todos los movimientos físicos (Entradas, Salidas y Saldos de cantidades físicas) de un material seleccionado. El reporte debe permitir filtros por rangos específicos de fechas, meses o gestión anual.
  * *Actor:* Administrador, Operador, Visor.
* **RF-12 (Reporte de Consumo por Proyecto):**
  * *Descripción:* El sistema debe emitir un consolidado cuantitativo de todos los materiales y cantidades consumidas en una obra específica durante un periodo de tiempo determinado.
  * *Actor:* Administrador, Operador, Visor.
* **RF-13 (Exportación e Impresión de Reportes):**
  * *Descripción:* Todos los reportes generados por la aplicación deben ser exportables a formato PDF optimizado para su correspondiente impresión y firma física por los responsables de la planta.
  * *Actor:* Administrador, Operador, Visor.

---

## 3.2. Requisitos No Funcionales (RNF)

Los requisitos no funcionales definen los atributos de calidad, rendimiento, restricciones técnicas y usabilidad que debe poseer el sistema Asphalt-AGY para operar eficazmente en el entorno real de la Planta de Asfalto del GAMEA.

* **RNF-01 (Seguridad y Resguardo de Datos):**
  * *Descripción:* Las contraseñas de las cuentas de usuario registradas en la base de datos MySQL deben protegerse utilizando el algoritmo de cifrado irreversible unidireccional **Bcrypt** (estándar nativo de Laravel), evitando la filtración de credenciales en texto plano.
* **RNF-02 (Rendimiento y Eficiencia del Algoritmo PEPS):**
  * *Descripción:* El procesamiento lógico del backend para el descuento PEPS de lotes e ingresos, así como la consulta de saldos y la generación del Kardex Físico, no deben superar un tiempo de respuesta de **2 segundos** bajo condiciones normales de carga de datos en el servidor local.
* **RNF-03 (Independencia de Conexión a Internet - Operación Offline):**
  * *Descripción:* El sistema debe ser capaz de funcionar al 100% de su capacidad sin requerir de conexión a la red pública de internet. La arquitectura está diseñada para ejecutarse localmente como una Intranet sobre un servidor local Apache y base de datos MySQL integrados en el paquete XAMPP en el servidor de la Planta de Asfalto.
* **RNF-04 (Diseño de Interfaz Responsivo e Industrial):**
  * *Descripción:* La interfaz visual del sistema debe implementarse con Tailwind CSS utilizando una paleta de colores de estilo oscuro premium optimizada para entornos industriales de pantallas locales. Debe ser fluida, responsiva (adaptable a tabletas y ordenadores de escritorio) y proveer micro-animaciones dinámicas y sutiles en los indicadores de stock para mejorar la experiencia de usuario (UX) del personal operativo.

---

## 3.3. Diagrama de Casos de Uso UML

El diagrama de Casos de Uso describe las interacciones entre los diferentes actores de la Planta de Asfalto del GAMEA y las funcionalidades provistas por el sistema Asphalt-AGY, definiendo las fronteras de la aplicación.

```mermaid
usecaseDiagram
    actor Administrador as "Administrador (Jefe de Planta)"
    actor Operador as "Operador (Almacenero / Balanza)"
    actor Visor as "Visor (Inspector / Supervisor)"

    %% Casos de Uso del Administrador
    Administrador --> (Gestionar Usuarios)
    Administrador --> (Gestionar Materiales)
    Administrador --> (Gestionar Funcionarios)

    %% Casos de Uso Comunes de Consulta
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

## 3.4. Especificación Detallada de Casos de Uso Críticos

Para garantizar el correcto modelado de los flujos de información del sistema, se detalla a continuación el comportamiento del caso de uso de negocio más crítico: el registro de consumos físicos de materiales aplicando de forma automatizada la lógica del método PEPS.

### Caso de Uso: Registrar Salida de Material (PEPS Automático)
| Elemento | Descripción |
| :--- | :--- |
| **Identificador** | CU-09 |
| **Nombre del Caso de Uso** | Registrar Salida de Material (PEPS Automático) |
| **Actor Principal** | Operador (Almacenero / Balanza) |
| **Precondiciones** | 1. El Operador se encuentra autenticado en el sistema.<br>2. Existen materiales registrados con stock físico disponible mayor a cero.<br>3. Existen proyectos registrados en estado "Activo".<br>4. Existen funcionarios registrados autorizados. |
| **Postcondiciones** | El stock total consolidado del material disminuye en la cantidad solicitada. Los saldos de los lotes individuales (`cantidad_actual_lote`) se actualizan en orden cronológico de ingreso. Se registran las transacciones correspondientes en las tablas de egresos. |
| **Flujo Básico de Eventos** | 1. El Operador ingresa al módulo de "Egresos" en el menú principal del sistema.<br>2. El sistema muestra el formulario de egreso de materiales.<br>3. El Operador selecciona el **Proyecto de destino**, el **Material** a retirar, ingresa la **Cantidad Física** a descontar, selecciona el **Funcionario** solicitante y detalla el **Uso específico** en el campo de observaciones.<br>4. El Operador presiona el botón "Registrar Salida".<br>5. El sistema inicia una transacción de base de datos (`Transaction START`).<br>6. El sistema realiza una consulta a la tabla `DETALLE_INGRESO` recuperando los lotes del material ordenados de forma ascendente por `fecha_adquirida` e `id_detalle_ingreso` (orden cronológico) cuyo saldo físico sea mayor a cero.<br>7. El sistema ejecuta el algoritmo de descuento PEPS iterando sobre los lotes obtenidos:<br>&nbsp;&nbsp;&nbsp;&nbsp;a. Si la cantidad a retirar cabe dentro del saldo del lote más antiguo, descuenta la cantidad completa de este lote y actualiza su propiedad `saldo_lote` en la base de datos. Registra un detalle de salida vinculado a dicho lote.<br>&nbsp;&nbsp;&nbsp;&nbsp;b. Si la cantidad solicitada supera el saldo del lote más antiguo, agota el saldo del lote reduciéndolo a cero, registra un detalle de salida por la cantidad parcial descontada y repite el proceso deduciendo el saldo restante del siguiente lote cronológico disponible en la cola.<br>8. El sistema registra la cabecera de la transacción en la tabla `salida`.<br>9. El sistema confirma la transacción en la base de datos (`COMMIT`).<br>10. El sistema redirecciona al operador al listado de egresos, mostrando un mensaje visual en pantalla confirmando el éxito del registro. |
| **Flujo Alternativo (Stock Insuficiente)** | **7.1. Cantidad solicitada supera el stock consolidado del material:**<br>&nbsp;&nbsp;&nbsp;&nbsp;a. El sistema detecta que la suma total de saldos de todos los lotes disponibles del material seleccionado es inferior a la cantidad solicitada por el operador.<br>&nbsp;&nbsp;&nbsp;&nbsp;b. El sistema interrumpe el procesamiento del algoritmo.<br>&nbsp;&nbsp;&nbsp;&nbsp;c. El sistema ejecuta la cancelación de la transacción (`ROLLBACK`) previniendo cualquier cambio en la base de datos.<br>&nbsp;&nbsp;&nbsp;&nbsp;d. El sistema muestra un mensaje de error alertando sobre la insuficiencia de stock (mostrando la cantidad máxima disponible para retiro).<br>&nbsp;&nbsp;&nbsp;&nbsp;e. El sistema retorna al operador al formulario de registro manteniendo los datos previamente completados para su respectiva corrección. |
| **Flujo Alternativo (Datos Inválidos)** | **4.1. El formulario contiene datos obligatorios vacíos o formatos incorrectos:**<br>&nbsp;&nbsp;&nbsp;&nbsp;a. El sistema realiza la validación de los datos ingresados del lado del servidor.<br>&nbsp;&nbsp;&nbsp;&nbsp;b. Si se detectan campos requeridos vacíos (ej. cantidad en cero o vacía, falta de selección de proyecto, material o funcionario), el sistema cancela el envío.<br>&nbsp;&nbsp;&nbsp;&nbsp;c. Retorna al formulario resaltando con color rojo las advertencias de validación específicas en cada campo. |

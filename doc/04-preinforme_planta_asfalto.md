# PREINFORME: Sistema de Gestión "Planta de Asfalto"

**Versión:** 1.0
**Fecha:** 21 de mayo de 2026
**Preparado por:** Diego Vega
**Estado:** Borrador para revisión

---

## 1. CONTEXTO Y PROBLEMA A RESOLVER

La **Planta de Asfalto Municipal de El Alto** actualmente enfrenta dificultades en el control y seguimiento de sus materiales e insumos. El sistema anterior no cumplió con las expectativas de la organización, y el equipo ha recurrido al uso de planillas de Excel y documentos Word para registrar manualmente los proyectos, ingresos y despachos de materiales. Este proceso manual genera:

- **Pérdida de tiempo** en transcripción y duplicación de información.
- **Errores humanos** en el registro de cantidades y proyectos.
- **Dificultad para auditar** qué materiales se usaron, en qué proyecto, cuándo y por quién.
- **Falta de trazabilidad** entre el ingreso de materiales y su consumo real en cada proyecto.

Este nuevo sistema busca automatizar y centralizar toda esa información, eliminando el trabajo manual y proporcionando reportes precisos en formato digital.

---

## 2. OBJETIVO DEL SISTEMA

Desarrollar un **Sistema de Gestión Web** que permita:

- **Controlar el flujo completo de materiales** por cada proyecto: cuánto ingresó, cuánto se usó, cuánto quedó y en qué actividad se utilizó.
- **Asignar materiales a proyectos específicos**, llevando un registro detallado de cada transacción.
- **Gestionar los proyectos de construcción** de la planta, incluyendo los funcionarios y personal involucrado.
- **Generar reportes en PDF** por mes o por año, listos para imprimir y presentar.
- **Asignar roles y permisos** a los usuarios para que cada persona tenga acceso solo a lo que necesita según su cargo.

---

## 3. USUARIOS Y ROLES DEL SISTEMA

El sistema contempla tres roles principales más un administrador supreme:

| Rol | Descripción |
|---|---|
| **Administrador del Sistema** | Persona encargada del área de sistemas o software. Tiene control total: crear usuarios, definir roles, asignar permisos personalizados, suspender usuarios, y administrar todo el catálogo del sistema. |
| **Operador** | Personal de planta que registra proyectos, anota ingresos y despachos de materiales, y genera reportes. Por defecto puede crear, editar y eliminar proyectos y materiales. |
| **Visor** | Personal que solo necesita consultar información. Puede ver proyectos, revisar detalles y generar reportes PDF. No puede crear, editar ni eliminar nada. |

> **Nota:** El administrador puede **personalizar los permisos** de cada rol. Por ejemplo, a un operador se le puede quitar el permiso de eliminar proyectos, o a un visor se le puede restringir la impresión de reportes. Esto permite adaptarse a cualquier estructura organizacional que la planta tenga actualmente o en el futuro.

---

## 4. ENTIDADES Y MÓDULOS DEL SISTEMA

### 4.1 Materiales
Catálogo de todos los insumos que maneja la planta: asfalto, emulsiones, agregados, etc. Cada material tiene:
- Nombre, descripción detallada y código interno.
- Unidad de medida (kilogramos, litros, metros cúbicos, etc.).
- Stock mínimo de alerta.
- Historial de ingresos y salidas.

### 4.2 Proyectos
Cada proyecto de construcción municipal registrado en el sistema incluye:
- Nombre y ubicación de la obra.
- Encargado del proyecto.
- Fechas de inicio y fin.
- Estado: Activo, Pausado o Finalizado.
- **Materiales vinculados**: dentro de cada proyecto se registran los ingresos y consumos de materiales específicos.
- **Funcionarios asignados**: registro del personal involucrado.

### 4.3 Ingresos de Materiales (Entradas)
Cuando llega un lote de materiales a la planta, se registra:
- Número de ticket de báscula y orden de compra (opcional).
- Proveedor (quién entregó).
- Material, cantidad recibida y fecha de adquisición.
- Proyecto al que se asigna.
- Observaciones.

### 4.4 Despachos de Materiales (Salidas)
Cuando se dispatcha material a un proyecto, se registra:
- Funcionario que recibe el material.
- Proyecto destino.
- Detalle de qué materiales salieron y en qué cantidad.
- **Trazabilidad FIFO**: el sistema consume primero los lotes más antiguos (método PEPS - Primeras Entradas, Primeras Salidas), garantizando un control exacto de qué material se usó en cada proyecto.

### 4.5 Reportes y Kardex
Reporte valorizado de movimientos por proyecto y/o material, mostrando:
- Lotes existentes con saldos actuales.
- Movimientos cronológicos con saldo correrido.
- Posibilidad de filtrar por rango de fechas (mes, año).
- **Exportación a PDF** para impressão y archivo.

### 4.6 Gestión de Usuarios
- Crear, editar, suspender y eliminar usuarios.
- Definir roles personalizados con permisos granulares.
- Registro de actividad del usuario (quién creó o modificó qué).

### 4.7 Catálogos Auxiliares
- **Unidades de medida**: kg, l, m³, etc.
- **Proveedores**: datos de empresas proveedoras de materiales.
- **Funcionarios**: personal de la planta y sus cargos/áreas.

---

## 5. MÉTODO DE CONTROL DE INVENTARIO (FIFO/PEPS)

El sistema utiliza el método **PEPS (Primeras Entradas, Primeras Salidas)** para el control de materiales:

- Cada ingreso crea un "lote" con una cantidad específica y una fecha.
- Cuando se hace un despacho, el sistema consume primero el lote más antiguo.
- Si un lote no alcanza, se toma del siguiente más antiguo.
- Esto permite saber exactamente en qué proyecto se usó cada lote de material.

**Ejemplo:** Si ingresaron 5000 kg de asfalto el lunes y 3000 kg el miércoles, cuando el jueves se dispatchen 4000 kg a un proyecto, el sistema automáticamente consume los 3000 del miércoles más 1000 de los 5000 del lunes, dejando 4000 kg del lote del lunes disponibles.

---

## 6. PERMISOS Y SEGURIDAD

El sistema cuenta con control de acceso basado en roles (RBAC):

- Cada usuario inicia sesión con usuario y contraseña.
- Dependiendo de su rol, ve solo las opciones que puede usar.
- El administrador del sistema decide qué puede hacer cada rol.
- Los permisos son ajustables en cualquier momento sin necesidad de programar.

**Nivel de seguridad:** Medio. Login con autenticación, roles de acceso, y registro de actividad. Puede escalar a Alto según las necesidades de la organización.

---

## 7. TECNOLOGÍAS A UTILIZAR

| Componente | Tecnología |
|---|---|
| **Backend (lógica del servidor)** | PHP 8.2+ con Laravel 12 |
| **Frontend (interfaz de usuario)** | Vue 3 con Inertia.js |
| **Estilos y diseño** | Tailwind CSS 3 (interfaz oscura industrial) |
| **Base de datos (desarrollo)** | MySQL (XAMPP / phpMyAdmin) |
| **Base de datos (producción)** | PostgreSQL (planeado) |
| **Construcción y empaquetado** | Vite 7 |
| **Autenticación** | Laravel Breeze (personalizado) |
| **Despliegue (pruebas)** | Railway.app |
| **Despliegue (producción)** | Servidor de la Alcaldía con acceso a nube |

---

## 8. ALCANCES Y LÍMITES DEL SISTEMA

### Lo que SÍ hará:
- Registrar materiales, ingresos y salidas.
- Crear y gestionar proyectos con materiales vinculados.
- Asignar funcionarios a proyectos.
- Generar reportes PDF por mes o año.
- Control de inventario con trazabilidad FIFO por lotes.
- Gestión de usuarios y permisos configurables.
- Diseño responsive (funciona en PC, laptops, tablets y celulares).

### Lo que NO hará:
- **No manejos aspectos monetarios**: sin costos, precios de compra ni facturación.
- **No gestión de procesos de proyectos**: no hace seguimiento del avance de la obra en sí. Solo registra cuándo se recibieron los materiales y deduce la duración del proyecto de esas fechas.
- **No integración con otros sistemas** de la Alcaldía por ahora ( queda abierto para futuras conexiones).

---

## 9. METODOLOGÍA DE DESARROLLO

Se utilizará la metodología **Scrumban** (combinación de Scrum y Kanban):

- **Iteraciones cortas (Sprints)** para avanzar de forma estructurada.
- **Tablero visual (Kanban)** para seguir el progreso de cada funcionalidad.
- **Revisiones frecuentes** con el equipo para ajustar el camino según sea necesario.
- Flexibilidad para incorporar cambios o correcciones durante el desarrollo.

---

## 10. ENTREGABLES

Al finalizar el desarrollo, se entregarán:

1. **Sistema web funcional** desplegado y funcionando.
2. **Documentación técnica** del sistema (diagrama de base de datos, arquitectura, manuales de usuario).
3. **Informe descriptivo** del sistema para presentar a supervisores, ingenieros y personal no técnico de la planta.
4. **Código fuente** del proyecto en repositorio.

---

## 11. PLAZO ESTIMADO

**Deadline: 3 semanas máximo** desde el inicio del desarrollo.

---

## 12. PRÓXIMOS PASOS

1. Revisión de este preinforme por parte del equipo.
2. Aprobación o correcciones al documento.
3. Inicio del desarrollo con la planificación técnica detallada.
4. Demostraciones por iteración según la metodología Scrumban.

---

*Documento sujeto a correcciones y aprobaciones.*
# Documento de Visión y Alcance
## Proyecto: Sistema de Control de Materiales e Inventarios (Asphalt-AGY)
**Autor:** Estudiante de Ingeniería de Sistemas (UPEA)
**Entorno:** Planta de Asfalto - Trabajo de Pasantía

---

## 1. Introducción y Contexto del Negocio
Las plantas de producción de asfalto en caliente y frío manejan grandes volúmenes de materias primas críticas (áridos/agregados, cemento asfáltico o bitumen, combustibles y aditivos). El control preciso del inventario es fundamental, ya que una parada de producción por falta de material (especialmente el cemento asfáltico, que debe conservarse a altas temperaturas o bajo pedidos programados) genera costos elevados de maquinaria inactiva y penalizaciones por retraso en obras de infraestructura vial.

Actualmente, la planta carece de un sistema automatizado para cruzar los ingresos reportados por la balanza de camiones proveedores con los consumos diarios aplicados a cada proyecto de pavimentación. Esto dificulta la detección de pérdidas (merma por evaporación o humedad en agregados), la planificación de compras y la rendición de cuentas para las auditorías.

---

## 2. Declaración del Problema
| Elemento | Descripción |
| :--- | :--- |
| **El problema de** | Falta de control en tiempo real, falta de trazabilidad e imprecisión en los saldos de materiales en stock. |
| **Afecta a** | El Jefe de Planta, los operadores de balanza, los ingenieros residentes de obras y el departamento administrativo. |
| **El impacto es** | Retrasos en proyectos por desabastecimiento, discrepancias financieras entre lo comprado y lo usado, y dificultad para calcular la merma real de los agregados y asfalto. |
| **Una solución exitosa sería** | Un sistema web local (Intranet) que registre de forma transparente cada camión de ingreso (Balanza), asocie cada consumo a un proyecto específico (Dosificación/Egreso) y alerte automáticamente al alcanzar límites críticos de stock. |

---

## 3. Objetivos SMART
* **S** (Específico): Desarrollar un sistema de control de inventarios web integrado por módulos de Ingresos (Balanza de proveedores), Egresos (Consumos por proyecto) e Inventario (Saldos y alertas de stock mínimo).
* **M** (Medible): Lograr que el 100% de los camiones de proveedores y los despachos diarios a obras se registren digitalmente. Reducir a 0% los incidentes de quiebre de stock.
* **A** (Alcanzable): Desarrollar la aplicación sobre una pila ligera y altamente compatible (PHP + MySQL en servidor local XAMPP) adecuada para la infraestructura de la planta.
* **R** (Relevante): Proporcionar información en tiempo real para optimizar la toma de decisiones del Jefe de Planta y generar los reportes necesarios para la aprobación de la pasantía.
* **T** (Temporal): Diseñar, implementar, probar y desplegar el sistema funcional en un lapso de 8 semanas.

---

## 4. Partes Interesadas (Stakeholders)
1. **Jefe de Planta (Administrador):** Principal tomador de decisiones. Requiere reportes de consolidados de inventario, consumo por proyecto y alertas visuales de stock mínimo.
2. **Operador de Balanza / Almacenero (Operador):** Responsable de registrar las entradas de agregados y cemento asfáltico mediante el pesaje de camiones. Requiere un formulario rápido e intuitivo.
3. **Residente de Obra / Supervisor (Visor):** Requiere consultar el avance en el consumo de los materiales asignados a su respectivo proyecto para no exceder los presupuestos.
4. **Jurado Calificador (UPEA):** Evalúa la rigurosidad de la ingeniería del software aplicada, la calidad del código, el modelado y la seguridad del sistema.

---

## 5. Alcance del Sistema (Límites del Proyecto)

### 5.1. Funcionalidades Incluidas (En Alcance)
1. **Módulo de Autenticación y Seguridad:**
   * Control de acceso basado en roles (Administrador, Operador, Visor).
   * Contraseñas encriptadas mediante algoritmos seguros (ej. `password_hash` de PHP).
2. **Módulo de Gestión de Catálogos (CRUDs):**
   * Catálogo de Materiales (Nombre, descripción, unidad de medida: Ton, $m^3$, Galones, stock mínimo de alerta, stock actual).
   * Catálogo de Proveedores (Razón social, NIT, teléfono, dirección).
   * Catálogo de Proyectos (Nombre de la obra, ubicación, ingeniero a cargo, fecha inicio/fin, estado).
3. **Módulo de Ingresos (Balanza - Proveedores):**
   * Registro del ingreso de material: Material, Proveedor, Chofer, Placa del camión, Nro de Ticket de Balanza.
   * Registro de Peso: Peso Bruto (camión cargado) y Peso Tara (camión vacío).
   * Cálculo automático del Peso Neto (Neto = Bruto - Tara) en toneladas métricas, guardando la fecha/hora del servidor de forma inmutable.
4. **Módulo de Egresos / Consumos (Proyectos):**
   * Registro del consumo de material asignado a un Proyecto de obra.
   * Disminución automática del stock actual al procesar una salida.
5. **Módulo de Dashboard e Inventario:**
   * Vista de semáforo de stock (Verde: Stock OK, Amarillo: Cerca del límite, Rojo: Requiere compra urgente).
   * Gráficos estadísticos rápidos de los materiales más utilizados.
6. **Módulo de Reportes:**
   * Reporte de saldos de inventario general.
   * Reporte de consumos detallados por Proyecto (filtro por fechas).
   * Exportación de reportes a PDF (usando librerías como FPDF o TCPDF) listos para impresión.

### 5.2. Funcionalidades Excluidas (Fuera de Alcance)
* *Integración automática con hardware de balanza por puerto serial/red:* El operador digitará manualmente los pesos impresos por el software de balanza existente.
* *Facturación e Integración Contable/Tributaria:* El sistema no gestiona transacciones monetarias ni emite facturas del Servicio de Impuestos Nacionales (SIN) de Bolivia.
* *Control de asistencia del personal de planta:* Exclusivo para materiales de construcción.

---

## 6. Restricciones Técnicas
* **Servidor de Despliegue:** Servidor local (PC de planta ejecutando Windows y XAMPP).
* **Compatibilidad de Navegadores:** Debe funcionar de manera fluida en Google Chrome, Microsoft Edge y Firefox sin requerir conexión a Internet (funcionamiento 100% Intranet offline).
* **Seguridad de Datos:** Copias de seguridad automáticas o exportables de la base de datos MySQL en formato `.sql`.

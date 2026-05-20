# Diseño de Prototipos de Interfaz de Usuario (UI/UX)
## Proyecto: Sistema de Control de Materiales e Inventarios (Asphalt-AGY)

Este documento describe el sistema de diseño visual, la paleta de colores y la distribución de componentes para las principales pantallas del sistema, garantizando una interfaz premium, moderna e intuitiva de temática industrial.

---

## 1. Sistema de Diseño Visual (Style Guide)

* **Paleta de Colores (Tailwind CSS):**
  * **Fondo Principal:** `slate-900` (#0f172a) o `zinc-950` (#09090b) para un look oscuro limpio y premium.
  * **Tarjetas y Contenedores:** `slate-800/50` con efecto Glassmorphism (`backdrop-blur-md` y bordes sutiles `border-slate-700/50`).
  * **Color de Acento (Temática Asfalto):** `amber-500` (#f59e0b) y `orange-600` (#ea580c), que simulan la señalización industrial y maquinaria vial.
  * **Alertas de Stock:**
    * Stock Suficiente: `emerald-500` (#10b981).
    * Stock Crítico (Alerta Mínima): `amber-500` (#f59e0b).
    * Sin Stock / Agotado: `rose-500` (#f43f5e).
* **Tipografía:**
  * Fuente Primaria: **Outfit** o **Inter** (de Google Fonts), cargadas localmente para permitir el funcionamiento 100% offline.
* **Componentes Interactivos:**
  * Botones con transiciones suaves (`transition-all duration-300`) y efectos de elevación/iluminación al pasar el cursor (`hover:shadow-amber-500/20 hover:scale-[1.02]`).

---

## 2. Mockup y Estructura de Layout General

La aplicación utiliza un layout dividido (Sidebar de navegación fija y Panel de contenido dinámico).

```
+-----------------------------------------------------------------------------------+
|  [Asphalt-AGY Icon]             [Proyecto Activo Selector]    [User: Operador V]  |
+-----------------------------------------------------------------------------------+
|  (O) Dashboard     |  DASHBOARD - RESUMEN DE INVENTARIO                           |
|  [x] Proyectos     |  +-------------------+ +-------------------+ +-------------+ |
|  [+] Ingresos      |  | Stock Crítico: 2  | | Proyectos Act: 5  | | Regs Hoy: 8 | |
|  [-] Salidas       |  +-------------------+ +-------------------+ +-------------+ |
|  [=] Kardex PEPS   |                                                              |
|  [i] Catálogos     |  +---------------------------------------------------------+ |
|                    |  | TABLA DE MATERIALES Y NIVEL DE STOCK EN PLANTA             | |
|  ----------------- |  | Material     | Medida | Stock Total | Estado   | Acción  | |
|  [Cerrar Sesión]   |  | Cemento Asf. | Kg     | 9,750.00    | [Crítico]| Ver     | |
|                    |  | Grava 3/4    | m3     | 180.00      | [Normal] | Ver     | |
|                    |  +---------------------------------------------------------+ |
+-----------------------------------------------------------------------------------+
```

---

## 3. Detalle de Pantallas Clave

### 3.1. Pantalla: Login (Acceso Seguro)
* **Visual:** Un fondo oscuro profundo con un sutil degradado radial en tonos naranjas oscuros. Una tarjeta central con efecto de vidrio esmerilado (Glassmorphism).
* **Campos:**
  * Título: "ASPHALT-AGY" con el subtítulo "Control de Existencias - UPEA".
  * Input de texto con icono de usuario (`username`).
  * Input de contraseña con icono de candado (`password`).
  * Botón de acción principal: "Ingresar al Sistema" (estilo `bg-amber-500 text-slate-950 font-semibold hover:bg-amber-400`).

### 3.2. Pantalla: Registro de Ingreso de Insumos (Operador)
* **Visual:** Formulario limpio de dos columnas organizado en una tarjeta con bordes redondeados.
* **Campos:**
  * **Columna 1:**
    * Selector de Proyecto de Destino (Lista desplegable cargada de `proyectos`).
    * Nro. de Ticket de Balanza (Texto libre).
    * Orden de Compra (ODC) (Texto libre).
  * **Columna 2:**
    * Selector de Material (Lista desplegable cargada de `materials`).
    * Cantidad Adquirida (Número con validación de decimales).
    * Selector de Proveedor (Lista desplegable cargada de `proveedors`).
    * Fecha de Ingreso (DatePicker interactivo).
  * **Pie de Formulario:** Área de texto para "Observaciones" y botón "Guardar Ingreso" en color verde esmerilado.

### 3.3. Pantalla: Registro de Salida de Material (Despacho PEPS)
* **Visual:** Panel simplificado para agilizar el trabajo en planta.
* **Campos:**
  * Selector de Proyecto (Filtra automáticamente los materiales que tienen stock adquirido previamente para este proyecto).
  * Selector de Material (Muestra el stock disponible total del proyecto seleccionado).
  * Cantidad a Retirar (Validada contra el stock acumulado del proyecto).
  * Selector de Funcionario Receptor (Lista desplegable cargada de `funcionarios`).
  * Área de Uso (Texto descriptivo, ej. "Bacheo de la Av. Litoral").
  * Fecha de Salida (DatePicker).
* **Indicador en tiempo real:** Al escribir la cantidad, una etiqueta dinámica muestra si hay stock suficiente en los lotes del proyecto.

### 3.4. Pantalla: Kardex Físico PEPS (Reportes)
* **Visual:** Panel de filtros superior y tabla dinámica de resultados inferior preparada para impresión física directa.
* **Filtros:**
  * Selector de Material (Obligatorio).
  * Selector de Proyecto (Opcional, permite ver el Kardex consolidado de la obra o general de la planta).
  * Rango de Fechas (Desde / Hasta), con botones rápidos para "Mes Actual" y "Año Actual".
* **Tabla de Kardex (Físico):**
  * Columnas: Fecha | Documento (ODC/Ticket/Vale) | Operación (Ingreso / Salida) | Proyecto/Detalle | Entrada (Cantidad) | Salida (Cantidad) | Saldo (Cantidad Acumulada).
  * Botón en la esquina superior derecha: "Imprimir Reporte" (dispara la vista de impresión CSS optimizada `@media print` para ocultar barras de navegación y generar un documento listo para archivar).

# Design System Master File (Asphalt-AGY)

> **LOGIC:** Al construir o editar cualquier vista del sistema, consulte primero este archivo de diseño maestro. Para páginas específicas que requieran desviaciones visuales o interacciones particulares, cree un archivo de anulación en `doc/fase2_diseno/pages/[nombre-pagina].md`. Si dicho archivo existe, sus reglas **anulan** las de este Master; de lo contrario, se aplican estrictamente las reglas globales especificadas aquí.

---

**Proyecto:** Sistema de Control de Materiales e Inventarios (Asphalt-AGY)  
**Categoría:** Software Industrial / Panel de Control de Planta y Administración Gubernamental  
**Estado:** Fase de Diseño - Estructura Visual Premium  
**Tipografía Base:** Fira Sans (Interfaz) & Fira Code (Datos / Telemetría)  
**Layout Principal:** Estructura Asimétrica Bento Grid  

---

## 1. Reglas Globales (Global Rules)

### 1.1. Paleta de Colores Oficial (Color Palette)
> [!NOTE]
> La siguiente es la paleta de colores oficial y definitiva extraída de las capturas aprobadas para la interfaz de Asphalt-AGY, optimizada para ofrecer un alto contraste de legibilidad en entornos oscuros industriales.

| Rol Visual | Color Extraído / Nombre | Variable CSS | Valor Hexadecimal |
| :--- | :--- | :--- | :--- |
| **Primary Dark Background** | Fondo base de aplicación y Sidebar | `--color-background` | `#111417` |
| **Card Background** | Tarjetas, bloques Bento y contenedores | `--color-card` | `#1b1e22` |
| **Field/Input Background** | Fondo de inputs y campos de formulario | `--color-field` | `#0e1113` |
| **Primary Accent (Brand)** | Naranja de Seguridad Vial / Botones principales / Quotas | `--color-accent` | `#f27b00` |
| **Secondary Accent** | Active Blue / Botón activo de navegación y Sidebar | `--color-secondary-accent` | `#0a5c8f` |
| **Text Primary** | Texto estándar / Cifras principales | `--color-foreground` | `#e1e6eb` |
| **Text Muted** | Texto secundario / Labels de inputs / Subtítulos | `--color-muted` | `#8a949f` |
| **Border** | Gris metalizado para separadores y marcos sutiles | `--color-border` | `#2d3139` |
| **Alert Success (Bg)** | Fondo de alerta Stock Suficiente | `--color-success-bg` | `#0f3d2a` |
| **Alert Success (Border)**| Borde de alerta Stock Suficiente | `--color-success-border` | `#1a7f4c` |
| **Alert Success (Text)**  | Texto de alerta Stock Suficiente | `--color-success-text` | `#a6ffcc` |
| **Alert Warning (Bg)** | Fondo de alerta Stock Crítico/Mínimo | `--color-warning-bg` | `#3b2c12` |
| **Alert Warning (Border)**| Borde de alerta Stock Crítico/Mínimo | `--color-warning-border` | `#8a6125` |
| **Alert Warning (Text)**  | Texto de alerta Stock Crítico/Mínimo | `--color-warning-text` | `#ffe0a6` |
| **Alert Error (Bg)** | Fondo de alerta Stock Agotado/Error | `--color-destructive-bg` | `#3b1d22` |
| **Alert Error (Border)**| Borde de alerta Stock Agotado/Error | `--color-destructive-border` | `#8a252c` |
| **Alert Error (Text)**  | Texto de alerta Stock Agotado/Error | `--color-destructive-text` | `#ff8c94` |
| **Ring / Focus State** | Naranja de Enfoque Sutil | `--color-ring` | `#f27b00` |

*Nota: Todos los pares de color de alerta y texto principal cumplen rigurosamente con la relación de contraste WCAG AA (>= 4.5:1 para texto normal) para asegurar la legibilidad en pantallas industriales con baja iluminación.*

---

### 1.2. Tipografía Técnica (Typography)
Para reflejar precisión técnica y facilitar el escaneo rápido de cifras y telemetría por parte de operadores de planta en entornos offline, se utiliza una combinación tipográfica específica:

* **Fira Sans** (Sans-serif): Diseñada para pantallas y alta legibilidad en interfaces de usuario complejas.
  - **Uso:** Navegación, títulos de componentes, formularios, texto del cuerpo, botones y etiquetas generales.
* **Fira Code** (Monospace con ligaduras): Diseñada para código y presentación precisa de datos tabulares.
  - **Uso:** Lecturas de básculas, tickets de balanza, stocks numéricos, cantidades de insumos, fechas, tablas PEPS (Kardex) y códigos de error del sistema.

#### Escala Tipográfica (Typography Scale)
```css
--font-sans: 'Fira Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
--font-mono: 'Fira Code', Menlo, Monaco, Consolas, monospace;

/* Escala de Tamaños */
--text-xs: 0.75rem;   /* 12px - Micro-etiquetas, metadatos, unidades de medida */
--text-sm: 0.875rem;  /* 14px - Texto secundario, tablas, placeholders */
--text-base: 1rem;    /* 16px - Texto del cuerpo, inputs, navegación (Min. móvil) */
--text-lg: 1.125rem;  /* 18px - Subtítulos, cabeceras de tarjetas secundarias */
--text-xl: 1.25rem;   /* 20px - Títulos de tarjetas del Bento Grid */
--text-2xl: 1.5rem;   /* 24px - Títulos de sección, lecturas principales de stock */
--text-3xl: 1.875rem; /* 30px - Métricas destacadas (telemetría de tolva) */
--text-4xl: 2.25rem;  /* 36px - Números críticos de la báscula / balanza */
```

#### Reglas de Carga Offline
Dado que el sistema Asphalt-AGY opera en la planta física donde la conectividad a Internet es inestable o inexistente, las fuentes **Fira Sans** y **Fira Code** se compilarán y servirán de forma local dentro del directorio `public/fonts/` y se definirán en la hoja de estilos global mediante `@font-face`. No se realizarán llamadas externas a Google Fonts en producción.

---

### 1.3. Escala de Espaciado (Spacing Scale)
Basada en un incremento sistemático de 8dp (con un paso intermedio de 4dp para micro-alineación):

| Token CSS | Equivalencia Tailwind | Valor Absoluto | Uso Sugerido |
| :--- | :--- | :--- | :--- |
| `--space-xs` | `space-1` / `p-1` | `4px` / `0.25rem` | Micro-separación, bordes, separación interna en badges |
| `--space-sm` | `space-2` / `p-2` | `8px` / `0.5rem` | Separación icono-texto, gap entre botones de acción |
| `--space-md` | `space-4` / `p-4` | `16px` / `1rem` | Relleno de tarjetas Bento estándar, márgenes internos |
| `--space-lg` | `space-6` / `p-6` | `24px` / `1.5rem` | Espaciado entre bloques Bento, relleno de modales |
| `--space-xl` | `space-8` / `p-8` | `32px` / `2rem` | Margen superior de páginas, secciones del Dashboard |
| `--space-2xl` | `space-12` / `p-12` | `48px` / `3rem` | Márgenes de la pantalla de login, secciones hero |
| `--space-3xl` | `space-16` / `p-16` | `64px` / `4rem` | Márgenes exteriores en layouts ultra-anchos |

---

### 1.4. Profundidad y Sombras Industriales (Shadows & Elevation)
Al tratarse de una temática oscura industrial, las sombras tradicionales no son muy visibles. Por lo tanto, la elevación se representa mediante combinaciones de colores de superficie más claros, bordes sutiles y efectos de resplandor (glow) de acento.

```css
/* Bordes y sombras oscuras */
--border-subtle: 1px solid rgba(255, 255, 255, 0.08);
--border-interactive: 1px solid rgba(245, 158, 11, 0.2); /* Acento sutil */

--shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.5);
--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.6), 0 2px 4px -1px rgba(0, 0, 0, 0.4);
--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.7), 0 4px 6px -2px rgba(0, 0, 0, 0.5);

/* Efecto de Elevación Industrial (Border Glow) */
--glow-accent: 0 0 12px rgba(245, 158, 11, 0.15);
--glow-success: 0 0 12px rgba(16, 185, 129, 0.15);
--glow-destructive: 0 0 12px rgba(239, 68, 68, 0.15);
```

---

## 2. Estructura de Diseño y Layouts Clave

El sistema Asphalt-AGY define dos estructuras de layout principales diseñadas para alta eficiencia en la planta: la pantalla de login (acceso seguro) y el panel de control (Dashboard Bento Grid).

### 2.1. Pantalla de Login (Panel Dividido)
La pantalla de login utiliza una estructura asimétrica de dos columnas para combinar la identidad industrial con un formulario limpio y directo:

* **Panel Izquierdo (Visual Brand - 60% de ancho en Desktop):**
  - Muestra una imagen industrial a pantalla completa del proceso de producción de asfalto en escala de grises.
  - Superpone en alto contraste y de forma minimalista el lema del sistema:
    > "Precision. Resilience. Performance."
    *(Renderizado en Fira Sans, con "Precision" y "Performance" en Blanco Titán y "Resilience" destacado en Naranja Industrial `#f27b00`)*.
* **Panel Derecho (Formulario - 40% de ancho en Desktop):**
  - Fondo base limpio `#111417` (Deep Charcoal).
  - Tarjeta central del formulario utilizando el color de fondo de tarjeta `#1b1e22` con esquinas redondeadas (`12px`) y un borde superior destacado de `4px` en Naranja Industrial `#f27b00`.
  - Botón de acceso sólido naranja con la leyenda "LOG IN" en mayúsculas y fuente de alta densidad tipográfica.

### 2.2. Dashboard Bento Grid
El Dashboard utiliza una distribución Bento Grid optimizada para monitorear y registrar transacciones rápidamente.

```
+---------------------------------------------------------------------------------------+
|  [ A: Métrica Crítica Stock ]  |  [ B: Dosificación de Tolvas / Telemetría Activa ]   |
|  - Alertas de Stock (Derecha)  |  - Telemetría en tiempo real (Tolvas/Báscula)        |
|  - Barras de Progreso Naranja  |  - Cifras en Fira Code de alto contraste             |
|  (Col-span-4 / Row-span-2)     |  (Col-span-8 / Row-span-4)                           |
|                                |                                                      |
|                                |                                                      |
+--------------------------------+                                                      |
|  [ C: Accesos Rápidos ]        |                                                      |
|  - Botones de Registro Rápido  |                                                      |
|  - Botón Activo (Active Blue)  |                                                      |
|  (Col-span-4 / Row-span-2)     |                                                      |
+--------------------------------+------------------------------------------------------+
|  [ D: Últimas Transacciones (Ingresos / Salidas PEPS) ]                               |
|  - Gráfico de barras "Environmental Monitoring" en la base                            |
|  - Tabla de Auditoría en Fira Code                                                    |
|  (Col-span-12 / Row-span-3)                                                           |
|                                                                                       |
+---------------------------------------------------------------------------------------+
```

#### Reglas de Composición Bento Grid:
1. **Tarjetas de Contenedor:** Fondo sólido `#1b1e22` (Card Background), esquinas redondeadas (`12px`), y bordes sutiles `#2d3139` para separar los datos sin recargar la vista.
2. **Barra de Progreso de Quotas:** Indicadores visuales de capacidad de tolvas y cuotas de inventario utilizan el color Naranja Industrial `#f27b00` para denotar progreso activo.
3. **Semáforo y Alertas de Stock:** Ubicados de forma consistente en la esquina superior/derecha del Bento, informando de inmediato el estado de insumos críticos con la coloración semáforo oficial.
4. **Gráfico de Telemetría (Environmental Monitoring):** En la base del dashboard (Bloque D), se implementa un gráfico de barras técnico que muestra la monitorización de emisiones y consumos de la planta.

---

## 3. Especificaciones de Componentes (Component Specs)

### 3.1. Tarjeta Bento (Bento Card)
Bloques del Dashboard que encapsulan la telemetría e inputs.

```css
.bento-card {
  background-color: var(--color-card); /* #1b1e22 */
  border: 1px solid var(--color-border); /* #2d3139 */
  border-radius: 12px;
  padding: var(--space-md);
  transition: border-color 200ms ease, box-shadow 200ms ease, transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
  display: flex;
  flex-direction: column;
}

/* Hover interactivo con glow naranja */
.bento-card:hover {
  transform: translateY(-2px);
  border-color: rgba(242, 123, 0, 0.4);
  box-shadow: var(--shadow-lg), 0 0 12px rgba(242, 123, 0, 0.15);
}
```

### 3.2. Botones de Acción (Buttons)

```css
/* Botón Primario Sólido (LOG IN / Guardar Transacción) */
.btn-primary {
  font-family: var(--font-sans);
  background-color: var(--color-accent); /* #f27b00 */
  color: #111417; /* Contraste máximo con fondo oscuro */
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 12px 24px;
  border-radius: 8px;
  transition: background-color 150ms ease, transform 100ms ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-sm);
  border: none;
}

.btn-primary:hover:not(:disabled) {
  background-color: #ff9426; /* Naranja más brillante para interacción */
  transform: scale(1.02);
}

.btn-primary:active:not(:disabled) {
  transform: scale(0.98);
}

/* Botón Activo (Azul Acero para Sidebar / Tab Activo) */
.btn-active-blue {
  font-family: var(--font-sans);
  background-color: var(--color-secondary-accent); /* #0a5c8f */
  color: var(--color-foreground); /* #e1e6eb */
  font-weight: 600;
  padding: 10px 20px;
  border-radius: 8px;
  border: none;
  transition: background-color 150ms ease;
}

.btn-active-blue:hover {
  background-color: #0d75b5; /* Azul más claro */
}
```

### 3.3. Entradas de Formulario (Form Inputs)

```css
.form-input {
  font-family: var(--font-sans);
  background-color: var(--color-field); /* #0e1113 */
  color: var(--color-foreground); /* #e1e6eb */
  border: 1px solid var(--color-border); /* #2d3139 */
  border-radius: 6px;
  padding: 10px 14px;
  font-size: var(--text-base);
  width: 100%;
  transition: border-color 150ms ease, box-shadow 150ms ease;
}

/* Foco con resplandor naranja */
.form-input:focus {
  outline: none;
  border-color: var(--color-accent); /* #f27b00 */
  box-shadow: 0 0 0 3px rgba(242, 123, 0, 0.25);
}

.form-input-numeric {
  font-family: var(--font-mono); /* Fira Code */
  text-align: right;
}
```

### 3.4. Indicadores y Alertas de Stock (Alerts & Semaphores)
Utilizados en la esquina del Bento para notificar la disponibilidad de áridos y ligantes en tiempo real.

```css
/* Stock Suficiente (Success) */
.alert-success {
  background-color: var(--color-success-bg); /* #0f3d2a */
  border: 1px solid var(--color-success-border); /* #1a7f4c */
  color: var(--color-success-text); /* #a6ffcc` */
}

/* Stock Crítico (Warning) */
.alert-warning {
  background-color: var(--color-warning-bg); /* #3b2c12 */
  border: 1px solid var(--color-warning-border); /* #8a6125 */
  color: var(--color-warning-text); /* #ffe0a6 */
}

/* Sin Stock / Agotado (Error) */
.alert-error {
  background-color: var(--color-destructive-bg); /* #3b1d22 */
  border: 1px solid var(--color-destructive-border); /* #8a252c */
  color: var(--color-destructive-text); /* #ff8c94 */
}
```

---

## 4. Sistema de Animación y Transiciones (Motion System)

El movimiento en Asphalt-AGY debe ser funcional, no meramente decorativo. Debe servir para guiar la atención del operador a variaciones en la telemetría o confirmaciones de pesaje.

* **Duraciones de Transición:**
  - Micro-interacciones rápidas (botones, enlaces, checks): `150ms`
  - Transiciones de componentes medianos (cambio de tabs, expansión de tarjetas Bento): `200ms`
  - Modales, hojas de detalle o vistas completas: `250ms` (máximo `300ms`)
* **Curvas de Aceleración (Easing):**
  - Entrada de elementos: `cubic-bezier(0.16, 1, 0.3, 1)` (Out-Expo para un frenado suave y elegante).
  - Salida de elementos: `cubic-bezier(0.7, 0, 0.84, 0)` (In-Expo para una salida rápida).
  - Movimiento entre estados: `cubic-bezier(0.87, 0, 0.13, 1)` (In-Out-Quart).

### 4.1. Transición de Carga Staggered (Entrada de Rejilla Bento)
Al cargar el Dashboard, las tarjetas del Bento Grid aparecen secuencialmente para dar sensación de rendimiento óptimo:
- Retraso inicial (Delay) de `30ms` por tarjeta en orden de lectura (de izquierda a derecha, de arriba abajo).
- Efecto: Desplazamiento vertical sutil de `10px` hacia arriba complementado con un desvanecimiento (Fade-in).

```css
@keyframes bentoEntry {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.bento-card-animate {
  animation: bentoEntry 300ms cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
```

### 4.2. Soporte para Movimiento Reducido (`prefers-reduced-motion`)
El sistema respeta las preferencias del sistema operativo del operador. Si la reducción de movimiento está activa, se deshabilitan todas las traslaciones físicas (`translateY`) y las transiciones se limitan a desvanecimientos instantáneos de opacidad.

```css
@media (prefers-reduced-motion: reduce) {
  .bento-card, .btn-primary, .form-input, .bento-card-animate {
    transition: none !important;
    animation: none !important;
    transform: none !important;
  }
  .bento-card-animate {
    opacity: 1 !important;
  }
}
```

---

## 5. Buenas Prácticas de UX e Incompatibilidades (Anti-patterns to Avoid)

Para mantener la excelencia en la interfaz de usuario en una planta de asfalto gubernamental, evite rigurosamente lo siguiente:

1. **PROHIBIDO: Emojis como Iconos Estructurales.**  
   *Por qué:* Los emojis varían según el sistema operativo, restan formalidad y no se adaptan a los tokens de color oscuros.  
   *Solución:* Utilice únicamente la familia de iconos vectoriales **Phosphor Icons** (de forma primaria) o **Heroicons** (de forma secundaria), asegurando trazos consistentes de `1.5px` o `2px`.
2. **PROHIBIDO: Saltos de Diseño (Layout Shift - CLS) durante Pesajes.**  
   *Por qué:* El operador necesita precisión absoluta. Si al recibir el peso de la báscula los elementos adyacentes se desplazan, puede inducir a error.  
   *Solución:* Reserve espacio reservado para los valores numéricos con contenedores de ancho y alto fijo.
3. **PROHIBIDO: Dependencia Exclusiva del Evento Hover.**  
   *Por qué:* En terminales industriales táctiles de planta no existe el cursor. Toda acción crítica debe poder ejecutarse mediante clics/toques directos.  
   *Solución:* No esconda botones de guardar o editar bajo estados de hover. Utilice interfaces visibles y con hit targets de mínimo `44x44px`.
4. **PROHIBIDO: Carga de Fuentes Externas (Google Fonts online).**  
   *Por qué:* Si se corta el enlace a Internet, la aplicación se verá deformada.  
   *Solución:* Servir localmente Fira Sans y Fira Code desde `public/fonts/`.
5. **PROHIBIDO: Colores Ad-Hoc en Vistas.**  
   *Por qué:* Rompe la consistencia de la marca y dificulta el mantenimiento del modo oscuro.  
   *Solución:* Utilice clases de Tailwind semánticas basadas en variables CSS del sistema de diseño (ej. `bg-primary`, `text-accent`, etc.).

---

## 6. Lista de Verificación Pre-Entrega (Pre-Delivery Checklist)

Antes de pasar cualquier componente frontend o vista de la Fase 2 a implementación en Laravel/Inertia/Vue, verifique:

- [ ] **Accesibilidad de Contraste:** ¿El contraste del texto principal y de cifras críticas en modo oscuro supera la relación de 4.5:1?
- [ ] **Fuentes Locales:** ¿Las tipografías Fira Sans y Fira Code se cargan correctamente con la aplicación desconectada de Internet?
- [ ] **Touch Target:** ¿Todos los botones y selectores táctiles tienen un área interactiva de al menos 44x44px?
- [ ] **Tabulación de Datos:** ¿Todos los números del Kardex y las lecturas de peso se renderizan usando `font-mono` (Fira Code) para evitar variaciones visuales en las columnas?
- [ ] **Foco Visual:** ¿Los campos del formulario poseen un anillo de enfoque sutil (`ring`) visible al navegar con teclado o pulsar en pantalla táctil?
- [ ] **Feedback de Carga:** ¿Los botones de guardar transacciones PEPS muestran un estado deshabilitado e indicador de carga al enviar los datos para evitar clics dobles?
- [ ] **Compatibilidad de Movimiento:** ¿Se verificó que las animaciones Bento Grid respetan la directiva `@media (prefers-reduced-motion)`?

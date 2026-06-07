# Rediseño Del Dashboard Y App Shell

## Resumen

Se rediseñará la interfaz como una **Consola Industrial** para Asphalt-AGY, usando estos nombres oficiales:

- **Panel de Navegación:** zona izquierda para cambiar entre módulos.
- **Área de Trabajo:** zona derecha donde se muestra Dashboard, Ingresos, Materiales, Kardex, etc.
- **App Shell:** estructura completa que contiene Panel de Navegación + Área de Trabajo.

El objetivo principal es separar los scrolls: el Panel de Navegación no debe desplazarse junto con el Dashboard.

## Cambios Clave

- Convertir `AuthenticatedLayout.vue` en un App Shell de alto fijo (`100vh`) con dos zonas independientes.
- En escritorio:
    - Panel de Navegación fijo a la izquierda.
    - Logo/usuario/footer siempre visibles.
    - Solo la lista central de opciones tendrá scroll propio.
    - Área de Trabajo tendrá scroll propio separado.
    - Barra superior y encabezado de página quedarán fijos dentro del Área de Trabajo.
- En móvil:
    - Mantener el drawer actual.
    - Solo ajustar scroll/altura si el menú móvil se desborda.

## Rediseño Visual

- Panel de Navegación:
    - Reorganizar grupos: Operación, Catálogos, Seguridad.
    - Mejorar jerarquía visual del estado activo, permisos y separación de secciones.
    - Mantener estética industrial oscura con bordes sutiles, Fira Sans y Fira Code.
    - Usar iconografía SVG/Heroicons existente, sin añadir nueva dependencia.

- Dashboard:
    - Rediseño completo con dirección **Consola Industrial**.
    - Prioridad visual:
        1. Estado general de stock y alertas críticas.
        2. Flujo operativo de planta/PEPS.
        3. Inventario por proyecto.
        4. Últimos movimientos.
        5. Mapa y clima como widgets secundarios.
    - Mapa y clima se mantienen, pero con fallback claro si no hay conexión o datos.
    - Evitar que widgets secundarios dominen la vista inicial.

## Estructura Propuesta

```text
App Shell
├─ Panel de Navegación
│  ├─ Marca Asphalt-AGY fijo
│  ├─ Usuario/rol fijo
│  ├─ Lista de módulos con scroll independiente
│  └─ Perfil/Cerrar sesión fijo
└─ Área de Trabajo
   ├─ Barra superior fija
   ├─ Encabezado de página fijo
   └─ Contenido scrollable
      └─ Dashboard consola industrial
```

## Implementación Prevista

- Modificar principalmente:
    - `resources/js/Layouts/AuthenticatedLayout.vue`
    - `resources/js/Pages/Dashboard.vue`
    - `resources/css/app.css`
- Separar responsabilidades visuales del Dashboard en componentes internos o bloques reutilizables si el archivo queda demasiado grande.
- No cambiar permisos, rutas, base de datos ni lógica PEPS.
- No añadir APIs nuevas al backend salvo que el rediseño necesite un dato ya calculable y simple.

## Pruebas Y Validación

- Verificar en escritorio que:
    - Al bajar el Panel de Navegación, el Dashboard no se mueve.
    - Al bajar el Dashboard, el Panel de Navegación no se mueve.
    - Logo, usuario y footer del panel permanecen visibles.
    - Barra superior y encabezado del Área de Trabajo permanecen visibles.
- Verificar en móvil que:
    - El drawer abre/cierra correctamente.
    - El menú móvil no bloquea el contenido ni queda cortado.
- Verificar estados:
    - Usuario administrador, operador y visor.
    - Dashboard con muchos materiales/proyectos.
    - Dashboard sin datos.
    - Clima o mapa sin conexión.
- Ejecutar build frontend: `npm run build`.

## Supuestos

- Mantendremos la identidad visual actual: oscuro industrial, naranja vial `#f27b00`, superficies sobrias y datos en Fira Code.
- El rediseño será visual/estructural; no cambia reglas de negocio.
- Mapa y clima son informativos, no críticos para operación offline.

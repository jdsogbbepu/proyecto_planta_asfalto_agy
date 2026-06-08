# Análisis Inicial del Proyecto

**Fecha:** 2026-05-21
**Skill utilizada:** arquitectura-dev-inicial

---

## Bloque 0: Meta-información

- **Nombre del proyecto:** Sistema de Gestion "Planta de Asfalto"
- **Objetivo principal (una frase):** Control integral del flujo de materiales por proyecto (ingreso, consumo, saldo) con trazabilidad FIFO, actividades, funcionarios involucrados, y generación de reportes PDF por período.
- **Equipo:** Solo el usuario + un compañero que ayuda ocasionalmente.

---

## Bloque 1: Visión y Alcance

- **Problema a resolver:** Sistema anterior deficiente + control manual con Excel/Word que consume mucho tiempo en transcripción y genera errores.
- **Usuarios finales:** Operador de planta, jefe, administrador, y personal de la planta/alcaldía.
- **Límites (qué NO debe hacer):**
  1. Sin aspectos monetarios/costos — solo controla cuánto llegó, cuánto se usó, cuánto sobró.
  2. Sin gestión de procesos de proyectos — solo registra fechas de ingreso de materiales.
  3. Sin requisitos legales/normativos por ahora.
- **Requisitos legales/normativos:** Ninguno por ahora.
- **Usuarios concurrentes estimados:** 2-3 máximo.
- **Nivel de seguridad requerido:** Medio (con posibilidad de escalar a alto).

---

## Bloque 2: Requisitos Funcionales

- **Funcionalidades principales:**
  1. Registrar materiales
  2. Anotar materiales de ingreso y salida
  3. Generar reportes PDF por mes o año
  4. Crear proyectos
  5. Seguimiento y edición de proyectos
  6. Gestionar usuarios y roles
  7. Trazabilidad FIFO de lotes de materiales
- **Funcionalidad crítica (corazón del sistema):**
  1. Creación de proyectos con materiales vinculados
  2. Generación de reportes PDF
- **Autenticación y roles:**
  - **Administrador del sistema**: Crea usuarios, define roles, asigna permisos personalizables, suspende usuarios.
  - **Operador** (por defecto): Crear/editar proyectos, relacionar funcionarios, anotar detalles, ver/editar/imprimir otros proyectos.
  - **Visor** (por defecto): Solo lectura, ver proyectos, revisar, imprimir PDF. Sin crear, editar ni eliminar.
  - **Permisos granulares**: El administrador puede personalizar cada permiso de cada rol.
- **Integraciones con otros sistemas:** Potencialmente base de datos existente o servicio web (sin confirmar).

---

## Bloque 3: Requisitos Técnicos (Stack)

- **Backend (lenguaje/framework):** Laravel 12 (PHP 8.2+)
- **Frontend (tecnología):** Vue 3 + Inertia.js + Tailwind CSS
- **Base de datos:** MySQL (XAMPP/phpMyAdmin) actual; PostgreSQL planeado para producción.
- **Hosting / alojamiento:** Servidor de la Alcaldía (producción), Railway.app (prototipo/pruebas).
- **Herramientas adicionales:** Se definirán durante el desarrollo (Docker, Redis, etc.).

---

## Bloque 4: Diseño y UX

- **Diseño previo o referencia:** Interfaz oscura industrial con acentos naranja (#f27b00). Diseño existente facilitado por el equipo. Posibles instrucciones adicionales将来.
- **Responsive (móvil/escritorio):** PC, laptop, tablets y celulares.
- **Restricciones de accesibilidad:** Ninguna por ahora.
- **Idiomas:** Solo español.

---

## Bloque 5: Metodología y Entregables

- **Metodología de desarrollo:** Scrumban (combinación de Scrum + Kanban).
- **Entregables adicionales:**
  1. Documentación técnica (ER, arquitectura, manuales de usuario)
  2. Informe descriptivo para presentar a supervisores, ingeniero, encargado, etc.
  3. Código fuente en repositorio
- **Fecha límite aproximada:** 3 semanas máximo.
- **Definición de arquitectura (quién propone):** En conjunto — usuario propone, equipo valida, decisión compartida.

---

## Bloque 6: Confirmación y Cierre

- **Otras observaciones o restricciones:** Ninguna por ahora.
- **Preferencia de siguiente paso:** Redactar preinforme para presentar al equipo.
- **Confirmación de respuestas:** Sí, confirmadas por el usuario.
# Skill: Plan de Desarrollo Web Profesional de 9 Fases

Este documento define el procedimiento operativo estándar y la metodología obligatoria de desarrollo que debe seguir el Agente Principal en este proyecto. Está diseñado como una guía de habilidades ejecutable para garantizar la calidad, consistencia y alineación con los requerimientos del usuario.

---

## 🚨 Regla de Oro del Agente Principal

> [!IMPORTANT]
> **PREGUNTAR Y PLANIFICAR ANTES DE TOCAR CÓDIGO**
> 
> **Queda estrictamente prohibido al Agente Principal generar archivos de código (como `.php`, `.js`, `.css`, `.sql`, etc.) o inicializar el desarrollo técnico del software hasta que el usuario haya aprobado explícitamente y por escrito el Documento de Visión y Alcance, el diseño arquitectónico, el modelo de base de datos y los prototipos visuales (Fases 0 a 3).**
> 
> Ninguna línea de código de producción o base de datos debe ser escrita antes de completar y validar formalmente las fases de planificación inicial.

---

## Estructura detallada del Plan de Desarrollo de 9 Fases

A continuación se detallan las fases consecutivas que guían el ciclo de vida de este proyecto de desarrollo web. Cada fase debe culminar con un entregable claro y la aprobación del usuario antes de proceder a la siguiente.

### Fase 0: Alineación Inicial, Visión y Alcance
* **Objetivo:** Comprender las necesidades del cliente, definir el propósito del software, delimitar el alcance del proyecto y prevenir la desviación de requisitos (scope creep).
* **Pasos Clave:**
  1. Entrevistas de levantamiento de requerimientos.
  2. Identificación de usuarios finales y casos de uso principales.
  3. Establecimiento de límites del proyecto (qué está incluido y qué queda fuera).
  4. Análisis de riesgos y viabilidad técnica.
* **Entregable:** **Documento de Visión y Alcance** (en formato Markdown en `doc/vision_y_alcance.md`).
* **Herramientas:** Documentos de texto colaborativos, mapas mentales, diagramas conceptuales.

### Fase 1: Diseño Arquitectónico e Infraestructura
* **Objetivo:** Diseñar la topología del sistema, el flujo de datos y decidir el stack tecnológico definitivo.
* **Pasos Clave:**
  1. Definición del patrón arquitectónico (por ejemplo, MVC, Cliente-Servidor, APIs REST).
  2. Elección de lenguajes, frameworks y entornos de ejecución (ej. PHP/Laravel, JavaScript/Node, XAMPP, Apache).
  3. Definición de la estrategia de alojamiento (Hosting local, VPS, Cloud).
  4. Establecimiento de pautas de seguridad iniciales.
* **Entregable:** **Documento de Diseño Arquitectónico** (en `doc/arquitectura.md`) incluyendo diagramas de bloques del sistema.
* **Herramientas:** Draw.io, Mermaid.js, Lucidchart.

### Fase 2: Modelado del Almacenamiento de Datos (Base de Datos)
* **Objetivo:** Estructurar el almacenamiento de la información de manera óptima, garantizando la integridad, consistencia y escalabilidad de los datos.
* **Pasos Clave:**
  1. Identificación de entidades y relaciones del negocio.
  2. Creación del Diagrama Entidad-Relación (DER).
  3. Normalización del modelo relacional (habitualmente hasta la 3ª Forma Normal).
  4. Definición de tipos de datos, llaves primarias, llaves foráneas, índices y restricciones (constraints).
* **Entregable:** **Esquema Conceptual/Lógico de la Base de Datos** y scripts SQL de definición de tablas estructurados pero sin ejecutar (en `doc/database/`).
* **Herramientas:** dbdiagram.io, MySQL Workbench, draw.io, DB Designer.

### Fase 3: Prototipado Visual y Experiencia de Usuario (UI/UX)
* **Objetivo:** Definir la interfaz visual, el diseño de pantalla y el flujo de navegación de la aplicación para validar la experiencia de usuario de manera ágil.
* **Pasos Clave:**
  1. Diseño de Wireframes (esquemas de baja fidelidad) de las vistas principales.
  2. Diseño de Mockups (alta fidelidad) con colores, tipografías y estilos finales.
  3. Creación de prototipos interactivos para simular el flujo de clics del usuario.
* **Entregable:** **Prototipos Visuales e Informe de Flujo de Navegación** (en `doc/ui_ux/`).
* **Herramientas:** Figma, Penpot, Balsamiq.

---
> [!WARNING]
> **Punto de Control Crítico:** Solo tras la aprobación formal de las Fases 0, 1, 2 y 3 se puede proceder a las siguientes fases de desarrollo activo de software.
---

### Fase 4: Desarrollo Backend y APIs
* **Objetivo:** Construir el motor lógico del software, la gestión de reglas de negocio, la seguridad y el procesamiento de datos.
* **Pasos Clave:**
  1. Inicialización del entorno de desarrollo y configuración del repositorio Git.
  2. Creación de la base de datos física y ejecución de migraciones.
  3. Desarrollo de controladores, modelos y servicios backend.
  4. Implementación del sistema de autenticación (Login, roles y permisos).
  5. Creación y documentación de endpoints de API (REST/GraphQL).
* **Entregable:** **Código Backend funcional, APIs expuestas y Documentación de Endpoints** (API Specs).
* **Herramientas:** PHP (Vanilla o Frameworks), Node.js, Express, Postman, Insomnia, Swagger.

### Fase 5: Desarrollo Frontend e Integración
* **Objetivo:** Crear la interfaz de usuario interactiva y conectarla con los servicios backend para asegurar una experiencia de usuario fluida y reactiva.
* **Pasos Clave:**
  1. Maquetación estructurada de plantillas HTML y hojas de estilo CSS (o frameworks como Tailwind/Bootstrap).
  2. Desarrollo del comportamiento dinámico mediante JavaScript.
  3. Conexión y consumo de las APIs desarrolladas en la Fase 4.
  4. Asegurar el diseño adaptativo (Responsive Web Design) para dispositivos móviles y escritorio.
* **Entregable:** **Interfaz de usuario (Frontend) totalmente integrada con el Backend**.
* **Herramientas:** HTML5, CSS3, JavaScript, Bootstrap, TailwindCSS, React/Vue (si aplica), Vite.

### Fase 6: Pruebas, Aseguramiento de Calidad (QA) y Optimización
* **Objetivo:** Detectar fallos, optimizar el rendimiento y asegurar que el producto cumple con los estándares de calidad antes del despliegue.
* **Pasos Clave:**
  1. Pruebas Unitarias (Unit Testing) y de Integración.
  2. Pruebas funcionales y de aceptación de usuario (UAT).
  3. Auditoría de rendimiento (tiempos de carga, optimización de imágenes y scripts).
  4. Análisis de seguridad básica (prevención de inyecciones SQL, XSS, etc.).
  5. Optimización SEO básico e inclusión de etiquetas meta.
* **Entregable:** **Reporte de Calidad (QA Report) y Aplicación Optimizada**.
* **Herramientas:** PHPUnit, Jest, Cypress, Google Lighthouse, OWASP ZAP.

### Fase 7: Despliegue (Deployment) y Puesta en Producción
* **Objetivo:** Publicar la aplicación en el entorno de producción para que esté disponible para los usuarios finales.
* **Pasos Clave:**
  1. Configuración del servidor web de producción (Apache, Nginx).
  2. Configuración y securización de la base de datos en producción.
  3. Configuración de certificados de seguridad SSL (HTTPS).
  4. Automatización del despliegue mediante pipelines de CI/CD (opcional).
  5. Configuración de nombres de dominio y registros DNS.
* **Entregable:** **Sitio Web / Aplicación en Vivo en URL pública o de producción**.
* **Herramientas:** Git, GitHub Actions, Docker, cPanel, SSH, Let's Encrypt.

### Fase 8: Mantenimiento, Monitoreo y Soporte Post-Lanzamiento
* **Objetivo:** Garantizar la estabilidad a largo plazo del sistema, corregir errores imprevistos en producción y planificar mejoras iterativas.
* **Pasos Clave:**
  1. Configuración de sistemas de monitoreo de errores y disponibilidad del servidor.
  2. Implementación de políticas de copias de seguridad (backups) periódicas automáticas.
  3. Actualización de librerías, dependencias y parches de seguridad del servidor.
  4. Recolección de retroalimentación de usuarios para futuras versiones.
* **Entregable:** **Logs de Operación y Reportes de Mantenimiento Mensuales/Periódicos**.
* **Herramientas:** Sentry, Google Analytics, Logrotate, Cron jobs.

---

## Instrucciones de Ejecución para el Agente Principal

Cuando te enfrentes a un nuevo proyecto o módulo:
1. **Verificación Inicial:** Revisa si existe la documentación de las Fases 0 a 3.
2. **Si no existe:** Solicita inmediatamente al usuario iniciar la Fase 0 escribiendo el Documento de Visión y Alcance.
3. **Puntos de Aprobación:** Al final de cada una de las fases 0, 1, 2 y 3, detén tu ejecución y presenta los entregables al usuario usando la frase: *"Solicito aprobación formal para avanzar a la siguiente fase del plan."*
4. **No generes código fuente ejecutable** de las fases 4 y 5 hasta recibir la confirmación por escrito.

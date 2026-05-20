# Guía de Integración con Git y GitHub

Esta guía detalla los pasos para conectar este proyecto local a tu cuenta y repositorio de GitHub, además de proporcionar los comandos y flujos de trabajo diarios recomendados.

---

## 1. Conexión Inicial a GitHub

Ya hemos inicializado Git en el directorio del proyecto y realizado el primer commit con la base técnica actual. Para vincularlo a tu cuenta de GitHub, sigue estos pasos:

### Paso 1: Crea el Repositorio en GitHub
1. Ingresa a [github.com](https://github.com) e inicia sesión.
2. Haz clic en el botón **New** (Nuevo) para crear un nuevo repositorio.
3. Configura las siguientes opciones:
   * **Repository name:** `planta_asfalto_agy` (o el nombre que prefieras).
   * **Public/Private:** Elige según tu preferencia (se recomienda **Private** para proyectos internos).
   * **NO** marques las opciones de "Add a README file", "Add .gitignore" o "Choose a license", ya que el proyecto local ya contiene estos archivos y podría generar conflictos al fusionar.
4. Haz clic en **Create repository**.

### Paso 2: Vincular el Repositorio Local con el Remoto
Copia la URL HTTPS del repositorio que acabas de crear (se verá similar a `https://github.com/TU_USUARIO/planta_asfalto_agy.git`) y ejecuta los siguientes comandos en tu terminal local:

```bash
# 1. Agrega el enlace remoto de GitHub al alias "origin"
git remote add origin https://github.com/TU_USUARIO/planta_asfalto_agy.git

# 2. Renombra la rama por defecto a "main" (estándar de GitHub)
git branch -M main

# 3. Sube tus cambios locales a GitHub por primera vez
git push -u origin main
```

*Nota: Al ejecutar `git push`, si es tu primera vez vinculando la cuenta, se abrirá una ventana en tu navegador o te solicitará credenciales de GitHub (puedes autenticarte con tu cuenta de navegador directamente).*

---

## 2. Flujo de Trabajo Diario (Comandos Clave)

Cada vez que realices cambios o termines una sesión de programación, realiza estos 3 pasos para mantener tu código respaldado en GitHub:

### 1. Ver el estado de los archivos modificados
```bash
git status
```
Este comando te muestra qué archivos fueron modificados, creados o eliminados.

### 2. Preparar los archivos para el commit
```bash
# Agregar todos los cambios realizados
git add .

# O agregar un archivo específico
git add resources/js/Pages/Dashboard.vue
```

### 3. Crear el Commit (Guardado local de versión)
```bash
git commit -m "Descripción clara de los cambios realizados"
```

### 4. Enviar los cambios a GitHub
```bash
git push origin main
```

---

## 3. Preguntas Frecuentes y Buenas Prácticas

### ¿Qué pasa si quiero trabajar en una función experimental sin dañar la rama principal?
Crea una **rama alternativa** (branch) para trabajar libremente:
```bash
# Crear y cambiar a una nueva rama llamada "feature-materiales"
git checkout -b feature-materiales

# Después de hacer tus commits, subir la rama a GitHub
git push origin feature-materiales
```

### ¿Cómo descargo los cambios si edito el código en otra computadora?
```bash
git pull origin main
```

### ¿Qué archivos no se suben a GitHub?
El archivo `.gitignore` ya está configurado para **ignorar**:
* Carpeta `node_modules/` (dependencias de Javascript).
* Carpeta `vendor/` (dependencias de PHP/Laravel).
* Archivo `.env` (credenciales de base de datos, contraseñas y claves de seguridad). **¡Por seguridad, este archivo nunca debe subirse a GitHub!**

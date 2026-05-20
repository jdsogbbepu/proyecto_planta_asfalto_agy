# Plan de Pruebas y Estrategia de Despliegue Offline (Fases 6 y 7)
## Proyecto: Sistema de Control de Materiales e Inventarios (Asphalt-AGY)

Este documento detalla los casos de prueba transaccionales para validar el algoritmo PEPS físico, y la estrategia de instalación y contingencia en el entorno local (Intranet) de la Planta de Asfalto.

---

## 1. Plan de Pruebas Transaccionales (Algoritmo PEPS)

El objetivo de las pruebas es asegurar que la lógica de inventario físico descuente correctamente los lotes según el orden cronológico de entrada y mantenga la atomicidad de la base de datos.

### 1.1. Escenario de Prueba 1: Egreso Menor o Igual al Lote Más Antiguo
* **Condición Inicial:**
  * Lote A (Fecha: 01/05/2026): 50.00 $m^3$ de Arena para el Proyecto "Renueva Vial".
  * Lote B (Fecha: 05/05/2026): 100.00 $m^3$ de Arena para el Proyecto "Renueva Vial".
  * Stock Total del Proyecto: 150.00 $m^3$.
* **Acción:** Registrar una salida de 30.00 $m^3$ de Arena para el Proyecto "Renueva Vial".
* **Resultado Esperado:**
  * El sistema descuenta 30.00 $m^3$ del Lote A.
  * Lote A queda con saldo de 20.00 $m^3$.
  * Lote B se mantiene intacto con 100.00 $m^3$.
  * Se registra un registro en `detalle_salida` apuntando al Lote A por 30.00 $m^3$.

### 1.2. Escenario de Prueba 2: Egreso que Agota el Lote Más Antiguo y Afecta al Siguiente (Descuento Fraccionado)
* **Condición Inicial:**
  * Lote A (Fecha: 01/05/2026): 20.00 $m^3$ de Arena para el Proyecto "Renueva Vial".
  * Lote B (Fecha: 05/05/2026): 100.00 $m^3$ de Arena para el Proyecto "Renueva Vial".
  * Stock Total del Proyecto: 120.00 $m^3$.
* **Acción:** Registrar una salida de 50.00 $m^3$ de Arena para el Proyecto "Renueva Vial".
* **Resultado Esperado:**
  * El Lote A se agota por completo (saldo `cantidad_actual_lote = 0.00`).
  * Los 30.00 $m^3$ restantes se descuentan del Lote B (saldo `cantidad_actual_lote = 70.00`).
  * Se crean dos registros en `detalle_salida`: uno vinculado al Lote A por 20.00 $m^3$ y otro al Lote B por 30.00 $m^3$.

### 1.3. Escenario de Prueba 3: Intento de Salida con Stock Insuficiente
* **Condición Inicial:**
  * Lote A (Fecha: 01/05/2026): 10.00 $m^3$ de Arena para el Proyecto "Renueva Vial".
  * Stock Total del Proyecto: 10.00 $m^3$.
* **Acción:** Registrar una salida de 15.00 $m^3$ de Arena para el Proyecto "Renueva Vial".
* **Resultado Esperado:**
  * El sistema detecta que la cantidad solicitada (15.00) supera el stock disponible (10.00).
  * Se cancela la transacción (`ROLLBACK`).
  * No se altera la base de datos (Lote A se mantiene en 10.00 $m^3$).
  * Se muestra un mensaje de alerta: "Stock insuficiente para realizar el despacho".

---

## 2. Estrategia de Despliegue Offline en Intranet (Fase 7)

Dado que la planta de asfalto no cuenta con conexión a internet estable, el sistema se ejecutará de forma local simulando un entorno de servidor web local.

### 2.1. Arquitectura de Despliegue

```
                                  RED LOCAL (INTRANET)
 ┌───────────────────────┐         ┌───────────────────────┐
 │ Servidor de Planta    │         │ PC Balanza / Oficina  │
 │ (Windows + XAMPP)     │◄───────►│ (Cliente Navegador)   │
 │ IP: 192.168.1.100     │         │ URL: http://192.168...│
 └───────────────────────┘         └───────────────────────┘
```

### 2.2. Pasos para la Puesta en Marcha en Producción
1. **Compilación de Assets:** Ejecutar `npm run build` en la máquina de desarrollo. Esto compila todos los scripts de Vue 3, Inertia y Tailwind CSS en archivos estáticos JS/CSS dentro de la carpeta `public/build/` de Laravel, eliminando la necesidad de NodeJS en el servidor de la planta.
2. **Copia del Proyecto:** Copiar la carpeta del proyecto a la ruta `C:\xampp\htdocs\planta_asfalto_agy\` en la máquina que actuará como servidor.
3. **Base de Datos:** Importar el archivo SQL final en el phpMyAdmin del servidor.
4. **Configuración del Entorno (.env):** Modificar el archivo `.env` de Laravel en el servidor estableciendo `APP_ENV=production`, `APP_DEBUG=false`, y las credenciales de la base de datos local.
5. **Habilitación en Red Local (Apache):**
   * Configurar el archivo `httpd-vhosts.conf` de Apache para que apunte a `C:\xampp\htdocs\planta_asfalto_agy\public`.
   * Permitir el tráfico en el puerto 80 en el Firewall de Windows del servidor.
   * Otros computadores de la planta accederán ingresando la IP local del servidor en su navegador (ej. `http://192.168.1.100`).

### 2.3. Plan de Respaldos (Backup) y Contingencia
Para evitar pérdidas de información por fallos eléctricos o del sistema, se configurará una tarea programada en Windows en el servidor:
* **Script de Backup (`backup_db.bat`):**
  ```bat
  @echo off
  set BACKUP_DIR=C:\db_backups
  set MYSQL_BIN=C:\xampp\mysql\bin
  set DATE=%date:~10,4%-%date:~4,2%-%date:~7,2%
  %MYSQL_BIN%\mysqldump -u root db_asfalto_peps > %BACKUP_DIR%\backup_%DATE%.sql
  ```
* **Frecuencia:** Se ejecutará de forma automática todos los días a las 18:00 (cierre de operaciones de la planta) utilizando el Programador de Tareas de Windows.

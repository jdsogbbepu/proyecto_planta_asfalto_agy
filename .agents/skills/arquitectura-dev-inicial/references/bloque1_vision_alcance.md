# Bloque 1: Visión y Alcance

**Propósito:** Definir el problema, los usuarios, los límites del proyecto y aspectos legales.

Preguntas (con ejemplos y sugerencias):

1. **¿Qué problema concreto resuelve este sistema?**  
   _Ejemplo:_ "Actualmente llevamos el control de producción en hojas de cálculo y perdemos trazabilidad de lotes."  
   _Sugerencia:_ Piensa en el dolor actual que solucionará el software.

2. **¿Quiénes serán los usuarios finales?**  
   _Ejemplo:_ "Operarios de máquinas, supervisores de turno, gerente de planta, administradores."  
   _Sugerencia:_ Diferencia entre roles (admin, editor, solo lectura).

3. **¿Qué NO debe hacer el sistema? (límites claros)**  
   _Ejemplo:_ "No debe gestionar nóminas, no debe enviar correos automáticos, no debe integrarse con RRHH."  
   _Sugerencia:_ Esto evita el "scope creep" (expansión no controlada del alcance).

4. **¿Hay algún requisito legal o normativo que el sistema deba cumplir?**  
   _Ejemplos:_ "Facturación electrónica (CFDI), cumplir GDPR (protección de datos), normativa ISO 9001 de calidad, registro de trazabilidad obligatorio."  
   _Si el usuario no sabe_, puedes decir: "Podemos dejarlo como 'no aplica' o 'por definir'."

5. **¿Cuántos usuarios concurrentes esperas aproximadamente?**  
   _Ejemplos:_ "Menos de 10 (baja), entre 10 y 100 (media), más de 100 (alta)".  
   _Sugerencia:_ Esto afecta la arquitectura y el hosting. Si no lo sabe, sugiere empezar con estimación baja y escalar.

6. **¿Qué nivel de seguridad se requiere?**  
   _Ejemplos:_
    - Básico: autenticación con usuario/contraseña, HTTPS.
    - Medio: autenticación 2FA, registro de accesos, roles.
    - Alto: cifrado de datos sensibles, auditoría completa, protección contra inyecciones.  
      _Sugerencia:_ Puedes proponer "medio" como estándar y ajustar.

**Instrucción:** Realiza las preguntas en orden. Si alguna respuesta es vaga, pide un ejemplo concreto.

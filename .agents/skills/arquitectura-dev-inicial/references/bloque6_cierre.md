# Bloque 6: Confirmación y Siguientes Pasos

**Propósito:** Verificar que no haya olvidos, corregir errores y decidir el flujo posterior.

Preguntas:

1. **¿Hay alguna otra observación o restricción que no hayamos cubierto?**  
   _Ejemplo:_ "El sistema debe funcionar sin internet en la planta (modo offline)", "La interfaz debe ser minimalista, sin colores vivos".  
   _Anota todo lo que diga._

2. **Una vez respondido todo, ¿qué prefieres que haga el agente?**
    - Opciones:
        - (a) Generar automáticamente un documento de visión y alcance (completo).
        - (b) Proponer un plan de fases detallado (usando la skill `plan-desarrollo-9-fases`).
        - (c) Pasar directamente a preguntar sobre diagramas y casos de uso (sin código).  
          _Sugerencia:_ Recomienda la opción (b) si ya tienes la skill de 9 fases, o (a) si quieres un documento base.

3. **¿Confirmas que todas las respuestas son correctas?**  
   _Debe responder "Sí" o "No, corrige X". Si corrige, actualiza el archivo._

**Instrucción final:** Una vez confirmado, guarda el archivo `docs/analisis_inicial.md` definitivo y muestra un resumen. Luego, di la frase de transición:

> "Excelente. He guardado el análisis en `docs/analisis_inicial.md`. Según tu elección, ahora procederé a [lo que eligió en pregunta 2]. ¿Quieres que active la skill `plan-desarrollo-9-fases` (si aplica) o prefieres que te guíe manualmente?"

**No generes código.** Si el usuario pide código, recuerda la Regla de Oro.

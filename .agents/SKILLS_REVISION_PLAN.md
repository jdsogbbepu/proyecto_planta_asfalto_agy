# Plan de Revisión y Optimización de Código

Guía de uso de las skills instaladas para revisar, limpiar y optimizar tu proyecto web.

---

## Skills Disponibles

| Skill                           | Propósito                                 | Orden Recomendado |
| ------------------------------- | ----------------------------------------- | ----------------- |
| `diagnose`                      | Diagnóstico de bugs y problemas difíciles | 1º                |
| `vercel-react-best-practices`   | Optimización de rendimiento React/Next.js | 2º                |
| `error-handling-patterns`       | Revisión de manejo de errores             | 3º                |
| `systematic-debugging`          | Debugging sistemático de problemas        | 4º                |
| `improve-codebase-architecture` | Mejora de arquitectura y estructura       | 5º                |
| `caveman`                       | Modo compressión para reducir tokens      | Opcional          |

---

## Flujo de Trabajo Recomendado

### Sesión 1: Diagnóstico Inicial (30-60 min)

**Skill:** `diagnose`

**Prompt para activar:**

```
Usa la skill "diagnose" para revisar mi codebase. Busca:
1. Bugs o errores existentes
2. Problemas de lógica
3. Código que pueda fallar en edge cases
```

**Qué hace:**

- Crea un loop de feedback para reproducir problemas
- Formula hipótesis y las testa
- No propone cambios hasta confirmar el problema

---

### Sesión 2: Optimización React/Next.js (45-90 min)

**Skill:** `vercel-react-best-practices`

**Prompt para activar:**

```
Usa la skill "vercel-react-best-practices" para revisar mi código React/Next.js.
Enfócate en:

1. Eliminating Waterfalls (CRITICAL)
2. Bundle Size Optimization (CRITICAL)
3. Re-render Optimization (MEDIUM)
4. JavaScript Performance (LOW-MEDIUM)
```

**Qué hace:**

- Revisa las 70 reglas de optimización de Vercel
- Identifica problemas de rendimiento en componentes
- Sugiere patrones de data fetching

---

### Sesión 3: Manejo de Errores (30-45 min)

**Skill:** `error-handling-patterns`

**Prompt para activar:**

```
Usa la skill "error-handling-patterns" para revisar:
1. Cómo se manejan los errores en mi código
2. Si hay try-catch apropiados
3. Si los errores tienen contexto suficiente
4. Patrones de retry o circuit breaker
```

**Qué hace:**

- Identifica empty catch blocks
- Verifica que errores tengan stack traces y metadata
- Revisa si hay fallbacks apropiados

---

### Sesión 4: Debugging Sistemático (45-60 min)

**Skill:** `systematic-debugging`

**Prompt para activar:**

```
Usa la skill "systematic-debugging" para:
1. Identificar código que puede ser problemático
2. Revisar edge cases no manejados
3. Verificar validaciones de input
```

**Qué hace:**

- Fase 1: Construir loop de feedback
- Fase 2: Reproducir problemas
- Fase 3: Formular hipótesis
- Fase 4: Instrumentar y testear

---

### Sesión 5: Arquitectura y Estructura (60-90 min)

**Skill:** `improve-codebase-architecture`

**Prompt para activar:**

```
Usa la skill "improve-codebase-architecture" para:
1. Revisar la estructura de archivos
2. Identificar acoplamiento excesivo
3. Sugerir mejor organización de componentes
4. Revisar patrones de importación
```

**Qué hace:**

- Analiza dependencias entre módulos
- Identifica violating layers
- Propone拆分 (split) de archivos muy grandes
- Mejora nomenclatura y organización

---

## Uso del Modo Caveman (Opcional)

**Skill:** `caveman`

Úsalo cuando:

- Las respuestas son muy largas
- Quieres ahorrar tokens
- Necesitas respuestas ultra-brev

**Prompt:**

```
Activa modo caveman (full)
```

**Niveles disponibles:**

- `lite` - Profesional pero breve
- `full` - Drop articles, fragmentos OK (default)
- `ultra` - Máxima compresión

---

## Consejos por Tipo de Proyecto

### PHP/Laravel (Backend puro)

- Saltar `vercel-react-best-practices` (no aplica)
- Enfocarse en: `diagnose`, `error-handling-patterns`, `systematic-debugging`

### React/Next.js (Full-stack)

- Seguir el orden recomendado completo
- Priorizar: bundle size + re-renders

### JavaScript Vanilla

- `vercel-react-best-practices` solo reglas de JS (sección 7)
- `error-handling-patterns` para patterns generales

---

## Combinar Skills en Una Sesión

Si quieres hacer múltiples revisiones en una sesión:

```
Usa "diagnose" primero para bugs.
Luego usa "error-handling-patterns" para validar manejo de errores.
Finalmente usa "improve-codebase-architecture" para estructura.
```

**Orden al combinar:**

1. `diagnose` - Problemas primero
2. `error-handling-patterns` - Validación
3. `improve-codebase-architecture` - Estructura
4. `vercel-react-best-practices` - Optimización (al final porque puede encontrar muchos cambios)

---

## Checklist Rápido por Sesión

### Antes de empezar

- [ ] Backup del código (git)
- [ ] Limpiar archivos temporales
- [ ] Definir alcance (todo el proyecto o módulo específico)

### Durante

- [ ] Tomar notas de problemas encontrados
- [ ] Priorizar por impacto
- [ ] Anotar archivos que necesitan cambios

### Después

- [ ] Implementar fixes más críticos primero
- [ ] Testear cambios
- [ ] Documentar decisiones de arquitectura

---

## Ejemplo de Prompt Completo para Revisión

```
=== REVISIÓN COMPLETA ===

1. Usa "diagnose" para encontrar bugs en src/
2. Usa "error-handling-patterns" para validar manejo de errores en src/
3. Usa "improve-codebase-architecture" para revisar estructura

Scope: src/, excludes: node_modules/, vendor/
```

---

## Notas Importantes

- **No ejecutar todas las skills a la vez** - puede ser abrumador y producir muchas sugerencias contradictorias
- **Un proyecto a la vez** - Completa una sesión antes de pasar a la siguiente
- **Guardar resultados** - Mantén un log de problemas encontrados para seguimiento
- **Regresión testing** - Después de cambios, verifica que todo siga funcionando

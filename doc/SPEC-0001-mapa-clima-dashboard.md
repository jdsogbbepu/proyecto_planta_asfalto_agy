# SPEC: Widget de Mapa y Clima para Dashboard
**Fecha:** 2026-06-04
**Proyecto:** Planta de Asfalto - Asphalt-AGY
**Autor:** Sistema Asphalt-AGY
**Ubicación:** Card "Telemetría de Planta y Sensores" en Dashboard

---

## 1. Resumen Ejecutivo

Reemplazar la sección de telemetría simulada del dashboard con dos nuevos componentes informativos: un **mapa interactivo de la ciudad de El Alto** y un **widget de clima actualizado en tiempo real**. Ambos mantienen la estética industrial oscura existente del sistema.

---

## 2. Objetivos

- Proporcionar información geográfica visual de la ciudad de El Alto para contextualizar las operaciones de la planta de asfalto
- Mostrar clima actual y pronóstico semanal para planificación de actividades
- Mantener coherencia visual con el tema industrial oscuro existente
- No interferir con la funcionalidad actual del dashboard

---

## 3. Componente: Mapa Interactivo de El Alto

### 3.1 Descripción General
Minimapa interactivo centrado en El Alto, La Paz, Bolivia, con navegación por distritos. Permite visualizar la distribución geográfica de la ciudad y hacer zoom para ver detalles.

### 3.2 Especificaciones Técnicas
- **Librería:** Leaflet.js + Vue-Leaflet
- **Tiles:** OpenStreetMap (gratuito, sin API key)
- **Coordenadas iniciales:** -16.5, -68.15
- **Zoom inicial:** 13 (vista ciudad completa)
- **Zoom distrito:** 15-16 (detalle por distrito)

### 3.3 Datos Geográficos
- **GeoJSON Distritos:** El Alto tiene aproximadamente 15 distritos
- **Nomenclatura visual:** D1, D2, D3... (etiquetas cortas)
- **CRS:** EPSG:4326 (WGS84)
- **Fuente GeoJSON:** GeoJSON.io o OpenStreetMap Overpass

### 3.4 Interacciones

| Acción | Resultado |
|--------|-----------|
| Hover sobre distrito | Tooltip: nombre completo del distrito, avenidas principales, puntos clave |
| Click sobre distrito | Zoom al distrito + breadcrumb "El Alto > D3 > [Nombre Distrito]" |
| Marcadores fijos | Planta de asfalto, almacenes principales, obras activas |
| Control zoom | Botones +/- estándar Leaflet |

### 3.5 Indicadores Visuales
- **Zona Urbana vs Rural:** Borde punteado para区分 zona altiplánica/rural
- **Distrito hover:** fill `#f27b00` con 30% opacity
- **Distrito activo:** fill `#f27b00` con 50% opacity

### 3.6 Tooltip por Distrito (ejemplo)
```
┌─────────────────────────────┐
│ D3 - Ciudad Satélite        │
├─────────────────────────────┤
│ 📍 Zona: Urbana             │
│ 🛣️ Av. Principal: Av. 6 de │
│    Marzo, Av. Jorge Caetro   │
│ 🏢 Puntos: Estadio,         │
│    Centro Comercial         │
└─────────────────────────────┘
```

### 3.7 Breadcrumb de Navegación
Al hacer zoom en un distrito:
```
El Alto > D3 > Ciudad Satélite
    ↓        ↓          ↓
 [ciudad] [distrito] [zona]
 [clickable para volver]
```

---

## 4. Componente: Widget de Clima

### 4.1 Descripción General
Panel compacto mostrando clima actual y pronóstico de 7 días para El Alto, actualizado automáticamente.

### 4.2 API
- **Proveedor:** Open-Meteo (gratuito, sin API key)
- **Endpoint:** `https://api.open-meteo.com/v1/forecast`
- **Parámetros:**
  - latitude=-16.5
  - longitude=-68.15
  - hourly (para hoy/mañana)
  - daily (para 7 días)
  - timezone=auto
  - wind_speed_unit=kmh
  - current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m

### 4.3 Datos Mostrados

#### Clima Actual
| Campo | Descripción |
|-------|-------------|
| Temperatura | °C actual |
| Icono | Código WMO del clima |
| Humedad | % relativa |
| Viento | km/h |
| Sensación térmica | °C |

#### Pronóstico Horizontal (7 días)
| Día | Icono | Temp Máx | Temp Mín |
|-----|-------|----------|----------|
| Hoy | ☀️/☁️/🌧️ | XX°C | XX°C |
| Mañana | ... | XX°C | XX°C |
| +2 | ... | XX°C | XX°C |
| +3 | ... | XX°C | XX°C |
| +4 | ... | XX°C | XX°C |
| +5 | ... | XX°C | XX°C |
| +6 | ... | XX°C | XX°C |

### 4.4 Estilo Visual
- Contenedor: `bg-[#0e1113] border border-[#2d3139]`
- Día activo (Hoy): borde `#f27b00`
- Iconos: SVG o emoji compatible con el tema
- Texto temperatura: blanco/gris (`#e1e6eb`, `#9ca3af`)

---

## 5. Layout en Dashboard

### 5.1 Estructura Final
Reemplazar la card "Telemetría de Planta y Sensores" (col-span-8) con:

```
┌─────────────────────────────────────────────────────┐
│  Clima: [HOY] [Mañana] [Jue 6] [Vie 7] ... [Mié 11] │
├─────────────────────────────────────────────────────┤
│                    🌡️ 18°C  💧 45%  💨 12km/h       │
├─────────────────────────────────────────────────────┤
│                                                     │
│              🗺️ MAPA INTERACTIVO                    │
│        (Hover: tooltip, Click: zoom distrito)        │
│                                                     │
│  [ breadcrumb: El Alto > D3 > Ciudad Satélite ]    │
└─────────────────────────────────────────────────────┘
```

### 5.2 Responsive
- Desktop: lado a lado con otros componentes si hay espacio
- Tablet/Mobile: pila vertical (clima arriba, mapa abajo)

---

## 6. Archivos a Crear/Modificar

| Archivo | Acción | Descripción |
|---------|--------|-------------|
| `resources/js/Components/CityMap.vue` | Crear | Componente Leaflet con GeoJSON distritos |
| `resources/js/Components/WeatherWidget.vue` | Crear | Widget clima con consumo API Open-Meteo |
| `resources/js/Pages/Dashboard.vue` | Modificar | Integrar CityMap y WeatherWidget |
| `resources/js/app.js` | Modificar | Registrar VueLeaflet |
| `resources/js/composables/useWeather.js` | Crear | Composables para consumir API clima |
| `package.json` | Modificar | Agregar leaflet, @vue-leaflet |
| `public/geojson/el-alto-districts.json` | Crear | GeoJSON con límites de distritos |

---

## 7. Dependencias

### NPM
```json
{
  "leaflet": "^1.9.4",
  "@vue-leaflet/vue-leaflet": "^0.10.1"
}
```

### CDN (alternativo, no recomendado)
- Leaflet CSS: `https://unpkg.com/leaflet@1.9.4/dist/leaflet.css`
- Leaflet JS: `https://unpkg.com/leaflet@1.9.4/dist/leaflet.js`

---

## 8. Mapa de Tareas de Implementación

### Fase 1: Configuración
- [x] Instalar dependencias npm (leaflet, vue-leaflet)
- [x] Registrar VueLeaflet en app.js
- [x] Importar CSS de Leaflet en app.js

### Fase 2: GeoJSON Distritos
- [x] Obtener o crear GeoJSON de distritos de El Alto
- [x] Guardar en `public/geojson/el-alto-districts.json`
- [x] Verificar que cargue correctamente

### Fase 3: Componente CityMap
- [x] Crear CityMap.vue con Leaflet
- [x] Implementar carga de GeoJSON
- [x] Estilizar distritos con colores industriales
- [x] Implementar hover tooltips
- [x] Implementar click para zoom
- [x] Agregar marcadores fijos (planta, almacenes)
- [x] Agregar breadcrumb de navegación
- [ ] Agregar indicador zona urbana/rural

### Fase 4: Componente WeatherWidget
- [x] Crear useWeather.js composable
- [x] Consumir API Open-Meteo
- [x] Crear WeatherWidget.vue
- [x] Mostrar clima actual
- [x] Implementar pronóstico horizontal 7 días
- [x] Estilizar con tema industrial

### Fase 5: Integración Dashboard
- [x] Reemplazar contenido card Telemetría
- [x] Organizar layout: clima arriba, mapa abajo
- [x] Probar responsive
- [x] Verificar que no rompa otros componentes

### Fase 6: Testing y Ajustes
- [ ] Probar hover/click en mapa
- [ ] Probar carga de clima
- [ ] Verificar estilos en mobile
- [ ] Ajustar colores/bordes si necesario

---

## 9. Consideraciones Técnicas

### 9.1 Rendimiento
- Lazy loading del mapa (solo cargar cuando sea visible)
- Cachear respuesta del clima (actualizar cada 30 min)
- GeoJSON cargado una sola vez

### 9.2 Manejo de Errores
- Si GeoJSON no carga: mostrar mapa base sin distritos
- Si API clima falla: mostrar "Clima no disponible"
- Timeout en llamadas API: 10 segundos

### 9.3 Accesibilidad
- Tooltips accesibles por teclado
- Contraste de colores suficiente
- Iconos con aria-labels

---

## 10. Alternativas Consideradas

### Mapa
| Alternativa | Pros | Contras |
|-------------|------|---------|
| Google Maps API | Mejor calidad | Requiere API key, costo |
| Mapbox | Mejor diseño | Requiere API key, costo |
| **Leaflet + OSM** | Gratis, sin key | Styling limitado |

### Clima
| Alternativa | Pros | Contras |
|-------------|------|---------|
| Open-Meteo | Gratis, sin key, bueno | Pocos datos históricos |
| WeatherAPI | Más datos | Requiere API key |
| OpenWeather | Popular | API key requerida |

---

## 11. Aprobaciones

| Rol | Nombre | Fecha | Estado |
|-----|--------|-------|--------|
| Desarrollador | Sistema | 2026-06-04 | ✅ Aprobado |
| Stakeholder | [Nombre empresa] | Pendiente | ⏳ Pendiente |

---

## Historial de Cambios

| Versión | Fecha | Autor | Cambios |
|---------|-------|-------|---------|
| 1.0 | 2026-06-04 | Sistema | Creación del spec |
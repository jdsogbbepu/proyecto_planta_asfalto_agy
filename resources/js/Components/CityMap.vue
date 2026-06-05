<script setup>
import 'leaflet/dist/leaflet.css';
import { ref, onMounted, computed, onErrorCaptured } from 'vue';
import { LMap, LTileLayer, LGeoJson, LMarker, LPopup } from '@vue-leaflet/vue-leaflet';
import L from 'leaflet';

const CENTER = [-16.51, -68.21];
const INITIAL_ZOOM = 13;

const mapRef = ref(null);
const geoJsonData = ref(null);
const selectedDistrict = ref(null);
const loading = ref(true);
const error = ref(null);
const mapError = ref(false);

const mainMarkers = [
    { id: 1, coords: [-16.4928, -68.1512], title: 'Planta de Asfalto', description: 'Ubicación principal' },
];

const districtColors = [
    '#f27b00', '#3b82f6', '#10b981', '#8b5cf6', '#ec4899',
    '#f59e0b', '#06b6d4', '#84cc16', '#f43f5e', '#6366f1',
    '#14b8a6', '#eab308', '#a855f7', '#22c55e', '#3b82f6'
];

const getDistrictColor = (index) => districtColors[index % districtColors.length];

const style = (feature, index) => {
    if (!feature?.properties) return { color: '#f27b00', weight: 1.5, opacity: 0.9, fillColor: '#f27b00', fillOpacity: 0.2 };
    const isSelected = selectedDistrict.value === feature.properties.c_dist_mun;
    const baseColor = getDistrictColor(index);
    return {
        color: isSelected ? '#ff9426' : baseColor,
        weight: isSelected ? 3 : 1.5,
        opacity: 0.9,
        fillColor: baseColor,
        fillOpacity: isSelected ? 0.45 : 0.2,
    };
};

const highlightStyle = (feature, index) => {
    const baseColor = getDistrictColor(index);
    return {
        color: baseColor,
        weight: 2.5,
        opacity: 1,
        fillColor: baseColor,
        fillOpacity: 0.4,
    };
};

const defaultIcon = L.icon({
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
});

const fetchGeoJson = async () => {
    try {
        loading.value = true;
        error.value = null;
        const response = await fetch('/geojson/el-alto-districts.json');
        if (!response.ok) throw new Error('Error al cargar GeoJSON');
        const data = await response.json();
        if (!data?.features) throw new Error('Formato de GeoJSON inválido');
        geoJsonData.value = data;
    } catch (e) {
        console.warn('GeoJSON load error:', e);
        error.value = e.message;
        geoJsonData.value = { type: 'FeatureCollection', features: [] };
    } finally {
        loading.value = false;
    }
};

const onEachFeature = (feature, layer, index) => {
    if (!feature?.properties) return;
    const props = feature.properties;
    const districtName = props.c_dist_mun || `Distrito ${index + 1}`;

    layer.on({
        mouseover: (e) => {
            e.target.setStyle(highlightStyle(feature, index));
            e.target.bringToFront();
        },
        mouseout: (e) => {
            const isSelected = selectedDistrict.value === districtName;
            if (!isSelected) {
                e.target.setStyle(style(feature, index));
            }
        },
        click: (e) => {
            if (mapRef.value?.leafletObject) {
                mapRef.value.leafletObject.fitBounds(e.target.getBounds(), { padding: [50, 50], maxZoom: 15 });
            }
            selectedDistrict.value = districtName;
        },
    });

    const color = getDistrictColor(index);
    const tooltipContent = `
        <div style="font-family: system-ui, sans-serif; padding: 6px 10px; min-width: 160px; border-left: 3px solid ${color};">
            <div style="font-size: 13px; font-weight: 700; color: ${color}; margin-bottom: 4px;">${districtName}</div>
            <div style="font-size: 11px; color: #666;">Población: <strong style="color: #333;">${parseInt(props.poblacion || 0).toLocaleString()}</strong></div>
            <div style="font-size: 11px; color: #666;">Área: <strong style="color: #333;">${parseFloat(props.area_km2 || 0).toFixed(2)} km²</strong></div>
        </div>
    `;
    layer.bindTooltip(tooltipContent, {
        sticky: true,
        className: 'district-tooltip',
        direction: 'top',
        offset: [0, -10]
    });
};

const resetView = () => {
    if (mapRef.value?.leafletObject) {
        mapRef.value.leafletObject.flyTo(CENTER, INITIAL_ZOOM, { duration: 0.8 });
    }
    selectedDistrict.value = null;
};

const districts = computed(() => {
    if (!geoJsonData.value?.features?.length) return [];
    return geoJsonData.value.features
        .map((f, i) => ({
            name: f.properties?.c_dist_mun || `Distrito ${i + 1}`,
            population: f.properties?.poblacion || '0',
            area: f.properties?.area_km2 ? parseFloat(f.properties.area_km2).toFixed(2) : '0',
            color: getDistrictColor(i),
            index: i,
        }))
        .sort((a, b) => parseInt(b.population) - parseInt(a.population));
});

const selectedDistrictData = computed(() => {
    if (!selectedDistrict.value) return null;
    return districts.value.find(d => d.name === selectedDistrict.value);
});

const maxPopulation = computed(() => {
    const populations = districts.value.map(d => parseInt(d.population) || 1);
    return populations.length ? Math.max(...populations) : 1;
});

const handleMapError = () => {
    mapError.value = true;
    console.warn('Map rendering error - showing fallback');
};

onMounted(() => {
    fetchGeoJson();
});
</script>

<template>
    <div class="bg-[#0e1113] border border-[#2d3139] rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-[#2d3139]/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-white">Distritos de El Alto</span>
                <span class="text-[9px] text-industrial-muted bg-[#1b1e22] px-2 py-1 rounded border border-[#2d3139]">
                    {{ districts.length }} zonas
                </span>
            </div>
            <div class="flex items-center gap-2">
                <button
                    v-if="selectedDistrict"
                    @click="resetView"
                    class="text-[10px] text-[#f27b00] hover:text-[#ff9426] font-semibold transition flex items-center gap-1"
                >
                    <span>↩</span> Vista completa
                </button>
            </div>
        </div>

        <div class="flex" style="height: 400px;">
            <div class="relative flex-1">
                <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-[#0e1113] z-10">
                    <div class="text-center">
                        <div class="animate-pulse text-2xl mb-2">🗺️</div>
                        <p class="text-xs text-industrial-muted">Cargando mapa...</p>
                    </div>
                </div>

                <div v-if="error && !geoJsonData?.features?.length" class="absolute inset-0 flex items-center justify-center bg-[#0e1113] z-10">
                    <div class="text-center p-4">
                        <div class="text-2xl mb-2">⚠️</div>
                        <p class="text-xs text-red-400">{{ error }}</p>
                    </div>
                </div>

                <l-map
                    ref="mapRef"
                    :zoom="INITIAL_ZOOM"
                    :center="CENTER"
                    :use-global-leaflet="false"
                    class="h-full w-full"
                    :options="{ zoomControl: true, attributionControl: false }"
                    @error="handleMapError"
                >
                    <l-tile-layer
                        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                        :attribution="'© OpenStreetMap'"
                        :options="{ maxZoom: 18 }"
                    />

                    <l-geo-json
                        v-if="geoJsonData?.features?.length"
                        :geojson="geoJsonData"
                        :options-style="(f) => style(f, geoJsonData.features.indexOf(f))"
                        :options-on-each-feature="(f, l) => onEachFeature(f, l, geoJsonData.features.indexOf(f))"
                    />

                    <l-marker
                        v-for="marker in mainMarkers"
                        :key="marker.id"
                        :lat-lng="marker.coords"
                        :icon="defaultIcon"
                    >
                        <l-popup>
                            <div class="text-center p-1">
                                <strong style="color: #f27b00; font-size: 13px;">{{ marker.title }}</strong>
                                <p style="font-size: 11px; color: #666;">{{ marker.description }}</p>
                            </div>
                        </l-popup>
                    </l-marker>
                </l-map>

                <div class="absolute bottom-3 left-3 z-[1000] bg-[#1b1e22]/95 backdrop-blur px-3 py-2 rounded-lg border border-[#2d3139]">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-[#f27b00] animate-pulse"></div>
                        <p class="text-[9px] text-industrial-muted">El Alto, La Paz - Bolivia</p>
                    </div>
                </div>

                <div class="absolute top-3 right-3 z-[1000] bg-[#1b1e22]/95 backdrop-blur px-2 py-1 rounded border border-[#2d3139]">
                    <p class="text-[9px] text-industrial-muted font-mono">4,000 msnm</p>
                </div>
            </div>

            <div class="w-72 border-l border-[#2d3139] bg-[#1b1e22]/30 overflow-y-auto">
                <div v-if="selectedDistrict && selectedDistrictData" class="p-4">
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-3 h-3 rounded" :style="{ backgroundColor: selectedDistrictData.color }"></div>
                            <h3 class="text-base font-bold text-white">{{ selectedDistrict }}</h3>
                        </div>
                        <p class="text-xs text-industrial-muted">Seleccionado</p>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-[#0e1113] rounded-xl p-4 border border-[#2d3139]">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] text-industrial-muted uppercase tracking-wider">Población</span>
                                <span class="text-xs text-[#f27b00]">hab.</span>
                            </div>
                            <p class="text-2xl font-extrabold font-mono text-white tracking-tight">
                                {{ parseInt(selectedDistrictData.population).toLocaleString() }}
                            </p>
                        </div>

                        <div class="bg-[#0e1113] rounded-xl p-4 border border-[#2d3139]">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] text-industrial-muted uppercase tracking-wider">Área Territorial</span>
                                <span class="text-xs text-[#f27b00]">km²</span>
                            </div>
                            <p class="text-2xl font-extrabold font-mono text-white tracking-tight">
                                {{ selectedDistrictData.area }}
                            </p>
                        </div>

                        <div class="bg-[#0e1113] rounded-xl p-4 border border-[#2d3139]">
                            <span class="text-[10px] text-industrial-muted uppercase tracking-wider">Densidad</span>
                            <p class="text-lg font-bold font-mono text-white mt-1">
                                {{ (parseInt(selectedDistrictData.population) / parseFloat(selectedDistrictData.area || 1)).toFixed(0) }}
                                <span class="text-xs text-industrial-muted font-normal"> hab/km²</span>
                            </p>
                        </div>
                    </div>

                    <button
                        @click="resetView"
                        class="w-full mt-4 bg-[#f27b00]/10 hover:bg-[#f27b00]/20 text-[#f27b00] text-xs font-semibold py-2.5 px-4 rounded-xl border border-[#f27b00]/20 transition"
                    >
                        Cerrar detalle
                    </button>
                </div>

                <div v-else class="p-4">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span>Ranking por Población</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#f27b00]"></span>
                    </h3>

                    <div class="space-y-2">
                        <div
                            v-for="(district, idx) in districts.slice(0, 10)"
                            :key="district.name"
                            class="group cursor-pointer"
                            @click="() => {
                                selectedDistrict = district.name;
                                if (mapRef?.leafletObject && geoJsonData?.features[district.index]) {
                                    const bounds = L.geoJSON(geoJsonData.features[district.index]).getBounds();
                                    mapRef.leafletObject.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
                                }
                            }"
                        >
                            <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-[#0e1113] transition">
                                <span class="text-[10px] font-bold text-industrial-muted w-4">{{ idx + 1 }}</span>
                                <span class="w-2 h-2 rounded-full flex-shrink-0" :style="{ backgroundColor: district.color }"></span>
                                <span class="text-xs text-industrial-foreground group-hover:text-white transition flex-1 truncate">
                                    {{ district.name }}
                                </span>
                                <span class="text-[10px] font-mono text-industrial-muted">
                                    {{ parseInt(district.population).toLocaleString() }}
                                </span>
                            </div>
                            <div class="h-1 bg-[#1b1e22] rounded-full mx-2 overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all duration-500"
                                    :style="{
                                        width: `${(parseInt(district.population) / maxPopulation) * 100}%`,
                                        backgroundColor: district.color,
                                        opacity: 0.6
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-[#2d3139]">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-industrial-muted uppercase tracking-wider">Total</span>
                            <span class="text-sm font-bold text-white font-mono">
                                {{ districts.reduce((sum, d) => sum + parseInt(d.population) || 0, 0).toLocaleString() }}
                                <span class="text-[10px] text-industrial-muted font-normal"> hab.</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.district-tooltip {
    background: #1b1e22 !important;
    border: 1px solid #2d3139 !important;
    border-radius: 10px !important;
    padding: 0 !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4) !important;
}
.district-tooltip::before {
    display: none !important;
}
.leaflet-popup-content-wrapper {
    background: #1b1e22 !important;
    border-radius: 10px !important;
    border: 1px solid #2d3139;
    color: #e1e6eb !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}
.leaflet-popup-tip {
    background: #1b1e22 !important;
}
.leaflet-popup-close-button {
    color: #9ca3af !important;
}
</style>
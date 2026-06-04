<script setup>
import { ref, onMounted, computed } from 'vue';
import { LMap, LTileLayer, LGeoJson, LMarker, LPopup } from '@vue-leaflet/vue-leaflet';
import L from 'leaflet';

const CENTER = [-16.51, -68.21];
const INITIAL_ZOOM = 13;

const mapRef = ref(null);
const geoJsonData = ref(null);
const selectedDistrict = ref(null);
const hoveredDistrict = ref(null);
const loading = ref(true);
const error = ref(null);

const mainMarkers = [
    { id: 1, coords: [-16.4928, -68.1512], title: 'Planta de Asfalto', description: 'Planta principal' },
];

const districtColors = [
    '#f27b00', '#0ea5e9', '#10b981', '#8b5cf6', '#ec4899',
    '#f59e0b', '#06b6d4', '#84cc16', '#f43f5e', '#6366f1',
    '#14b8a6', '#eab308', '#a855f7', '#22c55e', '#3b82f6'
];

const getDistrictColor = (index) => districtColors[index % districtColors.length];

const style = (feature, index) => ({
    color: selectedDistrict.value === feature.properties?.c_dist_mun ? '#ff9426' : getDistrictColor(index),
    weight: selectedDistrict.value === feature.properties?.c_dist_mun ? 3 : 2,
    opacity: 0.9,
    fillColor: getDistrictColor(index),
    fillOpacity: selectedDistrict.value === feature.properties?.c_dist_mun ? 0.4 : 0.15,
});

const highlightStyle = (feature, index) => ({
    color: getDistrictColor(index),
    weight: 3,
    opacity: 1,
    fillColor: getDistrictColor(index),
    fillOpacity: 0.4,
});

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
        const response = await fetch('/geojson/el-alto-districts.json');
        if (!response.ok) throw new Error('No se pudo cargar el GeoJSON');
        geoJsonData.value = await response.json();
    } catch (e) {
        error.value = e.message;
        console.error('GeoJSON load error:', e);
    } finally {
        loading.value = false;
    }
};

const onEachFeature = (feature, layer, index) => {
    const props = feature.properties || {};
    const districtName = props.c_dist_mun || `Distrito ${index + 1}`;

    layer.on({
        mouseover: (e) => {
            e.target.setStyle(highlightStyle(feature, index));
            e.target.bringToFront();
            hoveredDistrict.value = props;
        },
        mouseout: (e) => {
            const isSelected = selectedDistrict.value === districtName;
            if (!isSelected) {
                e.target.setStyle(style(feature, index));
            }
            hoveredDistrict.value = null;
        },
        click: (e) => {
            if (mapRef.value?.leafletObject) {
                mapRef.value.leafletObject.fitBounds(e.target.getBounds(), { padding: [50, 50], maxZoom: 15 });
            }
            selectedDistrict.value = districtName;
        },
    });

    const tooltipContent = `
        <div style="font-family: system-ui, sans-serif; padding: 4px; min-width: 120px;">
            <strong style="color: ${getDistrictColor(index)}; font-size: 12px;">${districtName}</strong>
        </div>
    `;
    layer.bindTooltip(tooltipContent, { sticky: true, className: 'district-tooltip' });
};

const resetView = () => {
    if (mapRef.value?.leafletObject) {
        mapRef.value.leafletObject.flyTo(CENTER, INITIAL_ZOOM, { duration: 0.8 });
    }
    selectedDistrict.value = null;
};

const districts = computed(() => {
    if (!geoJsonData.value?.features) return [];
    return geoJsonData.value.features.map((f, i) => ({
        name: f.properties?.c_dist_mun || `Distrito ${i + 1}`,
        population: f.properties?.poblacion || 'N/A',
        area: f.properties?.area_km2 ? parseFloat(f.properties.area_km2).toFixed(2) : 'N/A',
        color: getDistrictColor(i),
        index: i,
    }));
});

const totalPopulation = computed(() => {
    return districts.value.reduce((sum, d) => {
        return sum + (parseInt(d.population) || 0);
    }, 0);
});

onMounted(() => {
    fetchGeoJson();
});
</script>

<template>
    <div class="bg-[#0e1113] border border-[#2d3139] rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-[#2d3139]/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-white">Mapa de Distritos - El Alto</span>
                <span class="text-[10px] text-industrial-muted bg-[#1b1e22] px-2 py-1 rounded">
                    {{ districts.length }} distritos
                </span>
            </div>
            <button
                v-if="selectedDistrict"
                @click="resetView"
                class="text-[10px] text-[#f27b00] hover:text-[#ff9426] font-semibold transition"
            >
                Ver ciudad completa
            </button>
        </div>

        <div class="flex" style="height: 420px;">
            <div class="relative flex-1">
                <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-[#0e1113] z-10">
                    <div class="text-center">
                        <div class="text-2xl mb-2 animate-pulse">Cargando...</div>
                        <p class="text-xs text-industrial-muted">Inicializando mapa</p>
                    </div>
                </div>

                <div v-if="error && !geoJsonData" class="absolute inset-0 flex items-center justify-center bg-[#0e1113] z-10">
                    <div class="text-center p-4">
                        <div class="text-2xl mb-2">!</div>
                        <p class="text-xs text-red-400 mb-2">{{ error }}</p>
                    </div>
                </div>

                <l-map
                    ref="mapRef"
                    :zoom="INITIAL_ZOOM"
                    :center="CENTER"
                    :use-global-leaflet="false"
                    class="h-full w-full"
                    :options="{ zoomControl: true, attributionControl: false }"
                >
                    <l-tile-layer
                        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                        :attribution="'© OpenStreetMap'"
                        :options="{ maxZoom: 18 }"
                    />

                    <l-geo-json
                        v-if="geoJsonData"
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
                            <div class="text-center">
                                <strong style="color: #f27b00;">{{ marker.title }}</strong>
                                <p class="text-xs text-gray-600">{{ marker.description }}</p>
                            </div>
                        </l-popup>
                    </l-marker>
                </l-map>

                <div class="absolute bottom-3 left-3 z-[1000] bg-[#1b1e22]/95 backdrop-blur px-3 py-2 rounded-lg border border-[#2d3139]">
                    <p class="text-[9px] text-industrial-muted">El Alto, La Paz</p>
                    <p class="text-[10px] text-white font-semibold">{{ districts.length }} Distritos</p>
                </div>
            </div>

            <div class="w-64 border-l border-[#2d3139] bg-[#1b1e22]/50 overflow-y-auto">
                <div v-if="selectedDistrict" class="p-4">
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-white mb-1">{{ selectedDistrict }}</h3>
                        <div class="h-1 w-12 rounded" :style="{ backgroundColor: districts.find(d => d.name === selectedDistrict)?.color }"></div>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-[#0e1113] rounded-lg p-3 border border-[#2d3139]">
                            <span class="text-[10px] text-industrial-muted uppercase tracking-wider">Población</span>
                            <p class="text-lg font-bold text-white font-mono">
                                {{ districts.find(d => d.name === selectedDistrict)?.population?.toLocaleString() || 'N/A' }}
                            </p>
                        </div>

                        <div class="bg-[#0e1113] rounded-lg p-3 border border-[#2d3139]">
                            <span class="text-[10px] text-industrial-muted uppercase tracking-wider">Área</span>
                            <p class="text-lg font-bold text-white font-mono">
                                {{ districts.find(d => d.name === selectedDistrict)?.area || 'N/A' }} km²
                            </p>
                        </div>
                    </div>

                    <button
                        @click="resetView"
                        class="w-full mt-4 bg-[#f27b00]/20 hover:bg-[#f27b00]/30 text-[#f27b00] text-xs font-semibold py-2 px-3 rounded-lg border border-[#f27b00]/30 transition"
                    >
                        Cerrar detalles
                    </button>
                </div>

                <div v-else class="p-4">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Lista de Distritos</h3>
                    <div class="space-y-1">
                        <div
                            v-for="(district, idx) in districts"
                            :key="idx"
                            class="flex items-center gap-2 p-2 rounded-lg hover:bg-[#0e1113] cursor-pointer transition group"
                            @click="() => {
                                selectedDistrict = district.name;
                                if (mapRef?.leafletObject && geoJsonData?.features[idx]) {
                                    const bounds = L.geoJSON(geoJsonData.features[idx]).getBounds();
                                    mapRef.leafletObject.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
                                }
                            }"
                        >
                            <span class="w-2 h-2 rounded-full flex-shrink-0" :style="{ backgroundColor: district.color }"></span>
                            <span class="text-xs text-industrial-foreground group-hover:text-white transition flex-1 truncate">
                                {{ district.name }}
                            </span>
                            <span class="text-[10px] text-industrial-muted font-mono">
                                {{ district.population?.toLocaleString() || 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-[#2d3139]">
                        <span class="text-[10px] text-industrial-muted uppercase tracking-wider">Población Total</span>
                        <p class="text-sm font-bold text-white font-mono">{{ totalPopulation?.toLocaleString() || 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.district-tooltip {
    background: #1b1e22 !important;
    border: 1px solid #f27b00 !important;
    border-radius: 8px !important;
    padding: 8px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important;
}
.district-tooltip::before {
    border-top-color: #f27b00 !important;
}
.leaflet-popup-content-wrapper {
    background: #1b1e22 !important;
    border-radius: 8px !important;
    color: #e1e6eb !important;
}
.leaflet-popup-tip {
    background: #1b1e22 !important;
}
</style>
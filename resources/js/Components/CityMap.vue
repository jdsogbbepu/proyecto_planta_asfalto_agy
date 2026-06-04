<script setup>
import { ref, onMounted } from 'vue';
import { LMap, LTileLayer, LGeoJson, LMarker, LPopup } from '@vue-leaflet/vue-leaflet';
import L from 'leaflet';

const CENTER = [-16.5, -68.15];
const INITIAL_ZOOM = 13;

const mapRef = ref(null);
const geoJsonData = ref(null);
const selectedDistrict = ref(null);
const breadcrumb = ref(['El Alto']);
const loading = ref(true);
const error = ref(null);

const districtInfo = {
    D1: { name: 'D1', fullName: 'Zona Central', avenues: 'Av. 6 de Marzo, Av. Busch', points: 'Plaza principal, Mercado' },
    D2: { name: 'D2', fullName: 'Ciudad Satélite', avenues: 'Av. Jorge Caetro, Av. Kanturani', points: 'Estadio, Centro Comercial' },
    D3: { name: 'D3', fullName: 'Alto Lima', avenues: 'Av. Peru, Av. Ballivián', points: 'Mercado 16 de Julio' },
    D4: { name: 'D4', fullName: 'Pallo Loma', avenues: 'Av. 14 de Septiembre', points: 'Zona residencial' },
    D5: { name: 'D5', fullName: 'Villa Dolores', avenues: 'Av. Litoral', points: 'Zona urbana' },
    D6: { name: 'D6', fullName: 'La Perez', avenues: 'Av. 6 de Agosto', points: 'Mercado, Estadio' },
    D7: { name: 'D7', fullName: 'Senkata', avenues: 'Av. Senkata', points: 'Zona industrial' },
    D8: { name: 'D8', fullName: 'Plan 3 Mil', avenues: 'Av. 6 de Marzo', points: 'Urbanización Plan 3 Mil' },
};

const mainMarkers = [
    { id: 1, coords: [-16.4928, -68.1512], title: 'Planta de Asfalto', description: 'Planta principal' },
    { id: 2, coords: [-16.4890, -68.1450], title: 'Almacén Central', description: 'Almacén de materiales' },
];

const style = {
    color: '#f27b00',
    weight: 2,
    opacity: 0.8,
    fillColor: '#f27b00',
    fillOpacity: 0.15,
};

const highlightStyle = {
    color: '#ff9426',
    weight: 3,
    opacity: 1,
    fillColor: '#ff9426',
    fillOpacity: 0.4,
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

const onEachFeature = (feature, layer) => {
    const districtId = feature.properties?.id || 'D1';
    const info = districtInfo[districtId] || { name: districtId, fullName: 'Distrito', avenues: 'Avenidas principales', points: 'Puntos de interés' };

    layer.on({
        mouseover: (e) => {
            e.target.setStyle(highlightStyle);
            e.target.bringToFront();
        },
        mouseout: (e) => {
            if (selectedDistrict.value !== districtId) {
                e.target.setStyle(style);
            }
        },
        click: (e) => {
            if (mapRef.value?.leafletObject) {
                mapRef.value.leafletObject.fitBounds(e.target.getBounds(), { padding: [50, 50] });
            }
            selectedDistrict.value = districtId;
            breadcrumb.value = ['El Alto', info.name, info.fullName];
        },
    });

    layer.bindTooltip(`
        <div style="font-family: system-ui, sans-serif; padding: 4px; min-width: 150px;">
            <strong style="color: #f27b00; font-size: 12px;">${info.name} - ${info.fullName}</strong>
            <div style="font-size: 10px; color: #666; margin-top: 4px;">
                <strong>Avenidas:</strong> ${info.avenues}
            </div>
            <div style="font-size: 10px; color: #666; margin-top: 2px;">
                <strong>Puntos:</strong> ${info.points}
            </div>
        </div>
    `, { sticky: true, className: 'district-tooltip' });
};

const resetView = () => {
    if (mapRef.value?.leafletObject) {
        mapRef.value.leafletObject.flyTo(CENTER, INITIAL_ZOOM, { duration: 0.8 });
    }
    selectedDistrict.value = null;
    breadcrumb.value = ['El Alto'];
};

onMounted(() => {
    fetchGeoJson();
});
</script>

<template>
    <div class="bg-[#0e1113] border border-[#2d3139] rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-[#2d3139]/50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-white">Mapa - El Alto</span>
                <div class="flex items-center gap-1 text-[10px] text-industrial-muted font-mono">
                    <span v-for="(crumb, index) in breadcrumb" :key="index" class="flex items-center">
                        <span class="text-white">{{ crumb }}</span>
                        <span v-if="index < breadcrumb.length - 1" class="mx-1">/</span>
                    </span>
                </div>
            </div>
            <button
                v-if="selectedDistrict"
                @click="resetView"
                class="text-[10px] text-[#f27b00] hover:text-[#ff9426] font-semibold transition"
            >
                Ver ciudad completa
            </button>
        </div>

        <div class="relative h-80">
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
                    :attribution="'© OpenStreetMap contributors'"
                    :options="{ maxZoom: 18 }"
                />

                <l-geo-json
                    v-if="geoJsonData"
                    :geojson="geoJsonData"
                    :options-style="() => style"
                    :options-on-each-feature="onEachFeature"
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
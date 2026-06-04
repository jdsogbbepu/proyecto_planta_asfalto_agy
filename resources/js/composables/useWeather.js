import { ref, computed } from 'vue';

const API_URL = 'https://api.open-meteo.com/v1/forecast';
const LATITUDE = -16.5;
const LONGITUDE = -68.15;

const WMO_CODES = {
    0: { icon: '☀️', label: 'Despejado' },
    1: { icon: '🌤️', label: 'Mayormente despejado' },
    2: { icon: '⛅', label: 'Parcialmente nublado' },
    3: { icon: '☁️', label: 'Nublado' },
    45: { icon: '🌫️', label: 'Niebla' },
    48: { icon: '🌫️', label: 'Niebla congelada' },
    51: { icon: '🌧️', label: 'Llovizna ligera' },
    53: { icon: '🌧️', label: 'Llovizna moderada' },
    55: { icon: '🌧️', label: 'Llovizna densa' },
    61: { icon: '🌧️', label: 'Lluvia ligera' },
    63: { icon: '🌧️', label: 'Lluvia moderada' },
    65: { icon: '🌧️', label: 'Lluvia intensa' },
    71: { icon: '🌨️', label: 'Nieve ligera' },
    73: { icon: '🌨️', label: 'Nieve moderada' },
    75: { icon: '❄️', label: 'Nieve intensa' },
    80: { icon: '🌦️', label: 'Chubascos ligeros' },
    81: { icon: '🌧️', label: 'Chubascos moderados' },
    82: { icon: '⛈️', label: 'Chubascos intensos' },
    95: { icon: '⛈️', label: 'Tormenta' },
    96: { icon: '⛈️', label: 'Tormenta con granizo' },
    99: { icon: '⛈️', label: 'Tormenta con granizo intenso' },
};

export function useWeather() {
    const currentWeather = ref(null);
    const dailyForecast = ref([]);
    const hourlyForecast = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const lastUpdated = ref(null);

    const getWeatherIcon = (code) => {
        return WMO_CODES[code] || { icon: '❓', label: 'Desconocido' };
    };

    const getDayName = (dateStr, index) => {
        const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        const date = new Date(dateStr);
        if (index === 0) return 'Hoy';
        if (index === 1) return 'Mañana';
        return days[date.getDay()];
    };

    const fetchWeather = async () => {
        loading.value = true;
        error.value = null;

        const params = new URLSearchParams({
            latitude: LATITUDE,
            longitude: LONGITUDE,
            current: 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m',
            hourly: 'temperature_2m,weather_code,relative_humidity_2m',
            daily: 'weather_code,temperature_2m_max,temperature_2m_min',
            timezone: 'auto',
            wind_speed_unit: 'kmh',
            forecast_days: 7,
        });

        try {
            const response = await fetch(`${API_URL}?${params}`);
            if (!response.ok) throw new Error('Error al obtener datos del clima');

            const data = await response.json();

            currentWeather.value = {
                temperature: Math.round(data.current.temperature_2m),
                apparentTemperature: Math.round(data.current.apparent_temperature),
                humidity: data.current.relative_humidity_2m,
                windSpeed: Math.round(data.current.wind_speed_10m),
                weatherCode: data.current.weather_code,
                ...getWeatherIcon(data.current.weather_code),
            };

            dailyForecast.value = data.daily.time.map((date, i) => ({
                date,
                dayName: getDayName(date, i),
                tempMax: Math.round(data.daily.temperature_2m_max[i]),
                tempMin: Math.round(data.daily.temperature_2m_min[i]),
                weatherCode: data.daily.weather_code[i],
                ...getWeatherIcon(data.daily.weather_code[i]),
            }));

            const today = new Date().getHours();
            hourlyForecast.value = data.hourly.time.slice(today, today + 24).map((time, i) => ({
                time: new Date(time).getHours() + ':00',
                temperature: Math.round(data.hourly.temperature_2m[today + i]),
                weatherCode: data.hourly.weather_code[today + i],
                ...getWeatherIcon(data.hourly.weather_code[today + i]),
            }));

            lastUpdated.value = new Date().toLocaleTimeString('es-BO', {
                hour: '2-digit',
                minute: '2-digit',
            });
        } catch (e) {
            error.value = e.message;
            console.error('Weather fetch error:', e);
        } finally {
            loading.value = false;
        }
    };

    return {
        currentWeather,
        dailyForecast,
        hourlyForecast,
        loading,
        error,
        lastUpdated,
        fetchWeather,
        getWeatherIcon,
    };
}
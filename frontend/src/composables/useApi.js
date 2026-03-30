import { ref } from 'vue';

const BASE_URL = import.meta.env.VITE_API_BASE_URL;

export function useApi() {
    const data = ref(null);
    const loading = ref(false);
    const error = ref(null);

    const fetchData = async (endpoint, method = 'GET', body = null) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await fetch(`${BASE_URL}${endpoint}`, {
                method: method,
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: body
            });
            if (!response.ok) {
                throw new Error(`Ошибка HTTP: ${response.status}`);
            }
            data.value = await response.json();
        } catch (err) {
            error.value = err.message;
        } finally {
            loading.value = false;
        }
    };

    return { data, loading, error, fetchData };
}

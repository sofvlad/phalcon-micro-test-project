import { ref } from 'vue';
import { useLocalStorage } from '@vueuse/core';

const BASE_URL = import.meta.env.VITE_API_BASE_URL;

export function useApi() {
    const data = ref(null);
    const loading = ref(false);
    const error = ref(null);

    const token = useLocalStorage('auth_token', null);

    const fetchData = async (endpoint, method = 'GET', body = null, customHeaders = {}) => {
        loading.value = true;
        error.value = null;

        const headers = {
            'Content-Type': 'application/json;charset=utf-8',
            ...customHeaders
        }

        if (token.value) {
            headers['Authorization'] = `Bearer ${token.value}`;
        }

        try {
            const response = await fetch(`${BASE_URL}${endpoint}`, {
                method: method,
                headers: headers,
                body: body ? JSON.stringify(body) : null
            });

            // Если токен просрочен - выбрасываем специальную ошибку
            if (response.status === 401) {
                throw new Error(response.statusText);
            }

            if (!response.ok) {
                throw new Error(response?.statusText ?? `Ошибка HTTP: ${response.status}`);
            }

            data.value = await response.json();

            return { success: true, data: data.value };
        } catch (err) {
            error.value = err.message;

            return { success: false, error: err.message }
        } finally {
            loading.value = false;
        }
    }

    return { data, loading, error, fetchData, token }
}

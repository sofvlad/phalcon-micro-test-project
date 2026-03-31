import { useLocalStorage } from '@vueuse/core'
import { computed } from 'vue'
import { useApi } from './useApi'

export function useAuth() {
    const { fetchData, loading } = useApi();

    const user = useLocalStorage('auth_user', null);
    const token = useLocalStorage('auth_token', null);

    const isAuthenticated = computed(() => {
        return !!token.value && !!user.value;
    });

    const login = async (email, password) => {
        const result = await fetchData('/user/login', 'POST', { email, password });

        if (result.success) {
            const responseData = result.data;
            token.value = responseData?.data.token;
            user.value = await fetchData('/user/me');

            return { success: true };
        }

        return { success: false, error: result.error };
    }

    const register = async (email, password) => {
        const result = await fetchData('/user/register', 'POST', { email, password });

        if (result.success) {
            token.value = result.data.token;
            user.value = result.data.user;

            return { success: true };
        }

        return { success: false, error: result.error };
    }

    const logout = async () => {
        // if (token.value) {
        //     await fetchData('/user/logout', 'POST', null);
        // }

        user.value = null;
        token.value = null;
    }

    const fetchCurrentUser = async () => {
        if (!token.value) return null

        const result = await fetchData('/user/me', 'GET');

        if (result.success) {
            user.value = result.data;

            return result.data;
        }

        return null;
    }

    return {
        user,
        token,
        isAuthenticated,
        loading,
        login,
        register,
        logout,
        fetchCurrentUser
    }
}

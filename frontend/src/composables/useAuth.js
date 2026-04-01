import { StorageSerializers, useLocalStorage } from '@vueuse/core'
import { computed } from 'vue'
import { useApi } from './useApi'

export function useAuth() {
    const { fetchData, loading } = useApi();

    const user = useLocalStorage('auth_user', null, { serializer: StorageSerializers.object });
    const token = useLocalStorage('auth_token', null);

    const isAuthenticated = computed(() => {
        return !!token.value;
    });

    const login = async (email, password) => {
        if (token.value) {
            return null;
        }

        const result = await fetchData('/user/login', 'POST', { email, password });
        if (!result.success) {
            return { success: false, error: result.error };
        }
        token.value = result?.data.data.token;

        return { success: true };
    }

    const register = async (email, password) => {
        const result = await fetchData('/user/register', 'POST', { email, password });

        if (result.success) {
            token.value = result?.data.data.token;

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
        if (user.value) {
            return user.value;
        }
        if (!token.value) {
            return null;
        }

        const result = await fetchData('/user/me', 'GET', null, {
            'Authorization': `Bearer ${token.value}`,
        });
        const responseData = result.data;

        if (!result.success) {
            return null;
        }
        user.value = responseData.data;

        return user.value;
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

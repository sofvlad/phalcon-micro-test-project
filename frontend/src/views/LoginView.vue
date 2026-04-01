
<script>
import { useAuth } from '../composables/useAuth';
import { useRouter } from "vue-router";

export default {
  name: 'LoginForm',
  setup() {
    const auth = useAuth();
    const router = useRouter();

    if (auth.isAuthenticated.value) {
      router.push('/');
    }

    return { ...auth, router };
  },
  data() {
    return {
      formData: {
        email: '',
        password: ''
      },
      showPassword: false,
      authError: '',
      errors: {
        email: '',
        password: ''
      }
    }
  },
  watch: {
    'isAuthenticated': {
      handler(newVal) {
        if (newVal) {
          this.router.push('/');
        }
      }
    },
    'formData.email'(newVal) {
      this.errors.email = this.validateEmail(newVal);
    },
    'formData.password'(newVal) {
      this.errors.password = this.validatePassword(newVal);
    }
  },
  methods: {
    validateEmail(email) {
      if (!email) {
        return 'Email обязателен';
      }
      const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
      if (!emailRegex.test(email)) {
        return 'Введите корректный email';
      }
      return ''
    },
    validatePassword(password) {
      if (!password) {
        return 'Пароль обязателен';
      }
      return ''
    },
    async handleSubmit() {
      this.authError = '';

      const result = await this.login(this.formData.email, this.formData.password);

      if (!result.success) {
        this.authError = result.error;
      }
    }
  }
}
</script>

<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="card shadow">
          <div class="card-body p-4">
            <h3 class="card-title text-center mb-4">Вход в систему</h3>
            <form @submit.prevent="handleSubmit">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    id="email"
                    v-model="formData.email"
                    type="email"
                    class="form-control"
                    :class="{ 'is-invalid': errors.email }"
                    placeholder="ivan@example.com"
                    autocomplete="email"
                    autofocus
                    required
                />
                <div v-if="errors.email" class="invalid-feedback">
                  {{ errors.email }}
                </div>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <div class="input-group">
                  <input
                      id="password"
                      v-model="formData.password"
                      :type="showPassword ? 'text' : 'password'"
                      class="form-control"
                      :class="{ 'is-invalid': errors.password }"
                      placeholder="Введите пароль"
                      autocomplete="current-password"
                      required
                  />
                  <button
                      type="button"
                      class="btn btn-outline-secondary"
                      @click="showPassword = !showPassword"
                  >
                    <span>
                      <i v-if="showPassword" class="bi bi-eye-slash-fill"></i>
                      <i v-else class="bi bi-eye-fill"></i>
                    </span>
                  </button>
                </div>
                <div v-if="errors.password" class="invalid-feedback d-block">
                  {{ errors.password }}
                </div>
              </div>
              <div v-if="authError" class="alert alert-danger" role="alert">
                {{ authError }}
              </div>
              <button
                  type="submit"
                  class="btn btn-primary w-100"
                  :disabled="loading"
              >
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                {{ loading ? 'Вход...' : 'Войти' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

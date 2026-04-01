<script>
import { useApi } from './composables/useApi';
import { useAuth } from './composables/useAuth';
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

export default {
  name: 'App',
  data() {
    return {
      response: null,
      loading: false,
      error: null,
      fetchData: null,
      categories: [],
      auth: null,
      user: null,
      router: null
    }
  },
  computed: {
    categoryList() {
      return this.response?.data || [];
    },
    reactiveCategories() {
      return computed(() => this.categories);
    },
    isAuthenticated() {
      return this.auth?.isAuthenticated;
    },
    currentUser() {
      return this.auth?.user;
    }
  },
  methods: {
    async loadCategories() {
      if (!this.fetchData) return;

      this.loading = true;
      this.error = null;

      try {
        await this.fetchData('/category/list', 'POST');
      } catch (err) {
        this.error = err.message;
      } finally {
        this.loading = false;
      }
    },
    updateCategories(newVal) {
      if (newVal?.data) {
        this.categories = newVal.data;
      }
    },
    async handleLogout() {
      if (this.auth) {
        await this.auth.logout();
      }
    }
  },
  watch: {
    response: {
      handler(newVal) {
        this.updateCategories(newVal);
      },
      deep: true,
      immediate: true
    },
    'auth.isAuthenticated': {
      handler(newVal) {
        console.log(newVal);
        if (newVal) {
          this.auth.fetchCurrentUser();
        }
      }
    }
  },
  created() {
    const { data, loading, error, fetchData } = useApi();
    this.response = data;
    this.loading = loading;
    this.error = error;
    this.fetchData = fetchData;

    this.auth = useAuth();
    this.router = useRouter();

    this.loadCategories();
    this.auth.fetchCurrentUser();
  },
  provide() {
    return {
      auth: this.auth,
      categories: this.reactiveCategories
    }
  }
}
</script>

<template>
  <div id="app">
    <div class="d-flex" style="min-height: 100vh;">
      <div class="flex-shrink-0 p-3 bg-white" style="width: 280px;">
        <a href="/" class="d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none border-bottom">
          <span class="fs-2 fw-semibold">MARKET</span>
        </a>
        <ul class="categories-list list-unstyled ps-0">
          <li class="mb-1">
            <router-link to="/" active-class="active">
              <button class="btn btn-toggle align-items-center rounded collapsed">
                Главная
              </button>
            </router-link>
          </li>
          <li v-if="loading" class="mb-1">
            <button class="btn btn-toggle align-items-center rounded collapsed" disabled>
              Загрузка...
            </button>
          </li>
          <li v-else-if="error" class="mb-1 text-danger">
            {{ error }}
          </li>
          <li v-else v-for="category in categoryList" :key="category.code" class="mb-1">
            <router-link :to="{ name: 'category_products', params: { code: category.code } }" active-class="active">
              <button class="btn btn-toggle align-items-center rounded collapsed">
                {{ category.name }}
              </button>
            </router-link>
          </li>
        </ul>
        <hr class="my-3" />
        <p v-if="isAuthenticated && currentUser" class="ms-2 mb-0">{{ currentUser?.email }}</p>
        <ul class="list-unstyled ps-0">
          <li v-if="!isAuthenticated" class="mb-1">
            <router-link to="/login" active-class="active">
              <button class="btn btn-toggle align-items-center rounded collapsed">
                <i class="bi bi-box-arrow-in-right"></i>
                Вход
              </button>
            </router-link>
          </li>
          <li v-else class="mb-1">
            <button
                class="btn btn-toggle align-items-center rounded collapsed text-danger"
                @click="handleLogout"
            >
              <i class="bi bi-box-arrow-right"></i>
              Выход
            </button>
          </li>
        </ul>
      </div>

      <div class="flex-grow-1 p-4">
        <div class="container-fluid px-0">
          <router-view />
        </div>
      </div>
    </div>
  </div>
</template>

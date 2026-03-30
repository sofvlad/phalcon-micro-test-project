<script setup>
import { useApi } from './composables/useApi';
import {onMounted, provide, ref, watch} from 'vue';

const { data: response, loading, error, fetchData } = useApi();
const categories = ref([]);

provide('categories', categories);

watch(response, (newCode) => {
  categories.value = newCode?.data;
});

onMounted(() => {
  fetchData('/category/list', 'POST');
});
</script>

<template>
  <div id="app">
    <div class="d-flex" style="min-height: 100vh;">
      <div class="flex-shrink-0 p-3 bg-white" style="width: 280px;">
        <a href="/" class="d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none border-bottom">
          <span class="fs-2 fw-semibold">MARKET</span>
        </a>
        <ul class="list-unstyled ps-0">
          <li class="mb-1">
            <router-link to="/">
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
          <li v-else v-for="category in response?.data" :key="category.code" class="mb-1">
            <router-link :to="{ name: 'category_products', params: { code: category.code } }">
              <button class="btn btn-toggle align-items-center rounded collapsed">
                {{ category.name }}
              </button>
            </router-link>
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

<script setup>
import { useApi } from '../composables/useApi';
import { computed, inject, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router'

const route = useRoute();
const router = useRouter();

const limit = 16;
const currentPage = ref(route.query?.page ?? 1);
const lastPage = ref(route.query?.page ?? 1);
const pages = ref([]);

const { data: response, loading, error, fetchData } = useApi();
const categories = inject('categories');

const categoryName = computed(() => {
  return categories?.value.find(c => c.code === route.params.code)?.name;
});

const goToPage = (page) => {
  router.push({ name: 'category_products', params: route.params, query: { page: page } });
}

const fetchProductList = (code, page) => {
  fetchData('/product/list', 'POST', JSON.stringify({
    page: page,
    limit: limit,
    category: code,
    order: {
      field: "name",
      dir: "ASC"
    }
  }));
}

watch(response, (newVal) => {
  currentPage.value = newVal?.data?.page;
  lastPage.value = Math.ceil(newVal?.data?.total / limit);
  pages.value = Array.from({ length: lastPage.value }, (_, i) => i + 1);
});

watch(() => route.query.page, (newValue, oldValue) => {
  if (newValue && newValue !== oldValue) {
    fetchProductList(route.params?.code, newValue > 0 ? newValue : 1);
  }
});

watch(() => route.params.code, (newValue, oldValue) => {
  if (newValue && newValue !== oldValue) {
    fetchProductList(newValue, route.query?.page ?? 1);
  }
}, { immediate: true });
</script>

<template>
  <div class="mb-4">
    <h3>Продукты{{ categoryName ? ': ' + categoryName : '' }}</h3>
  </div>

  <div class="row g-4">
    <div v-if="loading">
      <button class="btn btn-toggle align-items-center rounded collapsed" disabled>
        Загрузка...
      </button>
    </div>
    <div v-else-if="error" class="text-danger">
      {{ error }}
    </div>
    <div v-else v-for="product in response?.data?.items" :key="product.id" class="col-sm-3">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-9">
              <p class="card-title fs-5 fw-bold">{{ product.name }}</p>
            </div>
            <div class="col text-end">
              <span class="fs-4">${{ product.price }}</span>
            </div>
          </div>
          <p class="card-text">{{ product.description }}</p>
        </div>
      </div>
    </div>
  </div>

  <nav v-if="pages.length" class="mt-5" aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
      <li class="page-item" :class="{ disabled: currentPage === 1 }" @click="goToPage(1)">
        <a role="button" class="page-link" @click="goToPage(1)">First</a>
      </li>
      <li
          v-for="page in pages"
          :key="page"
          class="page-item"
          :class="{ active: page === currentPage }"
      >
        <a role="button" class="page-link" @click="goToPage(page)">{{ page }}</a>
      </li>
      <li class="page-item" :class="{ disabled: currentPage === pages[pages.length - 1] }">
        <a role="button" class="page-link" @click="goToPage(pages[pages.length - 1])">Last</a>
      </li>
    </ul>
  </nav>
</template>

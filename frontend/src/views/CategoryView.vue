<script>
import { useApi } from '../composables/useApi';
import Pagination from "../components/Pagination.vue";
import ProductCard from "../components/ProductCard.vue";
import { useRoute, useRouter } from 'vue-router'

export default {
  name: 'CategoryProducts',
  components: {
    Pagination,
    ProductCard
  },
  inject: ['categories'],
  data() {
    return {
      limit: 16,
      currentPage: 1,
      lastPage: 1,
      pages: [],
      response: null,
      loading: false,
      error: null,
      fetchData: null,
      route: null,
      router: null
    }
  },
  computed: {
    categoryName() {
      const categories = this.categories;
      if (!categories) {
        return '';
      }

      return categories.find(c => c.code === this.route.params?.code)?.name;
    },
    products() {
      return this.response?.data?.items || [];
    }
  },
  methods: {
    initRouter() {
      this.route = useRoute();
      this.router = useRouter();
    },
    handlePageChange(page = 1) {
      if (this.route?.params?.code) {
        this.router.push({
          name: 'category_products',
          params: this.route.params,
          query: { page: page }
        });
      } else {
        this.router.push({
          name: 'all_products',
          query: { page: page }
        });
      }
    },
    async fetchProductList(code = null, page = 1) {
      if (!this.fetchData) return;

      this.loading = true;
      this.error = null;

      const body = {
        page: page,
        limit: this.limit,
        in_stock: true,
        order: {
          field: "name",
          dir: "ASC"
        }
      };

      if (code) {
        body['category'] = code;
      }

      try {
        await this.fetchData('/product/list', 'POST', body);
      } catch (err) {
        this.error = err.message;
      } finally {
        this.loading = false;
      }
    },
    updatePagination(responseData) {
      if (responseData?.data) {
        this.currentPage = responseData.data.page;
        this.lastPage = Math.ceil(responseData.data.total / this.limit);
        this.pages = Array.from({ length: this.lastPage }, (_, i) => i + 1);
      }
    }
  },
  watch: {
    'route.query.page': {
      handler(newValue, oldValue) {
        if (newValue && newValue !== oldValue) {
          const page = parseInt(newValue) > 0 ? parseInt(newValue) : 1;
          this.fetchProductList(this.route?.params?.code, page);
        }
      }
    },
    'route.params.code': {
      handler(newValue, oldValue) {
        if (newValue !== oldValue) {
          const page = this.route?.query?.page ?? 1;
          this.fetchProductList(newValue ?? null, parseInt(page));
        }
      },
      immediate: true
    },
    response: {
      handler(newVal) {
        this.updatePagination(newVal);
      },
      deep: true,
      immediate: true
    }
  },
  created() {
    this.initRouter();

    const { data, loading, error, fetchData } = useApi();
    this.response = data;
    this.loading = loading;
    this.error = error;
    this.fetchData = fetchData;

    this.currentPage = this.route.query?.page ? parseInt(this.route.query.page) : 1;
  }
}
</script>

<template>
  <div class="mb-4">
    <h3>Продукты{{ categoryName ? ': ' + categoryName : '' }}</h3>
  </div>

  <div v-if="loading" class="text-center py-5">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Загрузка...</span>
    </div>
  </div>

  <div v-else-if="error" class="alert alert-danger">
    {{ error }}
  </div>

  <div v-else class="row g-4">
    <p v-if="!products.length">Продуктов нет...</p>
    <div
        v-for="product in products"
        :key="product.id"
        class="col-sm-3"
    >
      <div class="card h-100">
        <ProductCard :product="product" />
      </div>
    </div>
  </div>

  <Pagination
      :current-page="currentPage"
      :last-page="lastPage"
      :max-visible-pages="5"
      @page-change="handlePageChange"
  />
</template>

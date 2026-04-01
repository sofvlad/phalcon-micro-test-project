<script>
import { useApi } from '../composables/useApi';
import { useAuth } from '../composables/useAuth';
import ConfirmButton from "./ConfirmButton.vue";

export default {
  name: 'ProductCard',
  components: {ConfirmButton},
  props: {
    product: {
      type: Object,
      required: true,
      default: () => ({
        name: '',
        price: 0,
        description: ''
      })
    }
  },
  emits: ['deleted'],
  setup() {
    const { fetchData } = useApi();
    const { isAuthenticated } = useAuth();

    return { fetchData, isAuthenticated };
  },
  methods: {
    async deleteProduct() {
      const result = await this.fetchData(`/product/${this.product.id}`, 'DELETE');

      if (!result.success) {
        throw new Error(result.error);
      }

      return result;
    },
    onDeleteSuccess() {
      this.$emit('deleted', this.product.id);
    },
    onDeleteError(error) {
      console.error('Ошибка удаления:', error);
      alert('Не удалось удалить продукт');
    }
  }
}
</script>

<template>
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
    <div class="card-actions">
      <ConfirmButton
          v-if="this.isAuthenticated"
          button-text="Удалить продукт"
          size="sm"
          variant="danger"
          modal-title="Удаление продукта"
          modal-message="Вы уверены, что хотите удалить этот продукт?"
          confirm-text="Удалить"
          :action="deleteProduct"
          @success="onDeleteSuccess"
          @error="onDeleteError"
      />
    </div>
  </div>
</template>

<style>
  .card-actions {
    text-align: right;
  }

  .card-actions .modal {
    text-align: left;
  }

  .card-actions .confirm-button {
    border-radius: var(--bs-border-radius-sm) 0 var(--bs-border-radius-sm) 0;
  }
</style>

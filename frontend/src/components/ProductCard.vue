<script>
export default {
  name: 'ProductCard',
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
  </div>
</template>

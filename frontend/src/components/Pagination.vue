<script>
export default {
  name: 'Pagination',
  props: {
    currentPage: {
      type: Number,
      required: true
    },
    lastPage: {
      type: Number,
      required: true
    },
    maxVisiblePages: {
      type: Number,
      default: 5
    }
  },
  emits: ['page-change'],
  computed: {
    visiblePages() {
      if (this.lastPage <= this.maxVisiblePages) {
        return Array.from({ length: this.lastPage }, (_, i) => i + 1);
      }

      let start = Math.max(1, this.currentPage - Math.floor(this.maxVisiblePages / 2));
      let end = start + this.maxVisiblePages - 1;

      if (end > this.lastPage) {
        end = this.lastPage;
        start = end - this.maxVisiblePages + 1;
      }

      return Array.from({ length: this.maxVisiblePages }, (_, i) => start + i);
    },
  },
  methods: {
    goToPage(page) {
      if (page !== this.currentPage && page >= 1 && page <= this.lastPage) {
        this.$emit('page-change', page);
      }
    },
    goToFirst() {
      this.goToPage(1);
    },
    goToLast() {
      this.goToPage(this.lastPage);
    },
    hasLast() {
      return this.visiblePages.at(-1) === this.lastPage;
    },
    hasFirst() {
      return this.visiblePages.at(0) === 1;
    },
  }
}
</script>

<template>
  <nav v-if="lastPage > 1" class="mt-5" aria-label="Page navigation">
    <ul class="pagination justify-content-center">
      <li class="page-item" :class="{ disabled: currentPage === 1 }">
        <a role="button" class="page-link" @click="goToFirst()">First</a>
      </li>
      <li v-if="!hasFirst()" class="page-item page-item-space">
        <a role="button" class="page-link">...</a>
      </li>
      <li
          v-for="page in visiblePages"
          :key="page"
          class="page-item"
          :class="{ active: page === currentPage }"
      >
        <a role="button" class="page-link" @click="goToPage(page)">{{ page }}</a>
      </li>
      <li v-if="!hasLast()" class="page-item page-item-space">
        <a role="button" class="page-link">...</a>
      </li>
      <li class="page-item" :class="{ disabled: currentPage === lastPage }">
        <a role="button" class="page-link" @click="goToLast()">Last</a>
      </li>
    </ul>
  </nav>
</template>

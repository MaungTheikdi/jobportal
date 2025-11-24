<template>
  <div class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6 rounded-b-lg">
    <!-- Mobile view -->
    <div class="flex flex-1 justify-between sm:hidden">
      <button 
        @click="handlePageChange(currentPage - 1)"
        :disabled="currentPage === 1"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md"
        :class="{
          'bg-white text-gray-700 hover:bg-gray-50': currentPage !== 1,
          'bg-gray-100 text-gray-400 cursor-not-allowed': currentPage === 1
        }"
      >
        Previous
      </button>
      <button 
        @click="handlePageChange(currentPage + 1)"
        :disabled="currentPage === totalPages"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md ml-3"
        :class="{
          'bg-white text-gray-700 hover:bg-gray-50': currentPage !== totalPages,
          'bg-gray-100 text-gray-400 cursor-not-allowed': currentPage === totalPages
        }"
      >
        Next
      </button>
    </div>

    <!-- Desktop view -->
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span>
          to <span class="font-medium">{{ Math.min(currentPage * perPage, totalItems) }}</span>
          of <span class="font-medium">{{ totalItems }}</span> results
        </p>
      </div>
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <button
            @click="handlePageChange(currentPage - 1)"
            :disabled="currentPage === 1"
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium"
            :class="{
              'text-gray-500 hover:bg-gray-50': currentPage !== 1,
              'text-gray-300 cursor-not-allowed': currentPage === 1
            }"
          >
            <span class="sr-only">Previous</span>
            <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
          </button>

          <!-- Page numbers -->
          <template v-for="page in visiblePages" :key="page">
            <button
              v-if="page === '...'"
              disabled
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
            >
              ...
            </button>
            <button
              v-else
              @click="handlePageChange(page)"
              :aria-current="page === currentPage ? 'page' : undefined"
              class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
              :class="{
                'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': page === currentPage,
                'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': page !== currentPage
              }"
            >
              {{ page }}
            </button>
          </template>

          <button
            @click="handlePageChange(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium"
            :class="{
              'text-gray-500 hover:bg-gray-50': currentPage !== totalPages,
              'text-gray-300 cursor-not-allowed': currentPage === totalPages
            }"
          >
            <span class="sr-only">Next</span>
            <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid'

export default {
  components: {
    ChevronLeftIcon,
    ChevronRightIcon
  },

  props: {
    currentPage: {
      type: Number,
      required: true
    },
    totalPages: {
      type: Number,
      required: true
    },
    totalItems: {
      type: Number,
      required: true
    },
    perPage: {
      type: Number,
      default: 15
    },
    maxVisiblePages: {
      type: Number,
      default: 5
    }
  },

  emits: ['page-changed'],

  computed: {
    visiblePages() {
      const range = [];
      const half = Math.floor(this.maxVisiblePages / 2);
      let start = Math.max(1, this.currentPage - half);
      let end = Math.min(this.totalPages, start + this.maxVisiblePages - 1);

      // Adjust if we're at the end
      if (end - start + 1 < this.maxVisiblePages) {
        start = Math.max(1, end - this.maxVisiblePages + 1);
      }

      // Always show first page
      if (start > 1) {
        range.push(1);
        if (start > 2) {
          range.push('...');
        }
      }

      // Add pages in range
      for (let i = start; i <= end; i++) {
        range.push(i);
      }

      // Always show last page
      if (end < this.totalPages) {
        if (end < this.totalPages - 1) {
          range.push('...');
        }
        range.push(this.totalPages);
      }

      return range;
    }
  },

  methods: {
    handlePageChange(page) {
      if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
        this.$emit('page-changed', page);
      }
    }
  }
}
</script>
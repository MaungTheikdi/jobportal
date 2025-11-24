<!-- components/Bet/HandicapBadge.vue -->
<template>
  <span class="inline-block bg-blue-100 text-blue-800 rounded-full px-2 py-1 text-xs font-medium">
    {{ displayValue }}
  </span>
</template>

<script>
export default {
  props: {
    value: {
      type: [String, Number],
      required: true
    },
    percentage: {
      type: String,
      required: true
    }
  },
  computed: {
    displayValue() {
      return `${this.value} ${this.percentage.includes('-') ? this.percentage : '+' + this.percentage}`
    }
  }
}
</script>
<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-500">{{ title }}</p>
        <p class="text-sm font-bold mt-1">{{ value }}</p>
      </div>
      <div class="p-3 rounded-full bg-blue-100 text-blue-600">
        <span class="text-2xl">{{ icon }}</span>
      </div>
    </div>
    <div v-if="change !== undefined" class="mt-2 flex items-center">
      <span 
        :class="{
          'text-green-600': trend === 'up',
          'text-red-600': trend === 'down',
          'text-gray-600': trend === 'neutral'
        }"
        class="font-medium"
      >
        {{ change }}%
      </span>
      <svg 
        v-if="trend === 'up'" 
        class="w-4 h-4 ml-1 text-green-600" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
      </svg>
      <svg 
        v-else-if="trend === 'down'"
        class="w-4 h-4 ml-1 text-red-600" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
    value: [String, Number],
    icon: String,
    trend: {
      type: String,
      validator: value => ['up', 'down', 'neutral'].includes(value)
    },
    change: Number
  }
}
</script>
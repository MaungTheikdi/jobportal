<template>
  <div class="max-w-xl mx-auto p-4 bg-white shadow rounded">
    <div class="flex justify-between items-center mb-4">
      <button @click="prevMonth" class="text-sm px-2 py-1 bg-gray-200 rounded">‹</button>
      <h2 class="text-lg font-bold">{{ monthYear }}</h2>
      <button @click="nextMonth" class="text-sm px-2 py-1 bg-gray-200 rounded">›</button>
    </div>

    <div class="grid grid-cols-7 gap-2 text-center text-sm font-semibold text-gray-600">
      <div v-for="day in days" :key="day">{{ day }}</div>
    </div>

    <div class="grid grid-cols-7 gap-2 mt-2">
      <div v-for="n in startDay" :key="'empty-' + n"></div>
      <div
        v-for="day in daysInMonth"
        :key="day"
        :class="[
          'text-center p-2 rounded cursor-pointer',
          isToday(day) ? 'bg-blue-500 text-white font-bold' : 'hover:bg-gray-100'
        ]"
      >
        {{ day }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    const today = new Date()
    return {
      currentMonth: today.getMonth(),
      currentYear: today.getFullYear(),
      todayDate: today.toISOString().split('T')[0],
    }
  },
  computed: {
    days() {
      return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
    },
    daysInMonth() {
      return new Date(this.currentYear, this.currentMonth + 1, 0).getDate()
    },
    startDay() {
      return new Date(this.currentYear, this.currentMonth, 1).getDay()
    },
    monthYear() {
      return new Date(this.currentYear, this.currentMonth).toLocaleDateString('default', {
        month: 'long',
        year: 'numeric',
      })
    },
  },
  methods: {
    isToday(day) {
      const date = new Date(this.currentYear, this.currentMonth, day)
      return date.toISOString().split('T')[0] === this.todayDate
    },
    nextMonth() {
      if (this.currentMonth === 11) {
        this.currentMonth = 0
        this.currentYear++
      } else {
        this.currentMonth++
      }
    },
    prevMonth() {
      if (this.currentMonth === 0) {
        this.currentMonth = 11
        this.currentYear--
      } else {
        this.currentMonth--
      }
    },
  },
}
</script>

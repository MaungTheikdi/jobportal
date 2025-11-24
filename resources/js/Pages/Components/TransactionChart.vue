<template>
  <div>


    <!-- <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow my-2"> -->
      <label for="year">ရွေးချယ်ထားသောနှစ်:</label>
      <select v-model="selectedYear" @change="fetchChartData" class="form-select border-0 rounded-md shadow-sm focus:ring focus:ring-blue-500">
        <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
      </select>

      <canvas id="transactionChart"></canvas>
    <!-- </div> -->

  </div>
</template>

<script>
import Chart from 'chart.js/auto'
import axios from 'axios'

export default {
  data() {
    return {
      chart: null,
      selectedYear: new Date().getFullYear(),
      years: [], // You can generate this dynamically if needed
    }
  },
  mounted() {
    this.fetchChartData()
    // generate the years start from 2024 and end to current year
    const currentYear = new Date().getFullYear()
    for (let i = 2024; i <= currentYear; i++) {
      this.years.push(i)
    }
  },
  methods: {
    fetchChartData() {
      axios.get(`transaction/chart-data?year=${this.selectedYear}`).then(res => {
        const labels = res.data.map(item => item.month)
        const deposits = res.data.map(item => item.deposits)
        const withdrawals = res.data.map(item => item.withdrawals)
        const bets = res.data.map(item => item.bets)
		const winnings = res.data.map(item => item.winnings)

		console.log("res data : chart :", res);

        if (this.chart) this.chart.destroy()

        const ctx = document.getElementById('transactionChart').getContext('2d')
        this.chart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [
              {
                label: 'ငွေသွင်း',
                data: deposits,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
              },
			  {
                label: 'ငွေထုတ်',
                data: withdrawals,
                backgroundColor: 'rgba(54, 122, 45, 0.5)',
              },
              {
                label: 'အရှုံး',
                data: bets,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
              },
              {
                label: 'အနိုင်',
                data: winnings,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
              }
            ]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: `${this.selectedYear} ခုနှစ် အတွက် အသုံးပြုသူ လအလိုက် ငွေစာရင်း`,
                font: {
                  size: 16
                }
              }
            }
          }
        });
		
      })
    }
  }
}
</script>
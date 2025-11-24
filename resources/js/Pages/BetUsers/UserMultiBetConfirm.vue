<template>
  <BetUserLayout>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-8">
      <div class="w-full max-w-3xl bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-indigo-600 p-4 text-center">
          <h1 class="text-xl sm:text-2xl font-bold text-white">
            Confirm Your Multi Bet ({{ bets.data.bets.length }} selections)
          </h1>
          <p class="text-indigo-100 text-sm mt-1">You need at least 4 selections</p>
        </div>

        <!-- Bets List -->
        <div class="divide-y">
          <div
            v-for="(bet, index) in bets.data.bets"
            :key="index"
            class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center"
          >
            <div class="flex-1 space-y-1">
              <div class="text-sm font-semibold text-gray-700">
                Match {{ index + 1 }}: {{ bet.selected_team_name }} @ {{ new Date(bet.match_date).toLocaleDateString() }}
              </div>
              <div class="text-xs text-gray-500">Bet Type: {{ bet.selected_betting_type }}</div>
              <div class="text-xs text-gray-500">Handicap: {{ bet.handicap_value }} ({{ bet.handicap_percentage }})</div>
            </div>
            <div class="text-right sm:text-left mt-2 sm:mt-0">
              <div class="text-gray-700">Stake: <strong>{{ bet.bet_amount }} MMK</strong></div>
              <div class="text-green-600 text-sm font-bold">Win: {{ bet.potential_win }} MMK</div>
            </div>
          </div>
        </div>

        <!-- Total Summary -->
        <div class="bg-gray-100 p-4 text-sm text-gray-700 flex justify-between items-center">
          <div>Total Stake: <strong>{{ totalStake }} MMK</strong></div>
          <div>Potential Win: <strong class="text-green-600">{{ totalWin }} MMK</strong></div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-gray-50 px-6 py-4 flex justify-between space-x-4">
          <button
            @click="cancel"
            class="flex-1 py-3 bg-white border border-red-500 text-red-500 rounded-lg shadow-sm hover:bg-red-50 transition flex items-center justify-center"
          >
            Cancel
          </button>
          <button
            @click="submitMultiBet"
            class="flex-1 py-3 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition flex items-center justify-center"
            :disabled="loading"
          >
            {{ loading ? 'Submitting...' : 'Confirm Multi Bet' }}
          </button>
        </div>
      </div>
    </div>
  </BetUserLayout>
</template>

<script>
import { router } from '@inertiajs/vue3';
import BetUserLayout from '@/Layouts/BetUserLayout.vue';

export default {
  components: {
    BetUserLayout,
  },
  props: {
    bets: Object,
  },
  data() {
    return {
      loading: false,
    };
  },
  computed: {
    totalStake() {
		return Array.isArray(this.bets.data.bets)
		? this.bets.data.bets.reduce((sum, bet) => sum + Number(bet.bet_amount), 0)
		: 0;
	},
	totalWin() {
		return Array.isArray(this.bets.data.bets)
		? this.bets.data.bets.reduce((sum, bet) => sum + Number(bet.potential_win), 0)
		: 0;
	},
  },
  methods: {
    submitMultiBet() {
      if (this.bets.data.bets.length < 4) {
        alert('You must select at least 4 matches for a multi bet.');
        return;
      }

      this.loading = true;

      router.post('/bet/api/create/multi_bet', { bets: this.bets.data.bets }, {
        onSuccess: () => {
          alert('Multi bet placed successfully!');
          this.$inertia.visit('/bet/history');
        },
        onError: (errors) => {
          console.error(errors);
          alert('There was an error placing your bet.');
        },
        onFinish: () => {
          this.loading = false;
        },
      });
    },
    cancel() {
      this.$inertia.visit('/bet/usermultibet');
    },
  },
  mounted(){
	console.log("datas: ", this.bets.data.bets);
  },
};
</script>

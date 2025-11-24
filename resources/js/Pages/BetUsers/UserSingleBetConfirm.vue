<!--
<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-6 space-y-4">
      <h1 class="text-xl sm:text-2xl font-bold text-gray-800 text-center">
        Confirm Your Bet
      </h1>


      <div class="space-y-2 text-sm sm:text-base text-gray-700">
        <div class="flex justify-between">
          <span class="font-medium">Team:</span>
          <span>{{ bet.selected_team_name || bet.selected_betting_type }}</span>
        </div>
        <div class="flex justify-between">
          <span class="font-medium">Bet Amount:</span>
          <span>{{ bet.bet_amount }}</span>
        </div>
        <div class="flex justify-between">
          <span class="font-medium">Odds %:</span>
          <span>{{ bet.handicap_percentage }}</span>
        </div>
        <div class="flex justify-between">
          <span class="font-medium">Potential Win:</span>
          <span>{{ bet.potential_win }}</span>
        </div>
        <div class="flex justify-between">
          <span class="font-medium">Placed At:</span>
          <span>{{ bet.placed_at }}</span>
        </div>
      </div>

      <div class="flex justify-between space-x-2 mt-4">
        <button
          @click="cancel"
          class="w-1/2 py-2 bg-red-500 text-white rounded-xl shadow hover:bg-red-600 transition"
        >
          Cancel
        </button>
        <button
          @click="submitBet"
          class="w-1/2 py-2 bg-green-600 text-white rounded-xl shadow hover:bg-green-700 transition"
        >
          Confirm
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

export default {
  props: {
    bet: Object,
  },
  data() {
    return {
      form: useForm({ ...this.bet }),
	  
	  option_id: null,
	  metchdata: {},

    };
  },
  methods: {
	fetchOptionsData(){
		
		console.log("Bet data to confirm:", this.bet);

		axios.get(`/bet/api/user/option/${this.option_id}`)
			.then(response => {
				this.metchdata = response.data;
				console.log("Fetched option data:", this.metchdata);
			})
			.catch(error => {
				console.error("Error fetching option data:", error);
			});
		
	},
    submitBet() {
      this.form.post('/api/create/single_bet', {
        onSuccess: () => {
          alert('✅ Bet placed successfully!');
          this.$inertia.visit('/user_dashboard'); // Or any route to redirect
        },
        onError: () => {
          alert('❌ Failed to place bet. Please try again.');
        },
      });
    },
    cancel() {
      this.$inertia.visit('/bet/usersinglebet');
    },
  },
  mounted() {
	this.option_id = this.bet.option_id;
	if (this.option_id){
		this.fetchOptionsData();
	}
  },
};
</script>

<style scoped>
</style>
-->
<template>
  <BetUserLayout>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-8">
      <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-indigo-600 p-4 text-center">
          <h1 class="text-xl sm:text-2xl font-bold text-white">
            Confirm Your Bet
          </h1>
          <p class="text-indigo-100 text-sm mt-1">{{ matchdata.league }}</p>
        </div>

        <!-- Match Info -->
        <div class="p-6 border-b">
          <div class="flex items-center justify-between mb-4">

            <div class="text-center flex-1">
              <img :src="matchdata.home_team_logo" class="h-12 mx-auto mb-2" alt="Home team">
              <p class="font-medium text-gray-800">{{ matchdata.home_team_name }}</p>
        <p v-if="matchdata.bet_type_id === 1 && matchdata.is_home_team === 1"
          class="bg-green-500 text-white rounded-full px-2 py-1 text-xs ml-auto">
          {{ matchdata.handicap_value }} 
          {{ 	
            matchdata.handicap_percentage.includes('-') ? 
            matchdata.handicap_percentage : '+' + 
            matchdata.handicap_percentage 
          }}
          </p>
            </div>

            <div class="px-4 text-center">
              <div class="bg-gray-100 rounded-lg px-3 py-1">
                <p class="text-xs text-gray-500">Match Date</p>
                <p class="text-sm font-bold">{{ new Date(matchdata.match_date).toLocaleString() }}</p>
              </div>
              <p class="text-xl font-bold my-2">VS</p>
        <p v-if="matchdata.bet_type_id === 2"
          class="bg-green-500 text-white rounded-full px-2 py-1 text-xs ml-auto">
          {{ matchdata.handicap_value }} 
          {{ 	
            matchdata.handicap_percentage.includes('-') ? 
            matchdata.handicap_percentage : '+' + 
            matchdata.handicap_percentage 
          }}
          </p>
            </div>

            <div class="text-center flex-1">
              <img :src="matchdata.away_team_logo" class="h-12 mx-auto mb-2" alt="Away team">
              <p class="font-medium text-gray-800">{{ matchdata.away_team_name }}</p>
        <p v-if="matchdata.bet_type_id === 1 && matchdata.is_home_team === 0"
          class="bg-green-500 text-white rounded-full px-2 py-1 text-xs ml-auto">
          {{ matchdata.handicap_value }} 
          {{ 	
            matchdata.handicap_percentage.includes('-') ? 
            matchdata.handicap_percentage : '+' + 
            matchdata.handicap_percentage 
          }}
          </p>
            </div>

          </div>
        </div>

        <!-- Bet Details -->
        <div class="p-6 space-y-4">
          <h2 class="text-lg font-bold text-gray-800">Your Bet Details</h2>
          
          <div class="bg-gray-50 rounded-lg p-4 space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Bet Type:</span>
              <span class="font-medium">{{ matchdata.bet_type_name }}</span>
            </div>
        <div class="flex justify-between">
              <span class="text-gray-600">Selected:</span>
              <span class="font-medium">{{ bet.selected_team_name || bet.selected_betting_type }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Stake Amount:</span>
              <span class="font-medium">{{ bet.bet_amount }} MMK</span>
            </div>
            <div class="flex justify-between text-green-600">
              <span class="font-bold">Potential Win:</span>
              <span class="font-bold">{{ bet.potential_win }} MMK</span>
            </div>
          </div>

          <div class="pt-2 text-sm text-gray-500 flex justify-between">
            <span>Placed At:</span>
            <span>{{ new Date(bet.placed_at).toLocaleString() }}</span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-gray-50 px-6 py-4 flex justify-between space-x-4">
          <button
            @click="cancel"
            class="flex-1 py-3 bg-white border border-red-500 text-red-500 rounded-lg shadow-sm hover:bg-red-50 transition flex items-center justify-center"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Cancel
          </button>
          <button
            @click="submitBet"
            class="flex-1 py-3 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition flex items-center justify-center"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Confirm Bet
          </button>
        </div>
      </div>
    </div>
  </BetUserLayout>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import BetUserLayout from '@/Layouts/BetUserLayout.vue';
import axios from 'axios';

export default {
  components: {
    BetUserLayout,
  },
  props: {
    bet: Object,
  },
  data() {
    return {
      form: useForm({ ...this.bet }),
      option_id: null,
      matchdata: {},

    };
  },
  methods: {
    fetchOptionsData() {
      axios.get(`/bet/api/user/option/${this.option_id}`)
        .then(response => {
          this.matchdata = response.data;
		  console.log("Fetched option data:", this.matchdata);
		  console.log("Bet data to confirm:", this.bet);
        })
        .catch(error => {
          console.error("Error fetching option data:", error);
        });
    },
    submitBet() {
      axios.post('api/create/single_bet', {
			user_id: this.$page.props.auth.user.user_id,
			option_id: this.bet.option_id,
			match_id: this.bet.match_id,
			match_date: this.bet.match_date,
			is_home_team: this.bet.is_home_team,
			bet_type_id: this.bet.bet_type_id,
			handicap_value: this.bet.handicap_value,
			handicap_percentage: this.bet.handicap_percentage,
			bet_amount: this.bet.bet_amount,
			selected_betting_type: this.bet.selected_betting_type,
			selected_team_id: this.bet.selected_team_id,
			selected_team_name: this.bet.selected_team_name,

			potential_win: this.bet.potential_win,
			placed_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
			status: 'pending',
			settled_at: null,
		}).then(()=>{
			alert('Bet created successfully!');
		})
		.catch(error => {
			console.error('Validation Error:', error.response?.data?.errors);
			alert('Validation failed. Check console.');
      this.$inertia.visit('/bet/history');
		});
    },
    cancel() {
      this.$inertia.visit('/bet/usersinglebet');
    },
  },
  mounted() {
    this.option_id = this.bet.option_id;
    if (this.option_id) {
      this.fetchOptionsData();
    }
  },
};
</script>
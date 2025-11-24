<template>
  <BetUserLayout>
    <div class="max-w-6xl mx-auto px-4 py-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">ဘော်ဒီ/ဂိုးပေါင်းစာရင်း</h1>
      
      <EmptyState v-if="userBet.length === 0" />
      
      <div v-for="(userBet, index) in userBet" :key="index" class="bg-gray-100 flex justify-center px-4 py-8">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
          <!-- Match Info -->
          <div class="p-6 border-b">
            <div class="flex items-center justify-between mb-4">

              <div class="text-center flex-1">
                <img :src="userBet.match.home_team.logo_url" class="h-12 mx-auto mb-2" alt="Home team">
                <p class="font-medium text-gray-800">{{ userBet.match.home_team.team_name }}</p>
                <p v-if="userBet.bet_type_id === 1 && userBet.is_home_team === 1"
                  class="bg-green-500 text-white rounded-full px-2 py-1 text-xs ml-auto">
                  {{ userBet.handicap_value }}
                  {{
                    userBet.handicap_percentage.includes('-') ?
                      userBet.handicap_percentage : '+' +
                      userBet.handicap_percentage
                  }}
                </p>
              </div>

              <div class="px-4 text-center">
                <div class="bg-gray-100 rounded-lg px-3 py-1">
                  <p class="text-xs text-gray-500">Match Date</p>
                  <p class="text-sm font-bold">{{ new Date(userBet.match_date).toLocaleString() }}</p>
                </div>
                <!-- <p v-if="userBet.match.home_score !== null && userBet.match.away_score !== null" class="text-xl font-bold my-2">
                  <span>{{ userBet.match.home_score }}</span>VS<span>{{ userBet.match.away_score }}</span>
                </p>
                <p v-else class="text-xl font-bold my-2">
                  VS
                </p> -->
                <p class="text-xl font-bold my-2">
                  <span v-if="userBet.match.home_score !== null && userBet.match.away_score !== null">
                    {{ userBet.match.home_score }} - {{ userBet.match.away_score }}
                    <!-- <span class="mx-2">VS</span> -->
                  </span>
                  <span v-else>VS</span>
                </p>

                <p v-if="userBet.bet_type_id === 2"
                  class="bg-green-500 text-white rounded-full px-2 py-1 text-xs ml-auto">
                  {{ userBet.handicap_value }}
                  {{
                    userBet.handicap_percentage.includes('-') ?
                      userBet.handicap_percentage : '+' +
                      userBet.handicap_percentage
                  }}
                </p>
              </div>

              <div class="text-center flex-1">
                <img :src="userBet.match.away_team.logo_url" class="h-12 mx-auto mb-2" alt="Away team">
                <p class="font-medium text-gray-800">{{ userBet.match.away_team.team_name }}</p>
                <p v-if="userBet.bet_type_id === 1 && userBet.is_home_team === 0"
                  class="bg-green-500 text-white rounded-full px-2 py-1 text-xs ml-auto">
                  {{ userBet.handicap_value }}
                  {{
                    userBet.handicap_percentage.includes('-') ?
                      userBet.handicap_percentage : '+' +
                      userBet.handicap_percentage
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
                <span class="font-medium">{{ userBet.bet_type.type_name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Selected:</span>
                <span class="font-medium">{{ userBet.selected_team_name || userBet.selected_betting_type }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Stake Amount:</span>
                <span class="font-medium">{{ userBet.bet_amount }} MMK</span>
              </div>
              <div class="flex justify-between text-green-600">
                <span class="font-bold">Potential Win:</span>
                <span class="font-bold">{{ userBet.potential_win }} MMK</span>
              </div>
            </div>

            <div class="pt-2 text-sm text-gray-500 flex justify-between">
              <span>Placed At:</span>
              <span>{{ new Date(userBet.placed_at).toLocaleString() }}</span>
            </div>
          </div>

        </div>
      </div>
    </div>
  </BetUserLayout>
</template>

<script>
import BetUserLayout from '@/Layouts/BetUserLayout.vue';
import EmptyState from '@/Components/Bet/EmptyState.vue'
import axios from 'axios';

export default {
  components: {
    BetUserLayout,
    EmptyState,
  },

  data() {
    return {

      userBet: {},
      login_user: this.$page.props.auth.user,

    };
  },
  methods: {
    fetchUsers() {
      axios.get(`/bet/api/user/history/${this.login_user.user_id}`)
        .then(res => {
          this.userBet = res.data;
          console.log("users Bets:", this.userBet);
        })
        .catch(err => {
          console.error("Error fetching users Bets:", err);
        });
    },

  },
  mounted() {
    this.fetchUsers();
  },
};
</script>
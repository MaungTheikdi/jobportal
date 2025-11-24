<template>
  <BetUserLayout>
    <div class="max-w-6xl mx-auto px-4 py-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">မောင်းစာရင်း</h1>
      
      <EmptyState v-if="userBets.length === 0" />
      
      <div v-for="(group, index) in userBets" :key="group.group_code" class="mb-8 bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Group Header -->
        <div class="bg-indigo-600 px-6 py-4 border-b flex flex-wrap items-center justify-between gap-4">
          <div class="flex items-center space-x-4">
            <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-3 py-1 rounded-full">
              #{{ group.group_code }}
            </span>
            <span :class="statusBadgeClasses(group.status)">
              {{ group.status }}
            </span>
          </div>
          
          <div class="text-sm text-white space-x-6">            
            <span><strong>Stake:</strong> {{ formatCurrency(group.bet_amount) }}</span>
			<span><strong>Placed:</strong> {{ formatDateTime(group.placed_at) }}</span>
            <span v-if="group.potential_win" class="font-semibold text-green-600">
              <strong>Potential Win:</strong> {{ formatCurrency(group.potential_win) }}
            </span>
          </div>
        </div>

        <!-- Bets List -->
        <div class="divide-y">
          <div v-for="(userBet, i) in group.bets" :key="i" class="p-6 hover:bg-gray-50 transition-colors">
            <div class="flex flex-col md:flex-row gap-6">
              <!-- Match Info -->
              <div class="flex-1">
                <div class="flex items-center justify-between mb-4">
                  <!-- Home Team -->
                  <div class="text-center flex-1">
                    <img :src="userBet.match.home_team.logo_url" class="h-12 mx-auto mb-2" alt="Home team">
                    <p class="font-medium text-gray-800">{{ userBet.match.home_team.team_name }}</p>
                    <HandicapBadge 
                      v-if="userBet.bet_type_id === 1 && userBet.is_home_team === 1"
                      :value="userBet.handicap_value" 
                      :percentage="userBet.handicap_percentage" />
                  </div>

                  <!-- Match Info -->
                  <div class="px-4 text-center">
                    <div class="bg-gray-100 rounded-lg px-3 py-1">
                      <p class="text-xs text-gray-500">Match Date</p>
                      <p class="text-sm font-bold">{{ formatDateTime(userBet.match_date) }}</p>
                    </div>
                    <p class="text-xl font-bold my-2">VS</p>
                    <HandicapBadge 
                      v-if="userBet.bet_type_id === 2"
                      :value="userBet.handicap_value" 
                      :percentage="userBet.handicap_percentage" />
                  </div>

                  <!-- Away Team -->
                  <div class="text-center flex-1">
                    <img :src="userBet.match.away_team.logo_url" class="h-12 mx-auto mb-2" alt="Away team">
                    <p class="font-medium text-gray-800">{{ userBet.match.away_team.team_name }}</p>
                    <HandicapBadge 
                      v-if="userBet.bet_type_id === 1 && userBet.is_home_team === 0"
                      :value="userBet.handicap_value" 
                      :percentage="userBet.handicap_percentage" />
                  </div>
                </div>
              </div>

              <!-- Bet Details -->
              <BetDetails 
                :type="userBet.bet_type.type_name"
                :selection="userBet.selected_team_name || userBet.selected_betting_type"/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BetUserLayout>
</template>

<script>
import BetUserLayout from '@/Layouts/BetUserLayout.vue'
import HandicapBadge from '@/Components/Bet/HandicapBadge.vue'
import EmptyState from '@/Components/Bet/EmptyState.vue'
import BetDetails from '@/Components/Bet/BetDetails.vue'
import { formatDateTime, formatCurrency, statusBadgeClasses } from '@/utils/formatters'

export default {
  components: {
    BetUserLayout,
    HandicapBadge,
    EmptyState,
    BetDetails,
  },

  data() {
    return {
      userBets: [],
      login_user: this.$page.props.auth.user,
    }
  },
  
  methods: {
    fetchUserMultiBets() {
      axios.get(`/bet/api/user/multibethistory/${this.login_user.user_id}`)
        .then(res => {
          this.userBets = res.data
          console.log("Fetched user multi bets:", JSON.stringify(this.userBets, null, 2))
        })
        .catch(err => {
          console.error("Error fetching grouped bets:", err)
        })
    }
  },
  
  mounted() {
    this.fetchUserMultiBets()
  },

  // Make helpers available in template
  setup() {
    return {
      formatDateTime,
      formatCurrency,
      statusBadgeClasses
    }
  }
}
</script>
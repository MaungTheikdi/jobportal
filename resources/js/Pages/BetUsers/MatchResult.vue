<template>
  <BetUserLayout>
    <div class="bg-gray-100 min-h-screen px-4 py-8">
      <div class="w-full max-w-md mx-auto space-y-6">
        <!-- Group by League -->
        <div v-for="(matches, league) in groupedMatches" :key="league" class="bg-white rounded-2xl shadow-xl overflow-hidden">
          <!-- League Header -->
          <div class="bg-indigo-600 p-4">
            <h2 class="text-lg font-bold text-white text-center">{{ league }}</h2>
          </div>
          
          <!-- Matches in this League -->
          <div v-for="(match, index) in matches" :key="index" class="p-6 border-b">
            <div class="flex items-center justify-between mb-4">
              <div class="text-center flex-1">
                <img :src="match.home_team.logo_url" class="h-12 mx-auto mb-2" alt="Home team">
                <p class="font-medium text-gray-800">{{ match.home_team.team_name }}</p>
              </div>

              <div class="px-4 text-center">
                <div class="bg-gray-100 rounded-lg px-3 py-1">
                  <p class="text-xs text-gray-500">Match Date</p>
                  <p class="text-sm font-bold">{{ new Date(match.match_date).toLocaleString() }}</p>
                </div>
                <p class="text-xl font-bold my-2">{{ match.home_score }}-{{ match.away_score }}</p>
              </div>

              <div class="text-center flex-1">
                <img :src="match.away_team.logo_url" class="h-12 mx-auto mb-2" alt="Away team">
                <p class="font-medium text-gray-800">{{ match.away_team.team_name }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BetUserLayout>
</template>

<script>
import BetUserLayout from '@/Layouts/BetUserLayout.vue';
import axios from 'axios';

export default {
  components: {
    BetUserLayout,
  },
  
  data() {
    return {
      teamMatches: [],
      login_user: this.$page.props.auth.user,
    };
  },
  computed: {
    groupedMatches() {
      const groups = {};
      this.teamMatches.forEach(match => {
        const league = match.league || 'Other Leagues';
        if (!groups[league]) {
          groups[league] = [];
        }
        groups[league].push(match);
      });
      return groups;
    }
  },
  methods: {
    fetchTeamMatches() {
      axios.get('/bet/api/user/match_result')
        .then(res => {
          this.teamMatches = res.data;
          console.log("teamMatches:", this.teamMatches);
        })
        .catch(err => {
          console.error("Error fetching match results:", err);
        });
    },
  },
  mounted() {
    this.fetchTeamMatches();
  },
};
</script>

<style scoped>
/* Add any custom styles here */
</style>
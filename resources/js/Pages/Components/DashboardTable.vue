<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Bet Management</h1>
    
    <div class="bg-white rounded shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bet Details</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
            <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Potential Win</th> -->
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="bet in bets.data" :key="bet.bet_id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <img 
                    class="h-10 w-10 rounded-full" 
                    :src="bet.match.home_team.logo_url" 
                    :alt="bet.match.home_team.team_name"
                  >
                </div>
                <div class="ml-4">

                  <div  
                    v-if="bet.bet_type_id === 1 && bet.is_home_team === 1"
                    class="text-sm font-medium text-gray-900"
                    >
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ bet.handicap_value }} {{ bet.handicap_percentage .includes('-') ? bet.handicap_percentage : '+' + bet.handicap_percentage }}</span>
                    
                    <span 
                      :class="[
                        'text-sm font-medium', 
                        bet.selected_team_id === bet.match.home_team_id ? 'text-blue-500 font-bold' : 'text-gray-500'
                      ]"
                    >
                      {{ bet.match.home_team.team_name }}
                    </span>

                    <span v-if="bet.match.home_score !== null" class="bg-yellow-100 text-yellow-800 px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ bet.match.home_score }} - {{ bet.match.away_score }}
                    </span>
                    <span v-else> VS </span>

                    <span 
                      :class="[
                        'text-sm font-medium', 
                        bet.selected_team_id === bet.match.away_team_id ? 'text-blue-500 font-bold' : 'text-gray-500'
                      ]"
                    >
                      {{ bet.match.away_team.team_name }}
                    </span>
                  </div>

                  <div  
                    v-if="bet.bet_type_id === 1 && bet.is_home_team === 0"
                    class="text-sm font-medium text-gray-900"
                    >

                    <span 
                      :class="[
                        'text-sm font-medium', 
                        bet.selected_team_id === bet.match.home_team_id ? 'text-blue-500 font-bold' : 'text-gray-500'
                      ]"
                    >
                      {{ bet.match.home_team.team_name }}
                    </span>

                    <span v-if="bet.match.home_score !== null" class="bg-yellow-100 text-yellow-800 px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ bet.match.home_score }} - {{ bet.match.away_score }}
                    </span>
                    <span v-else> VS </span>

                    <span 
                      :class="[
                        'text-sm font-medium', 
                        bet.selected_team_id === bet.match.away_team_id ? 'text-blue-500 font-bold' : 'text-gray-500'
                      ]"
                    >
                      {{ bet.match.away_team.team_name }}
                    </span>

                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ bet.handicap_value }} {{ bet.handicap_percentage .includes('-') ? bet.handicap_percentage : '+' + bet.handicap_percentage }}</span>
                  </div>

                  <div  
                    v-if="bet.bet_type_id === 2"
                    class="text-sm font-medium text-gray-900"
                    >
                    {{ bet.match.home_team.team_name }}
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ bet.handicap_value }} {{ bet.handicap_percentage .includes('-') ? bet.handicap_percentage : '+' + bet.handicap_percentage }}</span>
                    {{ bet.match.away_team.team_name }}
                  </div>

                  <div class="text-sm text-gray-500">
                    {{ formatDate(bet.match_date) }}
                  </div>

                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ bet.bet_user.username }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">
                <span class="font-semibold">{{ bet.selected_team_name }}</span>
                <!-- <span v-if="bet.handicap_value"> ({{ bet.handicap_value }})</span> -->
              </div>
              <div class="text-sm text-gray-500">
                {{ getBetTypeName(bet.bet_type_id) }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatCurrency(bet.bet_amount) }}
            </td>
            <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatCurrency(bet.potential_win) }}
            </td> -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="statusBadgeClass(bet.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ bet.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button @click="openEditModal(bet)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex justify-between items-center">
      <div class="text-sm text-gray-500">
        Showing {{ bets.from }} to {{ bets.to }} of {{ bets.total }} bets
      </div>
      <div class="flex space-x-2">
        <button 
          @click="fetchBets(bets.current_page - 1)" 
          :disabled="bets.current_page === 1"
          class="px-4 py-2 border rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
          Previous
        </button>
        <button 
          @click="fetchBets(bets.current_page + 1)" 
          :disabled="bets.current_page === bets.last_page"
          class="px-4 py-2 border rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Update Bet Status
            </h3>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">
                Match Score
              </label>
              <div class="flex items-center">
                <input v-model="editForm.home_score" type="number" class="shadow appearance-none border rounded w-16 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <span class="mx-2">-</span>
                <input v-model="editForm.away_score" type="number" class="shadow appearance-none border rounded w-16 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              </div>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">
                Bet Status
              </label>
              <select v-model="editForm.status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option v-for="option in statusOptions" :value="option" :key="option">
                  {{ option }}
                </option>
              </select>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">
                Amount
              </label>
              <input 
                v-model="editForm.transaction_amount" type="number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              </input>
            </div>

          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="updateBet" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
              Save
            </button>
            <button @click="showModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      bets: {},
      statusOptions: [],
      showModal: false,
      editForm: {
        bet_id: null,
        home_score: null,
        away_score: null,
        status: 'pending',
        transaction_amount: null,
      },
      
    }
  },
  created() {
    this.fetchBets();
  },
  methods: {
    fetchBets(page = 1) {
      axios.get(`/admin/bets?page=${page}`)
        .then(response => {
			console.log("table : ", response);
          this.bets = response.data.bets;

          this.statusOptions = response.data.statusOptions;
        });
    },
    openEditModal(bet) {
      this.editForm = {
        bet_id: bet.bet_id,
        home_score: bet.match.home_score,
        away_score: bet.match.away_score,
        status: bet.status
      };
      this.showModal = true;
    },
    updateBet() {
      axios.put(`/admin/bets/${this.editForm.bet_id}`, this.editForm)
        .then(() => {
          this.showModal = false;
          this.fetchBets(this.bets.current_page);
        });
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    formatCurrency(amount) {
      return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'MMK' }).format(amount);
    },
    statusBadgeClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        won: 'bg-green-100 text-green-800',
        lost: 'bg-red-100 text-red-800',
        draw: 'bg-blue-100 text-blue-800',
        void: 'bg-gray-100 text-gray-800'
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    },
    getBetTypeName(betTypeId) {
      // You would typically fetch this from your API or have a mapping
      const types = {
        1: 'ဘော်ဒီ',
        2: 'ဂိုးပေါင်း',

      };
      return types[betTypeId] || 'Unknown';
    }
  }
}
</script>
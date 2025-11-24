<template>
  <BetUserLayout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Bet History</h1>
            <p class="text-gray-500 dark:text-gray-400">Track all your betting activities</p>
          </div>
          <div class="mt-4 sm:mt-0 flex space-x-2">
            <select 
              v-model="filterStatus"
              class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="all">All Bets</option>
              <option value="pending">Pending</option>
              <option value="won">Won</option>
              <option value="lost">Lost</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <input
              type="date"
              v-model="filterDate"
              class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Bets</p>
                <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ stats.totalBets }}</p>
              </div>
              <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900">
                <TicketIcon class="h-6 w-6 text-indigo-600 dark:text-indigo-300" />
              </div>
            </div>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Won</p>
                <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ stats.wonBets }}</p>
              </div>
              <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                <CheckCircleIcon class="h-6 w-6 text-green-600 dark:text-green-300" />
              </div>
            </div>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Lost</p>
                <p class="text-2xl font-semibold text-red-600 dark:text-red-400">{{ stats.lostBets }}</p>
              </div>
              <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                <XCircleIcon class="h-6 w-6 text-red-600 dark:text-red-300" />
              </div>
            </div>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
                <p class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400">{{ stats.pendingBets }}</p>
              </div>
              <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                <ClockIcon class="h-6 w-6 text-yellow-600 dark:text-yellow-300" />
              </div>
            </div>
          </div>
        </div>

        <!-- Bet History Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th 
                    v-for="header in headers" 
                    :key="header.key"
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                    @click="sortBy(header.key)"
                  >
                    <div class="flex items-center">
                      {{ header.label }}
                      <span v-if="sortColumn === header.key">
                        <ArrowUpIcon v-if="sortDirection === 'asc'" class="ml-1 h-4 w-4" />
                        <ArrowDownIcon v-else class="ml-1 h-4 w-4" />
                      </span>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="bet in filteredBets" :key="bet.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ formatDate(bet.date) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <img :src="bet.homeTeamLogo" class="h-6 w-6 mr-2" :alt="bet.homeTeam">
                      <span class="text-sm text-gray-900 dark:text-white">{{ bet.homeTeam }}</span>
                      <span class="mx-2 text-gray-500">vs</span>
                      <img :src="bet.awayTeamLogo" class="h-6 w-6 mr-2" :alt="bet.awayTeam">
                      <span class="text-sm text-gray-900 dark:text-white">{{ bet.awayTeam }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-white">{{ bet.selection }}</div>
                    <div class="text-xs text-gray-500">{{ bet.market }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900 dark:text-white">{{ bet.odds }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ formatCurrency(bet.stake) }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium" :class="{
                      'text-green-600 dark:text-green-400': bet.status === 'won',
                      'text-red-600 dark:text-red-400': bet.status === 'lost',
                      'text-yellow-600 dark:text-yellow-400': bet.status === 'pending',
                      'text-gray-500': bet.status === 'cancelled'
                    }">
                      {{ formatCurrency(bet.payout) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" :class="{
                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': bet.status === 'won',
                      'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': bet.status === 'lost',
                      'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': bet.status === 'pending',
                      'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': bet.status === 'cancelled'
                    }">
                      {{ formatStatus(bet.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button 
                      @click="viewBetDetails(bet)"
                      class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                      Details
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                  Showing <span class="font-medium">{{ pagination.from }}</span> to <span class="font-medium">{{ pagination.to }}</span> of <span class="font-medium">{{ pagination.total }}</span> results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <button
                    @click="prevPage"
                    :disabled="pagination.currentPage === 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700"
                    :class="{ 'cursor-not-allowed opacity-50': pagination.currentPage === 1 }"
                  >
                    <span class="sr-only">Previous</span>
                    <ChevronLeftIcon class="h-5 w-5" />
                  </button>
                  <button
                    v-for="page in visiblePages"
                    :key="page"
                    @click="goToPage(page)"
                    :class="{
                      'z-10 bg-indigo-50 dark:bg-indigo-900 border-indigo-500 dark:border-indigo-600 text-indigo-600 dark:text-indigo-300': pagination.currentPage === page,
                      'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700': pagination.currentPage !== page
                    }"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                  >
                    {{ page }}
                  </button>
                  <button
                    @click="nextPage"
                    :disabled="pagination.currentPage === pagination.lastPage"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700"
                    :class="{ 'cursor-not-allowed opacity-50': pagination.currentPage === pagination.lastPage }"
                  >
                    <span class="sr-only">Next</span>
                    <ChevronRightIcon class="h-5 w-5" />
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bet Details Modal -->
      <!-- <BetDetailsModal 
        :show="showModal" 
        :bet="selectedBet" 
        @close="showModal = false" 
      /> -->
    </div>
  </BetUserLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import BetUserLayout from '@/Layouts/BetUserLayout.vue';
//import BetDetailsModal from '@/Components/BetDetailsModal.vue';
import { 
  TicketIcon,
  CheckCircleIcon,
  XCircleIcon,
  ClockIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ArrowUpIcon,
  ArrowDownIcon
} from '@heroicons/vue/24/outline';

// Sample data - replace with your actual data fetching
const bets = ref([
  {
    id: 1,
    date: '2023-05-15T14:30:00',
    homeTeam: 'Manchester United',
    homeTeamLogo: 'https://www.logo.wine/a/logo/Manchester_United_F.C./Manchester_United_F.C.-Logo.wine.svg',
    awayTeam: 'Manchester City',
    awayTeamLogo: 'https://www.logo.wine/a/logo/Manchester_City_F.C./Manchester_City_F.C.-Logo.wine.svg',
    selection: 'Manchester United',
    market: 'Match Winner',
    odds: 2.5,
    stake: 10000,
    payout: 25000,
    status: 'won'
  },
  // Add more bet objects...
]);

const stats = ref({
  totalBets: 24,
  wonBets: 12,
  lostBets: 8,
  pendingBets: 4
});

const headers = ref([
  { key: 'date', label: 'Date' },
  { key: 'teams', label: 'Match' },
  { key: 'selection', label: 'Selection' },
  { key: 'odds', label: 'Odds' },
  { key: 'stake', label: 'Stake' },
  { key: 'payout', label: 'Payout' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: 'Actions' }
]);

const filterStatus = ref('all');
const filterDate = ref('');
const sortColumn = ref('date');
const sortDirection = ref('desc');
const showModal = ref(false);
const selectedBet = ref(null);
const pagination = ref({
  currentPage: 1,
  perPage: 10,
  total: 0,
  lastPage: 1,
  from: 1,
  to: 10
});

// Computed properties
const filteredBets = computed(() => {
  let result = [...bets.value];
  
  // Apply status filter
  if (filterStatus.value !== 'all') {
    result = result.filter(bet => bet.status === filterStatus.value);
  }
  
  // Apply date filter
  if (filterDate.value) {
    const filterDateObj = new Date(filterDate.value);
    result = result.filter(bet => {
      const betDate = new Date(bet.date);
      return betDate.toDateString() === filterDateObj.toDateString();
    });
  }
  
  // Apply sorting
  result.sort((a, b) => {
    const aValue = a[sortColumn.value];
    const bValue = b[sortColumn.value];
    
    if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1;
    if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1;
    return 0;
  });
  
  // Update pagination totals
  pagination.value.total = result.length;
  pagination.value.lastPage = Math.ceil(result.length / pagination.value.perPage);
  
  // Apply pagination
  const start = (pagination.value.currentPage - 1) * pagination.value.perPage;
  const end = start + pagination.value.perPage;
  
  pagination.value.from = start + 1;
  pagination.value.to = Math.min(end, result.length);
  
  return result.slice(start, end);
});

const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  let start = Math.max(1, pagination.value.currentPage - Math.floor(maxVisible / 2));
  let end = Math.min(pagination.value.lastPage, start + maxVisible - 1);
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1);
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

// Methods
const sortBy = (column) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortColumn.value = column;
    sortDirection.value = 'asc';
  }
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString('en-US', options);
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US').format(amount) + ' Ks';
};

const formatStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const viewBetDetails = (bet) => {
  selectedBet.value = bet;
  showModal.value = true;
};

const prevPage = () => {
  if (pagination.value.currentPage > 1) {
    pagination.value.currentPage--;
  }
};

const nextPage = () => {
  if (pagination.value.currentPage < pagination.value.lastPage) {
    pagination.value.currentPage++;
  }
};

const goToPage = (page) => {
  pagination.value.currentPage = page;
};

// Initialize
onMounted(() => {
  // Fetch bet history data from API
  // fetchBetHistory();
});
</script>
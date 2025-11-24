<template>
  <BetUserLayout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Transaction History</h1>
            <p class="text-gray-500 dark:text-gray-400">All your deposit, withdrawal, and betting transactions</p>
          </div>
          <div class="mt-4 sm:mt-0">
            <select 
              v-model="filterType"
              class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="all">All Transactions</option>
              <option value="deposit">Deposits</option>
              <option value="withdrawal">Withdrawals</option>
              <option value="bet">Bets</option>
              <option value="win">Wins</option>
            </select>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transactions</p>
                <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ stats.totalTransactions }}</p>
              </div>
              <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900">
                <CreditCardIcon class="h-6 w-6 text-indigo-600 dark:text-indigo-300" />
              </div>
            </div>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Deposits</p>
                <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ formatCurrency(stats.totalDeposits) }}</p>
              </div>
              <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                <ArrowDownIcon class="h-6 w-6 text-green-600 dark:text-green-300" />
              </div>
            </div>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Withdrawals</p>
                <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ formatCurrency(stats.totalWithdrawals) }}</p>
              </div>
              <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                <ArrowUpIcon class="h-6 w-6 text-blue-600 dark:text-blue-300" />
              </div>
            </div>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Balance</p>
                <p class="text-2xl font-semibold text-purple-600 dark:text-purple-400">{{ formatCurrency(stats.netBalance) }}</p>
              </div>
              <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                <ScaleIcon class="h-6 w-6 text-purple-600 dark:text-purple-300" />
              </div>
            </div>
          </div>
        </div>

        <!-- Transaction Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Date & Time
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Type
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Amount
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Reference
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="transaction in filteredTransactions" :key="transaction.transaction_id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-white">
                      {{ formatDateTime(transaction.created_at) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div :class="getTransactionTypeIcon(transaction.transaction_type)" class="flex-shrink-0 h-5 w-5 mr-2">
                        <component :is="getTransactionTypeIconComponent(transaction.transaction_type)" class="h-5 w-5" />
                      </div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ formatTransactionType(transaction.transaction_type) }}
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium" :class="getAmountColor(transaction.transaction_type)">
                      {{ formatCurrency(transaction.amount) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusColor(transaction.status)">
                      {{ formatStatus(transaction.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ transaction.reference || 'N/A' }}
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredTransactions.length === 0">
                  <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    No transactions found
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
              <button 
                @click="prevPage" 
                :disabled="pagination.currentPage === 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.currentPage === 1 }"
              >
                Previous
              </button>
              <button 
                @click="nextPage" 
                :disabled="pagination.currentPage === pagination.lastPage"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.currentPage === pagination.lastPage }"
              >
                Next
              </button>
            </div>
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
    </div>
  </BetUserLayout>
</template>

<script>
import BetUserLayout from '@/Layouts/BetUserLayout.vue';
import axios from 'axios';
import {
  CreditCardIcon,
  ArrowDownIcon,
  ArrowUpIcon,
  ScaleIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  TicketIcon,
  GiftIcon
} from '@heroicons/vue/24/outline';

export default {
  components: {
    BetUserLayout,
    CreditCardIcon,
    ArrowDownIcon,
    ArrowUpIcon,
    ScaleIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    TicketIcon,
    GiftIcon
  },

  data() {
    return {
      userId: this.$page.props.auth.user.user_id,
      filterType: 'all',
      transactions: [],
      pagination: {
        currentPage: 1,
        perPage: 10,
        total: 0,
        lastPage: 1,
        from: 1,
        to: 10
      }
    }
  },

  computed: {
    stats() {
      const result = {
        totalTransactions: this.transactions.length,
        totalDeposits: 0,
        totalWithdrawals: 0,
        totalBets: 0,
        totalWins: 0,
        netBalance: 0
      };

      this.transactions.forEach(transaction => {
        if (transaction.transaction_type === 'deposit') {
          result.totalDeposits += Number(transaction.amount);
          result.netBalance += Number(transaction.amount);
        } else if (transaction.transaction_type === 'withdrawal') {
          result.totalWithdrawals += Number(transaction.amount);
          result.netBalance -= Number(transaction.amount);
        } else if (transaction.transaction_type === 'bet') {
          result.totalBets += 1;
          result.netBalance -= Number(transaction.amount);
        } else if (transaction.transaction_type === 'win') {
          result.totalWins += 1;
          result.netBalance += Number(transaction.amount);
        }
      });

      return result;
    },

    filteredTransactions() {
      let result = [...this.transactions];

      if (this.filterType !== 'all') {
        result = result.filter(t => t.transaction_type === this.filterType);
      }

      const start = (this.pagination.currentPage - 1) * this.pagination.perPage;
      const end = start + this.pagination.perPage;

      return result.slice(start, end);
    },

    visiblePages() {
      const pages = [];
      const maxVisible = 5;
      let start = Math.max(1, this.pagination.currentPage - Math.floor(maxVisible / 2));
      let end = Math.min(this.pagination.lastPage, start + maxVisible - 1);

      if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1);
      }

      for (let i = start; i <= end; i++) {
        pages.push(i);
      }

      return pages;
    }
  },

  methods: {
    formatCurrency(amount) {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
      }).format(amount).replace('$', '') + ' Ks';
    },

    formatDateTime(dateString) {
      const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      };
      return new Date(dateString).toLocaleDateString('en-US', options);
    },

    formatTransactionType(type) {
      const types = {
        deposit: 'Deposit',
        withdrawal: 'Withdrawal',
        bet: 'Bet Placed',
        win: 'Bet Won'
      };
      return types[type] || type;
    },

    formatStatus(status) {
      return status.charAt(0).toUpperCase() + status.slice(1);
    },

    getStatusColor(status) {
      const colors = {
        completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
      };
      return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    },

    getAmountColor(type) {
      const colors = {
        deposit: 'text-green-600 dark:text-green-400',
        win: 'text-green-600 dark:text-green-400',
        withdrawal: 'text-blue-600 dark:text-blue-400',
        bet: 'text-red-600 dark:text-red-400'
      };
      return colors[type] || 'text-gray-600 dark:text-gray-400';
    },

    getTransactionTypeIcon(type) {
      const colors = {
        deposit: 'text-green-500',
        withdrawal: 'text-blue-500',
        bet: 'text-red-500',
        win: 'text-purple-500'
      };
      return colors[type] || 'text-gray-500';
    },

    getTransactionTypeIconComponent(type) {
      const icons = {
        deposit: 'ArrowDownIcon',
        withdrawal: 'ArrowUpIcon',
        bet: 'TicketIcon',
        win: 'GiftIcon'
      };
      return icons[type] || 'CreditCardIcon';
    },

    prevPage() {
      if (this.pagination.currentPage > 1) {
        this.pagination.currentPage--;
        this.updatePagination();
      }
    },

    nextPage() {
      if (this.pagination.currentPage < this.pagination.lastPage) {
        this.pagination.currentPage++;
        this.updatePagination();
      }
    },

    goToPage(page) {
      this.pagination.currentPage = page;
      this.updatePagination();
    },

    updatePagination() {
      const totalItems = this.filterType === 'all' 
        ? this.transactions.length 
        : this.transactions.filter(t => t.transaction_type === this.filterType).length;
      
      this.pagination.total = totalItems;
      this.pagination.lastPage = Math.ceil(totalItems / this.pagination.perPage);
      this.pagination.from = (this.pagination.currentPage - 1) * this.pagination.perPage + 1;
      this.pagination.to = Math.min(
        this.pagination.currentPage * this.pagination.perPage,
        totalItems
      );
    },

    fetchTransaction() {
      axios.get(`/bet/api/user/transaction/${this.userId}`)
        .then(res => {
          this.transactions = res.data.transactions || [];
          this.updatePagination();
        })
        .catch(err => {
          console.error("Error fetching user transactions:", err);
        });
    }
  },

  watch: {
    filterType() {
      this.pagination.currentPage = 1;
      this.updatePagination();
    }
  },

  mounted() {
    this.fetchTransaction();
  }
}
</script>
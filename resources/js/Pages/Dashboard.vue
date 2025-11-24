<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>

        <div class="min-h-screen bg-gray-50 p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                    <div class="text-sm text-gray-500">
                        Last Updated: {{ new Date().toLocaleString() }}
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                    <SummaryCard title="Total User" 
                        :value="summary.total_transactions"
                        icon="🔢"
                        :trend="summary.total_trend"
                        :change="summary.total_change"
                    />

                    <SummaryCard 
                        title="Total Deposits"
                        :value="`${summary.total_deposits.toLocaleString()} MMK`"
                        icon="💹"
                        :trend="summary.deposit_trend"
                        :change="summary.deposit_change"
                    />

                    <SummaryCard 
                        title="Total Bets"
                        :value="summary.total_bets"
                        icon="🎲"
                        :trend="summary.bet_trend"
                        :change="summary.bet_change"
                    />

                    <SummaryCard title="Total Wins" 
                        :value="summary.total_wins"
                        icon="🏆"
                        :trend="summary.win_trend"
                        :change="summary.win_change"
                    />

                </div>

                <!-- Calendar -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <Calendar />
                    </div>

                    <!-- Charts -->
                    <div class="bg-white p-6 rounded-lg shadow lg:col-span-2">
                        <TransactionChart :monthly-fees="monthlyFees" />
                    </div>
                </div>

                <!-- Data Table -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <DashboardTable />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue';
//import TransferChart from '@/Pages/Transfers/TransferChart.vue'
//import TransactionChart from './Components/TransactionChart.vue';
import DailyTransfers from '@/Pages/Transfers/DailyTransfers.vue';
import SummaryCard from '@/Pages/Components/SummaryCard.vue'
import Calendar from '@/Pages/Components/Calendar.vue';
import axios from 'axios';
import TransactionChart from './Components/TransactionChart.vue';
import DashboardTable from './Components/DashboardTable.vue';

// Props from controller
const props = defineProps({
    transfers: Object,
    monthlyFees: Array,
    dailyTransfers: Array,
})
// Initialize summary with default values
const summary = ref({

    total_transactions: 0,
    total_deposits: 0,
    total_withdrawals: 0,
    total_bets: 0,
    total_wins: 0,
    profit: 0,

    deposit_change: 0,
    withdrawal_change: 0,
    bet_change: 0,
    win_change: 0,
    profit_change: 0,
    total_change: 0,

    deposit_trend: 'neutral',
    withdrawal_trend: 'neutral',
    bet_trend: 'neutral',
    win_trend: 'neutral',
    profit_trend: 'neutral',
    total_trend: 'neutral',
})

// Fetching summary data
const fetchSummary = async () => {
    try {
        const response = await axios.get('/dashboard/summary');
        const data = response.data?.summary || {};

        summary.value = {
            total_transactions: parseInt(data.total_transactions || 0),
            total_deposits: parseFloat(data.total_deposits || 0),
            total_withdrawals: parseFloat(data.total_withdrawals || 0),
            total_bets: parseFloat(data.total_bets || 0),
            total_wins: parseFloat(data.total_wins || 0),
            profit: parseFloat(data.profit || 0),

            deposit_change: parseInt(data.deposit_change || 0),
            withdrawal_change: parseInt(data.withdrawal_change || 0),
            bet_change: parseInt(data.bet_change || 0),
            win_change: parseInt(data.win_change || 0),
            profit_change: parseInt(data.profit_change || 0),
            total_change: parseInt(data.total_change || 0),

            deposit_trend: data.deposit_trend || 'neutral',
            withdrawal_trend: data.withdrawal_trend || 'neutral',
            bet_trend: data.bet_trend || 'neutral',
            win_trend: data.win_trend || 'neutral',
            profit_trend: data.profit_trend || 'neutral',
            total_trend: data.total_trend || 'neutral',
        };

        console.log('Summary data fetched:', summary.value);
    } catch (error) {
        console.error('Error fetching summary:', error);
    }
};

// Fetch summary on component mount
onMounted(() => {
    fetchSummary()
})
</script>
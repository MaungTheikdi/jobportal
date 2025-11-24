<template>
	<BetUserLayout>
		<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-6 px-4 sm:px-6 lg:px-8">
			<!-- User Profile Card -->
			<div class="max-w-3xl mx-auto mb-8">
				<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
					<div class="p-6 flex items-center space-x-4">
						<div class="flex-shrink-0">
							<div
								class="h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
								<UserIcon class="h-6 w-6 text-indigo-600 dark:text-indigo-300" />
							</div>
						</div>
						<div>
							<h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ user.username }}</h2>
							<div class="flex items-center mt-1">
								<!-- <CurrencyDollarIcon class="h-5 w-5 text-yellow-500 mr-1" /> -->
								<span class="text-lg font-semibold text-gray-700 dark:text-gray-300">
									{{ user.balance }} Ks
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Quick Actions Grid -->
			<div class="max-w-3xl mx-auto grid grid-cols-2 gap-4 mb-8">
				<Link :href="route('bet.usersinglebet')"
					class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col items-center justify-center hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors">
				<TicketIcon class="h-8 w-8 text-indigo-600 dark:text-indigo-400 mb-2" />
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">ဘော်ဒီ/ဂိုး‌ပေါင်း</span>
				</Link>

				<Link :href="route('bet.usermultibet')"
					class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col items-center justify-center hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors">
				<TicketIcon class="h-8 w-8 text-indigo-600 dark:text-indigo-400 mb-2" />
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">မောင်း</span>
				</Link>

				<Link :href="route('bet.userhistory')"
					class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col items-center justify-center hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors">
				<ClockIcon class="h-8 w-8 text-indigo-600 dark:text-indigo-400 mb-2" />
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">ဘော်ဒီ/ဂိုး‌ပေါင်းစာရင်း</span>
				</Link>

				<Link :href="route('bet.userhistorymulti')"
					class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col items-center justify-center hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors">
				<ArchiveBoxIcon class="h-8 w-8 text-indigo-600 dark:text-indigo-400 mb-2" />
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">မောင်းစာရင်း</span>
				</Link>

				<Link :href="route('bet.transaction')"
					class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col items-center justify-center hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors">
				<BanknotesIcon class="h-8 w-8 text-indigo-600 dark:text-indigo-400 mb-2" />
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">ငွေစာရင်း</span>
				</Link>

				<Link :href="route('bet.match_result')"
					class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col items-center justify-center hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors">
				<ChartBarIcon class="h-8 w-8 text-indigo-600 dark:text-indigo-400 mb-2" />
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">ပွဲပြီးရလဒ်</span>
				</Link>
			</div>

			<!-- Contact Us Button -->
			<div class="max-w-3xl mx-auto">
				<Link :href="route('bet.usersinglebet')"
					class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow p-4 flex items-center justify-center transition-colors">
				<ChatBubbleLeftRightIcon class="h-5 w-5 mr-2" />
				<span class="font-medium">ဆက်သွယ်ရန်</span>
				</Link>
			</div>
		</div>
	</BetUserLayout>
</template>

<script>
import BetUserLayout from '@/Layouts/BetUserLayout.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import {
	UserIcon,
	CurrencyDollarIcon,
	TicketIcon,
	ClockIcon,
	ArchiveBoxIcon,
	BanknotesIcon,
	ChartBarIcon,
	ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline';

export default {
	components: {
		BetUserLayout,
		Link,
		//Icons
		UserIcon,
		CurrencyDollarIcon,
		TicketIcon,
		ClockIcon,
		ArchiveBoxIcon,
		BanknotesIcon,
		ChartBarIcon,
		ChatBubbleLeftRightIcon
	},

	data() {
		return {
			user: {},
			login_user: this.$page.props.auth.user,
		}
	},
	methods: {
		fetchUsers() {
			axios.get(`/bet/api/user/data/${this.login_user.user_id}`)
				.then(res => {
					this.user = res.data;
					console.log("users:", this.user);
				})
				.catch(err => {
					console.error("Error fetching users:", err);
				});
		},

	},
	mounted() {
		this.fetchUsers();
	},

}
</script>
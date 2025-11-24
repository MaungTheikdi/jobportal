<template>
	<BetUserLayout>
		<template #header>
			<div class="space-y-6 p-4 bg-gray-100 min-h-screen pb-28 relative">
				<template v-for="(group, league) in groupedMatches" :key="league">
					<div class="mb-4">
						<div class="flex items-center space-x-2 mb-2">
							<span class="text-green-500 text-xl">★</span>
							<h2 class="text-lg font-bold">{{ league }}</h2>
						</div>

						<div v-for="(match, index) in group" :key="index" class="bg-white rounded-2xl shadow p-4 mb-4">
							<div class="flex items-center justify-between mb-2">
								<span class="text-sm text-gray-600">📅 {{ match.match_date }}</span>
							</div>

							<div class="grid grid-cols-2 gap-2 items-center">
								<!-- Home Team -->
								<div class="flex items-center space-x-2 bg-gray-200 rounded-lg p-2 cursor-pointer"
									@click="toggleOption(match, 'home_team_name')"
									:class="{ 'bg-green-300': match.selected === 'home_team_name' }">
									<img v-if="match.home_team_logo" :src="match.home_team_logo" class="w-6 h-6 rounded-full" />
									<span>{{ match.home_team_name }}</span>
									<span v-if="match.options[1]?.is_home_team === 1"
										class="bg-green-500 text-white rounded-full px-2 py-1 text-xs ml-auto">
										{{ match.options[1].handicap_value }}
										{{ formatHandicap(match.options[1].handicap_percentage) }}
									</span>
								</div>

								<!-- Away Team -->
								<div class="flex items-center space-x-2 bg-gray-200 rounded-lg p-2 cursor-pointer"
									@click="toggleOption(match, 'away_team_name')"
									:class="{ 'bg-green-300': match.selected === 'away_team_name' }">
									<img v-if="match.away_team_logo" :src="match.away_team_logo" class="w-6 h-6 rounded-full" />
									<span>{{ match.away_team_name }}</span>
									<span v-if="match.options[1]?.is_home_team === 0"
										class="bg-green-500 text-white rounded-full px-2 py-1 text-xs ml-auto">
										{{ match.options[1].handicap_value }}
										{{ formatHandicap(match.options[1].handicap_percentage) }}
									</span>
								</div>

								<!-- Over/Under -->
								<div v-if="match.options[2]" class="col-span-2 flex justify-between gap-2">
									<div class="flex-1 bg-gray-200 rounded-lg p-2 text-center cursor-pointer"
										@click="toggleOption(match, 'over')"
										:class="{ 'bg-green-300': match.selected === 'over' }">Over</div>
									<div class="flex-1 bg-green-500 text-white rounded-lg p-2 text-center">
										{{ match.options[2].handicap_value }}
										{{ formatHandicap(match.options[2].handicap_percentage) }}
									</div>
									<div class="flex-1 bg-gray-200 rounded-lg p-2 text-center cursor-pointer"
										@click="toggleOption(match, 'under')"
										:class="{ 'bg-green-300': match.selected === 'under' }">Under</div>
								</div>
							</div>
						</div>
					</div>
				</template>

				<!-- Submit Area -->
				<div class="fixed bottom-0 left-0 right-0 bg-white p-4 shadow-md z-50">
					<label for="betAmount" class="block text-sm font-medium text-gray-700 mb-1">Bet Amount (each match)</label>
					<input id="betAmount" type="number" v-model="betAmount" placeholder="Enter your bet amount"
						class="w-full border border-gray-300 rounded-lg p-2 mb-4" />
					<button @click="submitMultiBets" :disabled="!!error"
						class="bg-green-500 text-white px-4 py-2 rounded-lg w-full font-semibold">Submit Bets</button>
				</div>
			</div>
		</template>
	</BetUserLayout>
</template>

<script>
import BetUserLayout from '@/Layouts/BetUserLayout.vue'
import axios from 'axios'

export default {
	components: {
		BetUserLayout,
	},
	data() {
		return {
			matches: [],
			betAmount: '',
			user_id: this.$page.props.auth.user.user_id,
			user_balance: null,
			error: '',
		}
	},
	watch: {
		betAmount(val) {
			if (parseInt(val) > this.user_balance) {
				this.error = "Total bet exceeds your balance";
			} else {
				this.error = null;
			}
		}
	},
	methods: {
		formatHandicap(value) {
			return value.includes('-') ? value : '+' + value;
		},
		toggleOption(match, option) {
			match.selected = match.selected === option ? null : option;

			let selectedTeamName = '';
			if (match.selected === 'home_team_name') {
				selectedTeamName = match.home_team_name;
			} else if (match.selected === 'away_team_name') {
				selectedTeamName = match.away_team_name;
			} else if (match.selected === 'over') {
				selectedTeamName = 'Over';
			} else if (match.selected === 'under') {
				selectedTeamName = 'Under';
			}
			console.log(`Selected: ${selectedTeamName} for Match ID: ${match.match_id}`);
		},
		// toggleOption(match, option) {
		// 	match.selected = match.selected === option ? null : option;
		// },
		fetchUserBalance() {
			axios.get(`/bet/api/user/data/${this.user_id}`).then(res => {
				this.user_balance = res.data.balance;
			});
		},
		fetchBettingMatches() {
			axios.get('api/get/single_bets').then(response => {
				const matchMap = {};

				response.data.forEach(item => {
					if (!matchMap[item.match_id]) {
						matchMap[item.match_id] = {
							match_id: item.match_id,
							match_date: item.match_date,
							league: item.league,
							home_team_id: item.home_team_id,
							home_team_name: item.home_team_name,
							home_team_logo: item.home_team_logo,
							away_team_id: item.away_team_id,
							away_team_name: item.away_team_name,
							away_team_logo: item.away_team_logo,
							is_home_team: item.is_home_team,
							option_id: item.option_id,
							options: {},
							selected: null,
						};
					}
					matchMap[item.match_id].options[item.bet_type_id] = {
						option_id: item.option_id,
						bet_type_id: item.bet_type_id,
						bet_type_name: item.bet_type_name,
						handicap_value: item.handicap_value,
						handicap_percentage: item.handicap_percentage,
						is_home_team: item.is_home_team,
					};
				});

				this.matches = Object.values(matchMap);
				console.log("matches :", this.matches);
			});
		},
		
		submitMultiBets() {
			// const selectedBets = this.matches.filter(m => m.selected);

			// if (selectedBets.length < 4) {
			// 	alert("You must select at least 4 matches to place a multi bet.");
			// 	return;
			// }

			// if (!this.betAmount || this.betAmount < 1000) {
			// 	alert("Please enter a valid bet amount.");
			// 	return;
			// }

			// const bets = selectedBets.map(match => {
			// 	const type = match.selected;
			// 	const option = Object.values(match.options).find(opt =>
			// 		(type === 'home_team_name' && opt.bet_type_id === 1 && opt.is_home_team === 1) ||
			// 		(type === 'away_team_name' && opt.bet_type_id === 1 && opt.is_home_team === 0) ||
			// 		(type === 'over' && opt.bet_type_id === 2) ||
			// 		(type === 'under' && opt.bet_type_id === 2)
			// 	);

			// 	const selectedTeamID = (type === 'home_team_name') ? match.home_team_id :
			// 							(type === 'away_team_name') ? match.away_team_id : null;

			// 	const selectedTeamName = (type === 'home_team_name') ? match.home_team_name :
			// 							  (type === 'away_team_name') ? match.away_team_name : '';

			// 	return {
			// 		user_id: this.user_id,
			// 		option_id: option.option_id,
			// 		match_id: match.match_id,
			// 		match_date: match.match_date,
			// 		is_home_team: option.is_home_team,
			// 		bet_type_id: option.bet_type_id,
			// 		handicap_value: option.handicap_value,
			// 		handicap_percentage: option.handicap_percentage,
			// 		bet_amount: this.betAmount,
			// 		selected_betting_type: type,
			// 		selected_team_id: selectedTeamID,
			// 		selected_team_name: selectedTeamName,
			// 		potential_win: this.betAmount * 2 - (this.betAmount * 0.08),
			// 		placed_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
			// 		status: 'pending',
			// 		settled_at: null,
			// 	};
			// });

			// //this.$inertia.post('/bet/multibet/confirm', { bets });
			// this.$inertia.visit('/bet/multibet/confirm', {
			// 	data: { bets: this.selectedBets }
			// });
			const selectedBets = this.matches.filter(m => m.selected);

			if (selectedBets.length < 4) {
				alert("You must select at least 4 matches to place a multi bet.");
				return;
			}

			if (!this.betAmount || this.betAmount < 1000) {
				alert("Please enter a valid bet amount.");
				return;
			}

			const bets = [];

			for (const match of selectedBets) {
				const type = match.selected;
				const option = Object.values(match.options).find(opt =>
					(type === 'home_team_name' && opt.bet_type_id === 1) ||
					(type === 'away_team_name' && opt.bet_type_id === 1) ||
					(type === 'over' && opt.bet_type_id === 2) ||
					(type === 'under' && opt.bet_type_id === 2)
				);

				if (!option) {
					alert(`No valid betting option found for match ID ${match.match_id}`);
					return;
				}

				bets.push({
					user_id: this.user_id,
					option_id: option.option_id,
					match_id: match.match_id,
					match_date: match.match_date,
					is_home_team: option.is_home_team,
					bet_type_id: option.bet_type_id,
					handicap_value: option.handicap_value,
					handicap_percentage: option.handicap_percentage,
					bet_amount: this.betAmount,
					selected_betting_type: type,
					selected_team_id: (type === 'home_team_name') ? match.home_team_id :
									(type === 'away_team_name') ? match.away_team_id : null,
					selected_team_name: (type === 'home_team_name') ? match.home_team_name :
										(type === 'away_team_name') ? match.away_team_name : '',
					potential_win: this.betAmount * 2 - (this.betAmount * 0.08),
					placed_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
					status: 'pending',
					settled_at: null,
				});
			}

			this.$inertia.post('/bet/multibet/confirm', {
				data: { bets }
			});

		},

	},
	computed: {
		groupedMatches() {
			return this.matches.reduce((acc, match) => {
				(acc[match.league] = acc[match.league] || []).push(match);
				return acc;
			}, {});
		},
		selectedMatches() {
			return this.matches.filter(m => m.selected);
		}
	},
	mounted() {
		this.fetchBettingMatches();
		this.fetchUserBalance();
	}
}
</script>

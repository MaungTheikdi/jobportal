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
								<div class="flex items-center space-x-2 bg-gray-200 rounded-lg p-2 cursor-pointer"
									@click="selectOption(match, 'home_team_name')"
									:class="{ 'bg-green-300': match.selected === 'home_team_name' }">
									<img v-if="match.home_team_logo" :src="match.home_team_logo"
										class="w-6 h-6 rounded-full" />
									<span>{{ match.home_team_name }}</span>
									<span v-if="match.options[1] && match.options[1].is_home_team === 1"
										class="bg-green-500 text-white rounded-full px-2 py-1 text-xs ml-auto">
										{{ match.options[1].handicap_value }} 
	{{ match.options[1].handicap_percentage.includes('-') ? match.options[1].handicap_percentage : '+' + match.options[1].handicap_percentage }}

									</span>
								</div>

								<div class="flex items-center space-x-2 bg-gray-200 rounded-lg p-2 cursor-pointer"
									@click="selectOption(match, 'away_team_name')"
									:class="{ 'bg-green-300': match.selected === 'away_team_name' }">
									<img v-if="match.away_team_logo" :src="match.away_team_logo"
										class="w-6 h-6 rounded-full" />
									<span>{{ match.away_team_name }}</span>
									<span v-if="match.options[1] && match.options[1].is_home_team === 0"
										class="bg-green-500 text-white rounded-full px-2 py-1 text-xs ml-auto">
										{{ match.options[1].handicap_value }} 
	{{ match.options[1].handicap_percentage.includes('-') ? match.options[1].handicap_percentage : '+' + match.options[1].handicap_percentage }}

									</span>
								</div>

								<div v-if="match.options[2]" class="col-span-2 flex justify-between gap-2">
									<div class="flex-1 bg-gray-200 rounded-lg p-2 text-center cursor-pointer"
										@click="selectOption(match, 'over')"
										:class="{ 'bg-green-300': match.selected === 'over' }">Over</div>
									<div class="flex-1 bg-green-500 text-white rounded-lg p-2 text-center">
										{{ match.options[2].handicap_value }} 
{{ match.options[2].handicap_percentage.includes('-') ? match.options[2].handicap_percentage : '+' + match.options[2].handicap_percentage }}

									</div>
									<div class="flex-1 bg-gray-200 rounded-lg p-2 text-center cursor-pointer"
										@click="selectOption(match, 'under')"
										:class="{ 'bg-green-300': match.selected === 'under' }">Under</div>
								</div>
							</div>
						</div>

					</div>
				</template>

				<div class="fixed bottom-0 left-0 right-0 bg-white p-4 shadow-md z-50">
					<label for="betAmount" class="block text-sm font-medium text-gray-700 mb-1">Bet Amount</label>
					<input 
						id="betAmount" 
						type="number" 
						v-model="betAmount" 
						placeholder="Enter your bet amount"
						class="w-full border border-gray-300 rounded-lg p-2 mb-4" />
					<button 
						@click="submitBet"
						:disabled="!!error"
						class="bg-green-500 text-white px-4 py-2 rounded-lg w-full font-semibold">Submit
						Bet</button>
				</div>
			</div>
		</template>
	</BetUserLayout>
</template>


<script>
import BetUserLayout from '@/Layouts/BetUserLayout.vue';
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'

export default {
	components: {
		Head,
		Link,
		BetUserLayout
	},
	data() {
		return {
			matches: [],
			selectedMatch: null,
			betAmount: '',

			user_id: this.$page.props.auth.user.user_id,
			user_balance: null,
			error: '',
		}
	},

	watch: {
		betAmount(newVal) {
			if (newVal && parseInt(newVal) > this.user_balance) {
				this.error = "Bet amount exceeds your balance";
				this.betAmount = this.user_balance;
			} else {
				this.error = null;
			}
		}
	},

	methods: {

		fetchUserBalance() {
			axios.get(`/bet/api/user/data/${this.user_id}`)
				.then(res => {
					this.user_balance = res.data.balance;
					console.log("user balance:", this.user_balance);
				})
				.catch(err => {
					console.error("Error fetching users:", err);
				});
		},

		fetchBettingMatches() {
			axios.get('api/get/single_bets')
				.then(response => {
					const rawMatches = response.data;
					console.log('rawMatches: ', rawMatches);


					// Group by match_id
					const matchMap = {};

					rawMatches.forEach(item => {
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
								options: {}, // Store options by bet_type_id
								selected: null, // Track selection
							};
						}

						matchMap[item.match_id].options[item.bet_type_id] = {
							option_id: item.option_id,
							bet_type_id: item.bet_type_id,
							bet_type_name: item.bet_type_name,
							handicap_value: item.handicap_value,
							handicap_percentage: item.handicap_percentage,
							is_home_team: item.is_home_team
						};
					});

					this.matches = Object.values(matchMap);
					console.log('Merged Matches:',this.matches);
				})
				.catch(error => {
					console.error('Error fetching betting options:', error)
				});
		},
		selectOption(selectedMatch, option) {
			this.matches.forEach(match => {
				match.selected = null;
			});
			selectedMatch.selected = option;
			this.selectedMatch = selectedMatch;
		},

		submitBet() {
			if (!this.selectedMatch || !this.betAmount) {
				alert("Please select a team and enter a bet amount.");
				return;
			}

			let selectedType = this.selectedMatch.selected;
			
			let selectedTeamID = null;
			let selectedTeamName = '';

			let betTypeId = null;

			let handicap_value = '';
			let handicap_percentage = '';

			let selectedOption = null;

			if (selectedType === 'home_team_name') {
				betTypeId = 1;
				selectedTeamID = this.selectedMatch.home_team_id;
				selectedTeamName = this.selectedMatch.home_team_name;
				handicap_value = Object.values(this.selectedMatch.options).find(opt => opt.bet_type_id === 1)?.handicap_value;
				handicap_percentage = Object.values(this.selectedMatch.options).find(opt => opt.bet_type_id === 1)?.handicap_percentage;
				selectedOption = Object.values(this.selectedMatch.options).find(opt => opt.bet_type_id === 1)?.option_id;

			} else if (selectedType === 'away_team_name') {
				betTypeId = 1;
				selectedTeamID = this.selectedMatch.away_team_id;
				selectedTeamName = this.selectedMatch.away_team_name;
				handicap_value = Object.values(this.selectedMatch.options).find(opt => opt.bet_type_id === 1)?.handicap_value;
				handicap_percentage = Object.values(this.selectedMatch.options).find(opt => opt.bet_type_id === 1)?.handicap_percentage;
				selectedOption = Object.values(this.selectedMatch.options).find(opt => opt.bet_type_id === 1)?.option_id;

				
			} else if (selectedType === 'over') {
				betTypeId = 2;
				handicap_value = Object.values(this.selectedMatch.options).find(opt => opt.bet_type_id === 2)?.handicap_value;
				handicap_percentage = Object.values(this.selectedMatch.options).find(opt => opt.bet_type_id === 2)?.handicap_percentage;
				selectedOption = Object.values(this.selectedMatch.options).find(opt => opt.bet_type_id === 2)?.option_id;

			} else if (selectedType === 'under') {
				betTypeId = 2;
				handicap_value = Object.values(this.selectedMatch.options).find(opt => opt.bet_type_id === 2)?.handicap_value;
				handicap_percentage = Object.values(this.selectedMatch.options).find(opt => opt.bet_type_id === 2)?.handicap_percentage;
				selectedOption = Object.values(this.selectedMatch.options).find(opt => opt.bet_type_id === 2)?.option_id;
			}

			if (!selectedOption) {
				alert("Invalid selection.");
				return;
			}

    		const potential_win = this.betAmount + this.betAmount - (this.betAmount * 0.08);

			const betData = {
				user_id: this.$page.props.auth.user.user_id,
				option_id: selectedOption,
				match_id: this.selectedMatch.match_id,
				match_date: this.selectedMatch.match_date,
				is_home_team: this.selectedMatch.is_home_team,
				bet_type_id: betTypeId,
				handicap_value: handicap_value,
				handicap_percentage: handicap_percentage,
				bet_amount: this.betAmount,
				selected_betting_type: selectedType,
				selected_team_id: selectedTeamID,
				selected_team_name: selectedTeamName,

				potential_win: potential_win,
				placed_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
				status: 'pending',
				settled_at: null,
			};

			this.$inertia.post('/bet/single/confirm', betData);

		},

	},
	mounted() {
		this.fetchBettingMatches()
		this.fetchUserBalance();
	},
	computed: {
		groupedMatches() {
			return this.matches.reduce((groups, match) => {
				(groups[match.league] = groups[match.league] || []).push(match);
				return groups;
			}, {});
		}
	},
}
</script>
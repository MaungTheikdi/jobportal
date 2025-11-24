<!-- components/Bet/AdminBetDetails.vue -->
<template>
	<div class="flex flex-wrap items-center space-x-6 border-l md:pl-6">
		<div>
			<p class="text-xs text-gray-500 uppercase font-medium">Bet Type</p>
			<p class="font-semibold">{{ type }}</p>
		</div>

		<div>
			<p class="text-xs text-gray-500 uppercase font-medium">Selection</p>
			<p class="font-semibold">{{ selection }}</p>
		</div>
		<!-- 
    show the badget User Won or User Lost

    compare the user bet type and selection
    with home_score and away_score and the match of its handicap_value and handicap_percentage
     -->
		<div v-if="resultStatus">
			<span :class="{
				'bg-green-100 text-green-700': resultStatus === 'User Won',
				'bg-red-100 text-red-700': resultStatus === 'User Lost'
			}" class="inline-block text-xs font-semibold px-2 py-1 rounded mt-2">
				{{ resultStatus }}
			</span>
		</div>

	</div>
</template>


<script>
export default {
	props: {
		type: String,
		selection: String,

		typeId: Number,
		homeScore: Number,
		awayScore: Number,
		isHomeTeam: Number,
		handicapValue: Number,
		handicapPercentage: Number,
	},
	computed: {
		resultStatus() {
			if (this.homeScore == null || this.awayScore == null) return null;

			const totalGoals = this.homeScore + this.awayScore;

			// --- 1. Over/Under ---

				// example value of handicapValue and handicapPercentage
				// handicapValue (1 or 2 0r 3 or 4 etc)
				// handicapPercentage (-30 or +30 or -50 or +50)
				// Therefore handicapValue and handicapPercentage -> it will be like that 2 +30
				// check the totalGoals : totalGoals is the sum of home score and away scrore
				// e.g the selection is over and (the handicapValue and the handicapPercentage is 2 +30) 
				// and the home score is 2 and away scrore is 1
				// As a result, the user is won because the totalGoals is 3 and (the handicapValue and the handicapPercentage is 2 +30) 3 is completely greater than 2
				// if the home score is 1 and away scrore is 1, user won 30%
				// if (the handicapValue and the handicapPercentage is 2 -30) and totalGoals is 2, the user lost 30%
			// --- 1. Over/Under ---
  if (this.typeId === 2) {
    const threshold = parseInt(this.handicapValue) + parseInt(this.handicapPercentage) / 100;
    const isOver = this.selection.toLowerCase().includes('over');
    const isUnder = this.selection.toLowerCase().includes('under');

    if (isOver) {
      const diff = totalGoals - threshold;

      if (diff > 0.25) {
        return 'User Won';
      } else if (diff > -0.25 && diff <= 0.25) {
        return 'User Won 30%'; // or Half-win
      } else {
        return 'User Lost';
      }

    } else if (isUnder) {
      const diff = threshold - totalGoals;

      if (diff > 0.25) {
        return 'User Won';
      } else if (diff > -0.25 && diff <= 0.25) {
        return 'User Lost 30%'; // or Half-loss
      } else {
        return 'User Lost';
      }
    }

    return 'Invalid Selection';
}

			// --- 2. Handicap ---
			if (this.typeId === 1) {
				let adjustedHome = this.homeScore;
				let adjustedAway = this.awayScore;

				const handicap = this.handicapValue + this.handicapPercentage / 100;

				if (this.isHomeTeam === 1) adjustedHome += handicap;
				else adjustedAway += handicap;

				if (this.selection === 'Draw') {
					const isDraw = adjustedHome.toFixed(2) === adjustedAway.toFixed(2);
					return isDraw ? 'User Won' : 'User Lost';
				}

				const isHomeSelection = this.selection === 'Home' || this.selection === 'home';

				const didWin =
					(isHomeSelection && adjustedHome > adjustedAway) ||
					(!isHomeSelection && adjustedAway > adjustedHome);

				return didWin ? 'User Won' : 'User Lost';
			}

			return null;
		}
	}
}
</script>
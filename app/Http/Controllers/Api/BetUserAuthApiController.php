<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\BetUser;
use App\Models\BettingOption;
use App\Models\SingleBet;
use App\Models\Transaction;
use App\Models\MultiBet;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;


class BetUserAuthApiController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $betUser = BetUser::where('username', $credentials['username'])->first();

        if (!$betUser || !Hash::check($credentials['password'], $betUser->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid username or password'
            ], 401);
        }

        // Create API token (Sanctum)
        $token = $betUser->createToken('bet_user_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $betUser
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:bet_users,username',
            'password' => 'required|string|min:6',
        ]);

        $betUser = BetUser::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $betUser->createToken('bet_user_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $betUser
        ], 201);
    }

    public function profile($id)
    {
        $user = BetUser::findOrFail($id);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $formattedUser = [
            'user_id' => $user->user_id,
            'username' => $user->username,
            'balance' => $user->balance,
        ];

        return response()->json($formattedUser);
    }

    public function singleBetList()
    {
		$bettingOptions = BettingOption::with([
            'match.homeTeam',
            'match.awayTeam',
            'betType'
        ])->get();

        $formattedOptions = $bettingOptions->map(function ($option) {
            return [
                'option_id' => $option->option_id,
                'match_id' => $option->match_id,

                'match_date' => $option->match->match_date ?? null,
                'league' => $option->match->league ?? null,

                'home_team_id' => $option->match->home_team_id,
                'home_team_name' => $option->match->homeTeam->team_name ?? null,
                'home_team_logo'=> $option->match->homeTeam->logo_url ?? null,
                
                'away_team_id' => $option->match->away_team_id,
                'away_team_name' => $option->match->awayTeam->team_name ?? null,
                'away_team_logo' => $option->match->awayTeam->logo_url ?? null,

                'bet_type_id' => $option->bet_type_id,
                'bet_type_name' => $option->betType->type_name ?? null,

                'handicap_value' => $option->handicap_value,
                'handicap_percentage' => $option->handicap_percentage,
                'is_active' => $option->is_active,
                'is_home_team' => $option->is_home_team,
            ];
        });

        return response()->json($formattedOptions);
    }

    public function getUserBetsData($userId)
    {
        $user = BetUser::findOrFail($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        // Fetch all bets for the user
        $bets = SingleBet::where('user_id', $userId)
        ->with([
            'match' => function($query) {
                $query->select(
                    'match_id', 
                    'home_team_id', 
                    'away_team_id', 
                    'match_date', 
                    'league', 
                    'home_score', 
                    'away_score')
                    ->with(['homeTeam', 'awayTeam']);
            },
            'betType',
            'betOption',
            'selectedTeam'
        ])
        ->orderBy('placed_at', 'desc')
        ->get();

        // return response()->json($bets);
        // Transform the response
        $formattedBets = $bets->map(function ($bet) {
            return [
                'bet_id' => $bet->bet_id,
                'user_id' => $bet->user_id,
                'option_id' => $bet->option_id,
                'bet_amount' => $bet->bet_amount,
                'potential_win' => $bet->potential_win,
                'placed_at' => $bet->placed_at,
                'status' => $bet->status,
                'settled_at' => $bet->settled_at,
                'match_id' => $bet->match_id,
                'match_date' => $bet->match_date,
                'is_home_team' => $bet->is_home_team,
                'bet_type_id' => $bet->bet_type_id,
                'handicap_value' => $bet->handicap_value,
                'handicap_percentage' => $bet->handicap_percentage,
                'selected_betting_type' => $bet->selected_betting_type,
                'selected_team_id' => $bet->selected_team_id,
                'selected_team_name' => $bet->selected_team_name,
                'created_at' => $bet->created_at,
                'updated_at' => $bet->updated_at,
                // New flattened fields
                'home_team_name' => $bet->match?->homeTeam?->team_name,
                'home_team_logo' => $bet->match?->homeTeam?->logo_url,
                'away_team_name' => $bet->match?->awayTeam?->team_name,
                'away_team_logo' => $bet->match?->awayTeam?->logo_url,
                // Keep related objects if you still want them
                'match' => $bet->match,
                'bet_type' => $bet->betType,
                'bet_option' => $bet->betOption,
                'selected_team' => $bet->selectedTeam,
            ];
        });

        return response()->json($formattedBets);
    }

    public function createSingleBet(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'user_id' => 'required',
                'option_id' => 'required',
                'bet_amount' => 'required',
                'potential_win' => 'required',
                'placed_at' => 'required',
                'status' => 'required',
                'settled_at' => 'nullable|string',
                'match_id' => 'required',
                'match_date' => 'required',
                'is_home_team' => 'required',
                'bet_type_id' => 'required',
                'handicap_value' => 'required',
                'handicap_percentage' => 'required',
                'selected_betting_type' => 'required',
                'selected_team_id' => 'nullable|integer',
                'selected_team_name' => 'nullable|string',
            ]);
            $validatedData['created_at'] = date('Y-m-d H:i:s');
            $validatedData['updated_at'] = date('Y-m-d H:i:s');

            // Get user and verify balance
            $user = BetUser::findOrFail($validatedData['user_id']);
            if ($user->balance < $validatedData['bet_amount']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance'
                ], 400);
            }
            // Create the bet
            $singleBet = SingleBet::create($validatedData);
            // Deduct from user balance
            $user->decrement('balance', $validatedData['bet_amount']);
            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $validatedData['user_id'],
                'amount' => $validatedData['bet_amount'],
                'transaction_type' => 'bet',
                'reference' => 'BET-' . $singleBet->bet_id,
                'status' => 'completed',
                'created_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Successfully placed bet',
                'bet' => $singleBet,
                'new_balance' => $user->fresh()->balance,
                'transaction' => $transaction
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::error('Failed to place bet', [
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString(),
            //     'request_data' => $request->all(),
            //     'user_id' => $request->input('user_id')
            // ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to place bet: ' . $e->getMessage()
            ], 500);
        }
        
    }

    public function createMultiBet(Request $request)
    {
        $bets = $request->input('bets');

        if (!is_array($bets) || count($bets) < 4) {
            return response()->json([
                'message' => 'You must select at least 4 matches to place a multi bet.'
            ], 422);
        }

        // Generate user_multi_grouping_code like YmdHis
        $groupCode = now()->format('YmdHis');

        DB::beginTransaction();

        try {
            // Get user and verify balance
            $user = BetUser::findOrFail($bets[0]['user_id']);
            if ($user->balance < $bets[0]['bet_amount']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance '.$user->balance
                ], 400);
            }
            // Deduct from user balance
            $user->decrement('balance', $bets[0]['bet_amount']);
            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $bets[0]['user_id'],
                'amount' => $bets[0]['bet_amount'],
                'transaction_type' => 'bet',
                'reference' => 'MULTI_BET-' . $groupCode,
                'status' => 'completed',
                'created_at' => now()
            ]);

            foreach ($bets as $index => $bet) {
                $validator = Validator::make($bet, [
                    'user_id' => 'required|exists:bet_users,user_id',
                    'option_id' => 'required|exists:betting_options,option_id',
                    'bet_amount' => 'required',
                    'potential_win' => 'required',
                    'match_id' => 'required|integer',
                    'match_date' => 'required|date',
                    'is_home_team' => 'required|boolean',
                    'bet_type_id' => 'required|integer',
                    'handicap_value' => 'nullable|string',
                    'handicap_percentage' => 'nullable|string',
                    'selected_betting_type' => 'required|string',
                    'selected_team_id' => 'nullable|integer',
                    'selected_team_name' => 'nullable|string',
                    'placed_at' => 'required|date',
                    'status' => 'required|string|in:pending,completed,failed',
                    'settled_at' => 'nullable|date',
                ]);

                if ($validator->fails()) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Validation failed for bet at index $index",
                        'errors' => $validator->errors()
                    ], 422);
                }

                $validated = $validator->validated();

                MultiBet::create([
                    'user_id' => $validated['user_id'],
                    'user_multi_grouping_code' => $groupCode,
                    'option_id' => $validated['option_id'],
                    'bet_amount' => $validated['bet_amount'],
                    'potential_win' => $validated['potential_win'],
                    'match_id' => $validated['match_id'],
                    'match_date' => $validated['match_date'],
                    'is_home_team' => $validated['is_home_team'],
                    'bet_type_id' => $validated['bet_type_id'],
                    'handicap_value' => $validated['handicap_value'],
                    'handicap_percentage' => $validated['handicap_percentage'],
                    'selected_betting_type' => $validated['selected_betting_type'],
                    'selected_team_id' => $validated['selected_team_id'] ?? null,
                    'selected_team_name' => $validated['selected_team_name'],
                    'placed_at' => $validated['placed_at'],
                    'status' => $validated['status'],
                    'settled_at' => $validated['settled_at'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Multi bet placed successfully!',
                'group_code' => $groupCode,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to place multi bet.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserMultiBetsData($userId)
    {
        $user = BetUser::findOrFail($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        // Fetch all bets for the user
        $bets = MultiBet::where('user_id', $userId)
        ->with([
            'match' => function($query) {
                $query->select(
                    'match_id', 
                    'home_team_id', 
                    'away_team_id', 
                    'match_date', 
                    'league', 
                    'home_score', 
                    'away_score')
                    ->with(['homeTeam', 'awayTeam']);
            },
            'betType',
            'betOption',
            'selectedTeam'
        ])
        ->orderBy('placed_at', 'desc')
        ->get();

        //return response()->json($bets);

        // Group by multi bet group code
        $grouped = $bets->groupBy('user_multi_grouping_code')->map(function ($group, $code) {
            return [
                'group_code' => $code,
                'placed_at' => $group->first()->placed_at,
                'bet_amount' => $group->first()->bet_amount,
                'status' => $group->first()->status,
                'bets' => $group->values(),
            ];
        })->values();

        return response()->json($grouped);
    }

}

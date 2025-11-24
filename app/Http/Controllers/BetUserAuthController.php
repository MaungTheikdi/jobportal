<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BetUser;
use App\Models\SingleBet;
use App\Models\TeamMatch;
use App\Models\Transaction;
//use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

use Illuminate\Support\Facades\Validator;
use App\Models\MultiBet;

class BetUserAuthController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('Auth/BetUserLogin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('bet_user')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            return redirect()->intended('/bet/dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::guard('bet_user')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/bet/login');
    }

    public function showRegistrationForm()
    {
        return Inertia::render('Auth/BetUserRegister');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:bet_users'],
            //'email' => ['nullable', 'string', 'email', 'max:255', 'unique:bet_users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = BetUser::create($validated);

        Auth::guard('bet_user')->login($user);

        return redirect('/bet/dashboard');
    }

    public function getUserData($id){
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

        return response()->json($bets);
    }

    // get all Match results
    public function getAllMatchResults()
    {
        $matches = TeamMatch::with(['homeTeam', 'awayTeam'])
            ->orderBy('match_date', 'desc')
            ->get();

        return response()->json($matches);
    }

    public function getUserTransaction($userId)
    {
        $transactions = Transaction::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10); // or your preferred items per page

        return response()->json([
            'success' => true,
            'user_id' => $userId,
            'transactions' => $transactions->items(),
            'pagination' => [
                'total' => $transactions->total(),
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'last_page' => $transactions->lastPage(),
            ]
        ]);
    }

    //create Multi bet
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
                    'selected_team_id' => $validated['selected_team_id'],
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

    //getUserMultiBetsData
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

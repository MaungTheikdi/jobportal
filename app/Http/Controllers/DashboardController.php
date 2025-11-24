<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
//use Inertia\Inertia;
use App\Models\SingleBet;
use App\Models\TeamMatch;
use App\Models\Team;
use App\Models\BetUser;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function chartData(Request $request)
    {
        $year = $request->query('year', date('Y'));

        $transactions = Transaction::query()
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE 0 END) as deposits,
                SUM(CASE WHEN transaction_type = 'withdrawal' THEN amount ELSE 0 END) as withdrawals,
                SUM(CASE WHEN transaction_type = 'bet' THEN amount ELSE 0 END) as bets,
                SUM(CASE WHEN transaction_type = 'win' THEN amount ELSE 0 END) as winnings,
                COUNT(*) as transaction_count
            ")
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                // Cast numeric values to float for frontend chart compatibility
                return [
                    'month' => $row->month,
                    'deposits' => (float) $row->deposits,
                    'withdrawals' => (float) $row->withdrawals,
                    'bets' => (float) $row->bets,
                    'winnings' => (float) $row->winnings,
                    'transaction_count' => (int) $row->transaction_count,
                ];
            });

        return response()->json($transactions);
    }
    public function summary(Request $request)
    {
        // Current month transactions
        $currentTransactions = Transaction::query()
            ->whereBetween('created_at', [now()->startOfYear(), now()])
            ->get();
        
        // Previous month transactions
        $previousTransactions = Transaction::query()
            ->whereBetween('created_at', [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()])
            ->get();
        
        // Current period calculations
        $currentDeposits = $currentTransactions->where('transaction_type', 'deposit')->sum('amount');
        $currentWithdrawals = $currentTransactions->where('transaction_type', 'withdrawal')->sum('amount');
        $currentBets = $currentTransactions->where('transaction_type', 'bet')->sum('amount');
        $currentWins = $currentTransactions->where('transaction_type', 'win')->sum('amount');
        $currentTotal = $currentTransactions->count();
        
        // Previous period calculations
        $previousDeposits = $previousTransactions->where('transaction_type', 'deposit')->sum('amount');
        $previousWithdrawals = $previousTransactions->where('transaction_type', 'withdrawal')->sum('amount');
        $previousBets = $previousTransactions->where('transaction_type', 'bet')->sum('amount');
        $previousWins = $previousTransactions->where('transaction_type', 'win')->sum('amount');
        $previousTotal = $previousTransactions->count();
        
        // Calculate percentage changes
        $depositChange = $previousDeposits > 0 
            ? round(($currentDeposits - $previousDeposits) / $previousDeposits * 100, 2)
            : ($currentDeposits > 0 ? 100 : 0);
        
        $withdrawalChange = $previousWithdrawals > 0
            ? round(($currentWithdrawals - $previousWithdrawals) / $previousWithdrawals * 100, 2)
            : ($currentWithdrawals > 0 ? 100 : 0);
        
        $betChange = $previousBets > 0
            ? round(($currentBets - $previousBets) / $previousBets * 100, 2)
            : ($currentBets > 0 ? 100 : 0);
        
        $winChange = $previousWins > 0
            ? round(($currentWins - $previousWins) / $previousWins * 100, 2)
            : ($currentWins > 0 ? 100 : 0);
        
        $totalChange = $previousTotal > 0
            ? round(($currentTotal - $previousTotal) / $previousTotal * 100, 2)
            : ($currentTotal > 0 ? 100 : 0);
        
        // Calculate profit (wins - bets)
        $currentProfit = $currentWins - $currentBets;
        $previousProfit = $previousWins - $previousBets;
        $profitChange = $previousProfit != 0
            ? round(($currentProfit - $previousProfit) / abs($previousProfit) * 100, 2)
            : ($currentProfit != 0 ? ($currentProfit > 0 ? 100 : -100) : 0);
        
        $summary = [
            'total_transactions' => $currentTotal,
            'total_deposits' => $currentDeposits,
            'total_withdrawals' => $currentWithdrawals,
            'total_bets' => $currentBets,
            'total_wins' => $currentWins,
            'profit' => $currentProfit,
            'deposit_change' => abs($depositChange),
            'withdrawal_change' => abs($withdrawalChange),
            'bet_change' => abs($betChange),
            'win_change' => abs($winChange),
            'profit_change' => abs($profitChange),
            'total_change' => abs($totalChange),
            'deposit_trend' => $depositChange >= 0 ? 'up' : 'down',
            'withdrawal_trend' => $withdrawalChange >= 0 ? 'up' : 'down',
            'bet_trend' => $betChange >= 0 ? 'up' : 'down',
            'win_trend' => $winChange >= 0 ? 'up' : 'down',
            'profit_trend' => $profitChange >= 0 ? 'up' : 'down',
            'total_trend' => $totalChange >= 0 ? 'up' : 'down',
        ];
        
        return response()->json([
            'summary' => $summary,
        ]);
    }

    public function index()
    {
        $bets = SingleBet::with(['betUser', 'match.homeTeam', 'match.awayTeam'])
            ->orderBy('match_date', 'desc')
            ->paginate(20);

        return response()->json([
            'bets' => $bets,
            'statusOptions' => ['pending', 'won', 'lost', 'draw', 'void']
        ]);
    }

    public function update(Request $request, $betId)
    {
        $request->validate([
            'status' => 'required|in:won,lost,draw,void',
            'home_score' => 'nullable|integer',
            'away_score' => 'nullable|integer',
            'transaction_amount' => 'nullable|integer|min:0',
        ]);

        $bet = SingleBet::findOrFail($betId);
        $match = TeamMatch::findOrFail($bet->match_id);
        $betUser = BetUser::findOrFail($bet->user_id);
        //$transactions = Transaction::where('bet_id', $betId)->first();


        // Update match scores if provided
        if ($request->has('home_score') && $request->has('away_score')) {
            $match->update([
                'home_score' => $request->home_score,
                'away_score' => $request->away_score,
                'status' => 'completed'
            ]);
        }

        // Update bet status
        $previousStatus = $bet->status;
        $bet->update([
            'status' => $request->status,
            'settled_at' => now()
        ]);

        // Process transaction if status changed to won/lost
        if ($request->status === 'won') {
            if ($request->has('transaction_amount')) {
                // create winning transaction
                $transactions = Transaction::create([
                    'user_id' => $betUser->user_id,
                    'amount' => $request->transaction_amount,
                    'transaction_type' => 'win',
                    'status' => 'completed',
                    'reference' => 'BET_WIN_' . $bet->bet_id,
                    'bet_id' => $betId
                ]);
                // Update user balance
                $betUser->increment('balance', $request->transaction_amount);
            }
        }

        return response()->json(['message' => 'Bet updated successfully']);
    }

}

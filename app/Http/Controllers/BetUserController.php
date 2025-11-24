<?php
namespace App\Http\Controllers;

use App\Models\BetUser;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Transaction;


class BetUserController extends Controller
{
	public function getView()
	{
		return Inertia::render('BetUsers/Index');
	}
    public function getLoginView()
	{
		return Inertia::render('Auth/BetLogin');
	}
    public function getDashboardView()
	{
		return Inertia::render('UserDashboard');
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = BetUser::all();
        return response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Typically returns a view for creating a user
        // return view('bet_users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'username' => 'required|unique:bet_users|max:255',
                'password' => 'required|min:8',
                'balance' => 'numeric|min:0|nullable',
            ]);

            $user = BetUser::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'balance' => $request->balance ?? 0,
                'created_at' => now(),
            ]);

            Transaction::create([
                'user_id' => $user->user_id, // Adjust if your primary key is named differently
                'amount' => $request->balance ?? 0,
                'transaction_type' => 'deposit',
                'reference' => 'new register',
                'status' => 'completed',
                'created_at' => now()
            ]);

            DB::commit();
            return response()->json($user, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to register user and create transaction.',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = BetUser::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Typically returns a view for editing a user with user data
        // $user = BetUser::findOrFail($id);
        // return view('bet_users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id)
    {
        $user = BetUser::findOrFail($user_id);

        $request->validate([
			'username' => 'required|unique:bet_users,username,' . $user_id . ',user_id|max:255',
			'password' => 'sometimes|min:8',
		]);


        $user->username = $request->username;
        if ($request->filled('password')) {
            $user->password_hash = Hash::make($request->password);
        }
        //$user->balance = $request->balance ?? $user->balance; // Update balance if provided, otherwise keep current

        $user->save();

        return response()->json($user);
    }

        /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = BetUser::findOrFail($id);
        $user->delete();

        return response()->json(null, 204); // 204 No Content response for successful deletion
    }

    public function betUserLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = BetUser::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

         // Generate token if not exists
        if (!$user->api_token) {
            $user->api_token = Str::random(60);
            $user->save();
        }

        return response()->json([
            'token' => $user->api_token,
            'user_id' => $user->user_id,
            'user' => [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'balance' => $user->balance,
            ]
        ]);

        // // Store in session
        // $request->session()->put('bet_user', $user->user_id);

        // $token = $user->createToken('bet_user_token')->plainTextToken;

        // return response()->json([
        //     'token' => $token,
        //     'user' => [
        //         'user_id' => $user->user_id,
        //         'username' => $user->username,
        //         'balance' => $user->balance,
        //     ]
        // ]);
    }
}
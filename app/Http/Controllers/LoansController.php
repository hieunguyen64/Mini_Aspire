<?php

namespace App\Http\Controllers;

use App\Models\UserLoans;

use JWTAuth;
use Illuminate\Http\Request;

class LoansController extends Controller {
    public function index() {
        $user = JWTAuth::parseToken()->authenticate()->toArray();
        $user_id = $user['id'];
        $user_loans = UserLoans::where('id', $user_id)->get()->toArray();

        print_r($user);
    }
    public function show(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        print_r($user);

        return response()->json(compact('data'), 200);
    }

    public function store(Request $request) {
        $data = "Only authorized users can see this";
        return response()->json(compact('data'), 200);
    }

    public function update(Request $request) {
        $data = "Only authorized users can see this";
        return response()->json(compact('data'), 200);
    }

    public function destroy(Request $request) {
        $data = "Only authorized users can see this";
        return response()->json(compact('data'), 200);
    }
}

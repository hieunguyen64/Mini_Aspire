<?php

namespace App\Http\Controllers;

use App\Models\UserLoans;
use App\Models\UserLoanRepayment;

use JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LoansController extends Controller {
    public function index() {
        $user = JWTAuth::parseToken()->authenticate()->toArray();
        $user_loans = UserLoans::where("user_id", $user["id"])->get()->toArray();

        return response()->json(compact("user_loans"));
    }
    public function show($id) {
        $user_id =  JWTAuth::parseToken()->authenticate()->id;
        $data = $this->get_loan_info($user_id, $id);

        return response()->json(compact("data"), 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            "loan_amount" => "required|numeric|gt:0.00",
            "duration" => "required|integer|gt:0",
            "repay_frequency" => [
                "required",
                "string",
                Rule::in(["month", "day", "year"])
            ],
            "interest_rate" => "required|numeric",
            "arrangement_fee" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $duration = $request->get("duration");
        $loan_amount =  $request->get("loan_amount");
        $interest_rate = $request->get("interest_rate");
        $repay_frequency = $request->get("repay_frequency");
        $user_loan = UserLoans::create([
            "user_id" => JWTAuth::parseToken()->authenticate()->id,
            "loan_amount" => $loan_amount,
            "duration" => $duration,
            "repay_frequency" => $request->get("repay_frequency"),
            "interest_rate" => $interest_rate,
            "arrangement_fee" => $request->get("arrangement_fee"),
            "status" => 0
        ]);

        $this_date = date("Y-m-d");
        for ($i = 1; $i <= $duration; $i++) {
            $interest_amount = $this->get_interest_amount($loan_amount, $interest_rate, $duration);
            $payment_due = $this->get_payment_due($loan_amount, $duration, $interest_amount);
            $loan_repayments[$i] = [
                "loan_id" =>  $user_loan->id,
                "due_date" => date("Y-m-d", strtotime("+" . $i . " " . $repay_frequency, strtotime($this_date))),
                "payment_due" => $payment_due,
                "status" => 0,
            ];
        }
        UserLoanRepayment::insert($loan_repayments);

        $user_loan["amortization_schedule"] = $loan_repayments;
        $data = $user_loan;

        return response()->json(compact("data"), 201);
    }

    public function update($loan_id, Request $request) {
        $validator = Validator::make($request->all(), [
            "repayment_id" => "required|integer|gt:0",
            "repay_amount" => "required|numeric|gt:0.00"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user_id = JWTAuth::parseToken()->authenticate()->id;
        $loan_info = $this->get_loan_info($user_id, $loan_id);
        if (empty($loan_info)) {
            return response()->json(["mess" => "Data not found"], 404);
        }

        $repayment_id = $request->get("repayment_id");
        $repay_amount = $request->get("repay_amount");
        $mess = "Data not found";
        $code = 404;
        foreach ($loan_info['amortization_schedule'] as $key => $value) {
            if ($repayment_id === $value['id']) {
                if ($value['status'] === 0) {
                    if ($repay_amount === $value['payment_due']) {
                        UserLoanRepayment::where('id', $repayment_id)->update([
                            "actual_due_date" => date("Y-m-d"),
                            "actual_payment_due" => $repay_amount,
                            "status" => 1
                        ]);

                        $mess = "Resource updated successfully";
                        $code = 200;
                    }

                    if ($repay_amount > $value['payment_due'] || $repay_amount < $value['payment_due']) {
                        $next_repayment = UserLoanRepayment::select("payment_due")
                            ->where('id', $repayment_id + 1)
                            ->where('loan_id', $loan_id)
                            ->where('status', 0)
                            ->first();
                        if (empty($next_repayment)) {
                            if ($repay_amount < $value['payment_due']) {
                                return response()->json(["mess" => 'Last payment is not enough'], 400);
                            }
                        }

                        $diff = $repay_amount - $value['payment_due'];
                        UserLoanRepayment::where('id', $repayment_id)->update([
                            "actual_due_date" => date("Y-m-d"),
                            "actual_payment_due" => $value['payment_due'],
                            "status" => 1
                        ]);

                        if (!empty($next_repayment)) {
                            UserLoanRepayment::where('id', $repayment_id + 1)->update([
                                "payment_due" => $next_repayment->payment_due - $diff
                            ]);
                        }

                        $mess = "Resource updated successfully";
                        $code = 200;
                    }

                    $is_completed_loan = $this->is_completed_loan($loan_id);
                    if ($is_completed_loan === true) {
                        UserLoans::where('id', $loan_id)->update(['status' => 1]);
                    }
                }
            }
        }
        return response()->json(["mess" => $mess], $code);
    }

    public function is_completed_loan($loan_id) {
        $check = UserLoanRepayment::select('id')->where('loan_id', $loan_id)->where('status', 0)->get()->toArray();
        if (empty($check)) {
            return true;
        }
        return false;
    }

    public function get_loan_info($user_id, $loan_id) {
        $loan_info = UserLoans::where("id", $loan_id)->where("user_id", $user_id)->first();
        $loan_detail_info = UserLoanRepayment::where("loan_id", $loan_id)->get()->toArray();
        if (!empty($loan_info)) {
            $loan_info["amortization_schedule"] = $loan_detail_info;
        }
        return  $loan_info ?? [];
    }

    public function get_interest_amount($loan_amount, $interest_rate, $duration) {
        return $loan_amount * $interest_rate / 100 / $duration;
    }

    public function get_payment_due($loan_amount, $duration, $interest_amount) {
        return $loan_amount / $duration + $interest_amount;
    }
}
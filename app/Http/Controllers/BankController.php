<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function updateBalance() {
        $data = $this->ammountValidator();
        $ammount = array_shift($data);

        if (abs($ammount) > auth()->payload()->get('authorized_number')) {
            return $this->error('This operation is above your authorized limit !', 403);
        }

        $account = auth()->user();
        $account->balance += $ammount;
        $balance = $account->balance;

        if ($ammount < 0) {
            return $this->checkBalance($balance);
        }

        $account->save();

        return $this->respond('Operation successfull, Transfert Completed');
    }

    public function transfer() {
        $data = $this->tranferValidation();

        if ($data['ammount'] < 0 || $data['ammount'] == null) {
            return $this->error('You cannot transfert a negativ value or inexisting value !', 403);
        }

        $sender = auth()->user();
        $receiver = User::where('account_number', $data['account_number'])->first();

        $sender->balance -= $data['ammount'];

        if ($sender->balance < 0) {
            return $this->error('Your account would be overdrawn if this transfer was effective', 403);
        }

        $receiver->balance += $data['ammount'];

        $receiver->save();
        $receiver->save();

        return $this->respond($receiver, 'Tranfers successfull. Here is your current balance');
    }

    protected function tranferValidation() {
        return request()->validate([
            'ammount' => 'integer|required',
            'account_number' => 'string|required'
        ]);
    }

    protected function ammountValidator() {
        return request()->validate([
            'ammount' => 'integer|required'
        ]);
    }

    private function checkBalance($balanceNum) {
        if ($balanceNum < 0) {
            return $this->error('Your account would be overdrawn if this transfer was effective', 403);
        }
        return true;
    }
}

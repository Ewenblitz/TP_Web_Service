<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Integer;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\JWT;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register() {
        $data = $this->registerValidation();

        $account = '11111' . $this->randId(7) . $this->randLetter();
        $pin = $this->randId(6);

        $data['account_number'] = $account;
        $data['password'] = bcrypt($pin);

        User::create($data);

        $data['password'] = $pin;

        return $this->respond($data, 'Succesfully registered');
    }

    public function login() {
        $credential = [
            'account_number' => request('account_number'),
            'password' => request('password')
        ];

        if (!auth()->attempt($credential))
            return $this->unauthorized();

        $factory = JWTFactory::customClaims([
            'account_number' => $credential['account_number'],
            'authorized_number' => '1500',
        ]);

        $payload = $factory->make();
        $token = JWTAuth::encode($payload)->get();
        return $this->respondWithToken($token);
    }

    public function logout() {
        auth()->invalidate(true);
        return $this->respond('Logged Out', 'Logged Out successfully');
    }

    private function randId($length) {
        $numbers = '0123456789';
        $numberLenght = strlen($numbers);
        $randomNumber = '';
        for ($i = 0; $i < $length; $i++) {
            $randomNumber .= $numbers[rand(0, $numberLenght -1)];
        }
        return $randomNumber;
    }

    private function randLetter() {
        $charaters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($charaters);
        $randomLetter = $charaters[rand(0, $charactersLength -1)];

        return $randomLetter;
    }

    protected function registerValidation() {
        return request()->validate([
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'birth_date' => 'string|required',
            'address' => 'string|required',
            'civility' => 'string|required'
        ]);
    }
}

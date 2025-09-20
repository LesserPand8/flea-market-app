<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // 登録後のリダイレクト先をメール認証誘導画面に変更
        return redirect('/email/verify');
    }
}

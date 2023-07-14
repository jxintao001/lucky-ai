<?php

namespace App\Http\Controllers;

use App\Transformers\UserIntegralTransformer;
use Illuminate\Support\Facades\Auth;

class UserIntegralsController extends Controller
{
    public function index()
    {
        $user_integrals = Auth::user()->integrals()->paginate(per_page());
        return $this->response()->paginator($user_integrals, new UserIntegralTransformer());
    }


}
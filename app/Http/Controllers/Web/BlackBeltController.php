<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlackBelt;

class BlackBeltController extends Controller
{
    public function index()
    {
        return view('web.black-belts.index', [
            'blackBelts' => BlackBelt::query()->active()->orderByDesc('promoted_on')->get(),
        ]);
    }
}

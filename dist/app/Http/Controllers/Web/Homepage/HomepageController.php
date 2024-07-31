<?php

namespace App\Http\Controllers\Web\Homepage;

use App\Http\Controllers\Controller;

class HomepageController extends Controller
{
    public function index() {


        return view('web.home');
    }
}

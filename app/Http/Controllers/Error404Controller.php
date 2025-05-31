<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Error404Controller extends Controller
{
    public function error404() {
        return view('admin.Error404.error404');
    } 
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Generalpolicy;


class generalpolicysController extends Controller
{
    public function generalpolicy() 
    {
        $generalpolicy = Generalpolicy::find(1);
        // return "dashboard.policys.generalpolicy";
        return view('dashboard.policys.generalpolicy', compact(['generalpolicy']));
    }

}

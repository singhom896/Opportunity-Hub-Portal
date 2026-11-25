<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;

class LogController extends Controller
{

    
    public function logs()
    {
        $histories=LoginHistory::with('user','user.role')->orderBy('created_at', 'desc')->get();
        return view('backend.logs.list',compact('histories'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\LogError;

class LogErrorController extends Controller
{
    public function index()
    {
        $logerrors = LogError::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.logerrors.logerrors', compact('logerrors'));
    }
}

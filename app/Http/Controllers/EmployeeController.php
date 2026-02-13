<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class EmployeeController extends Controller
{
    public function create()
    {
        $response = Http::get('http://api.geonames.org/countryInfoJSON', [
            'username' => 'hier',
        ]);
        $countries = $response->json('geonames');
        return view('admin.employee.create-emplyee', compact('countries'));
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleModel;

class VehicleModelController extends Controller
{
    //
    public function index(Request $request)
    {
        $search_term    = $request->input('q');
        $results        = [];
        if ($search_term){
            $results = VehicleModel::where('name', 'LIKE', '%'.$search_term.'%')->paginate(10);
        }
        else{
            $results = VehicleModel::paginate(10);
        }
        return $results;
    }
}

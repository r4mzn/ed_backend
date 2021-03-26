<?php

namespace App\Http\Controllers;

use App\Models\Gejala;
use Illuminate\Http\Request;

class GejalaController extends Controller
{
    public function index(Request $request)
    {
        $data = Gejala::whereIn('id', $request->gejala)->get();
        return response()->json($data);
    }
}

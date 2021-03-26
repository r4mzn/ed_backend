<?php

namespace App\Http\Controllers;

use App\Models\Aturan;
use App\Models\Gangguan;
use App\Models\Gejala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class GangguanController extends Controller{
    
    public function index(Request $request){
        return Gangguan::identifikasi($request->gejala);
    }
}
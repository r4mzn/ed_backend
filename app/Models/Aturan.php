<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Aturan extends Model {
    protected $table = 'aturan';
    protected $guarded = [];

    public static function gejala($id_gejala){
        return self::whereIn('aturan.id_gejala',$id_gejala)
                    ->groupBy('aturan.id_gejala')
                    ->join('gangguan', 'gangguan.id', '=', 'aturan.id_gangguan')
                    ->selectRaw('group_concat(gangguan.id) as gangguan, aturan.nilai_keyakinan')
                    ->get();
        
    }
    
}
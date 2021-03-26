<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class Gangguan extends Model {
    protected $table = 'gangguan';
    protected $guarded = [];

    public static function identifikasi($id_gejala){
        $daftar_aturan = static::dapatkanAturan($id_gejala);
        $m3 = [];
        while(!empty($daftar_aturan)){
            $m1 = static::tentukanBPA(array_shift($daftar_aturan));
            $m2 = empty($m3)?static::tentukanBPA(array_shift($daftar_aturan)):static::tentukanBPAdari($m3);
            $m3 = static::hitungKombinasiBPA($m1,$m2);
        }
        return response()->json(static::dapatkanKesimpulan($m3));
    }
    protected static function dapatkanAturan($gejala){
        return json_decode(Aturan::gejala($gejala));
    }
    protected static function tentukanBPA($e){
        $m[0] = $e;
        $m[1] = new stdClass;
        $m[1]->gangguan = implode(',', Gangguan::fod());
        $m[1]->nilai_keyakinan = 1 - $m[0]->nilai_keyakinan;
        return $m;
    }
    protected static function tentukanBPAdari($m3){
        foreach ($m3 as $key => $val) { // konvert dari array assosiative ke objek
                $m = new stdClass;
                $m->gangguan = $key;
                $m->nilai_keyakinan = $val;
                $m2[] = $m;
        }
        return $m2;
    }
    protected static function dapatkanHimpunanGangguan($m1_j,$m2_i){
        $x = explode(',', $m1_j->gangguan);
        $y = explode(',', $m2_i->gangguan);
        sort($x);
        sort($y);
        $xy = array_intersect($x, $y);
        // buat array assosiative
        return empty($xy)?"himpunan_kosong":implode(',',$xy);
    }
    protected static function hitungKombinasiBPA($m1,$m2){
        $m3 = [];
        for ($i = 0; $i < count($m2); $i++) {
            for ($j = 0; $j < 2; $j++) {
                    $himpunan_gangguan = static::dapatkanHimpunanGangguan($m1[$j],$m2[$i]);
                    if (!isset($m3[$himpunan_gangguan])) { 
                        $m3[$himpunan_gangguan] = $m1[$j]->nilai_keyakinan * $m2[$i]->nilai_keyakinan;
                    } else {
                        $m3[$himpunan_gangguan] += $m1[$j]->nilai_keyakinan * $m2[$i]->nilai_keyakinan;
                    }
            }
        }
        return static::tanpaEvidentialConflict($m3);
    }
    protected static function dapatkanDataGangguan($ids){
        return static::whereIn('id',explode(",",$ids))->get();
    }
    protected static function dapatkanKesimpulan($m3){
        array_pop($m3);
        arsort($m3);
        return [
            'gangguan' => static::dapatkanDataGangguan(array_key_first($m3)),
            'nilai_keyakinan' => $m3[array_key_first($m3)]
        ];
    }
    protected static function tanpaEvidentialConflict($m3){
        foreach ($m3 as $himpunan_gangguan => $nilai) {
            if ($himpunan_gangguan != "himpunan_kosong") {
                $m3[$himpunan_gangguan] = $nilai / (1 - (isset($m3["himpunan_kosong"]) ? $m3["himpunan_kosong"] : 0));
            }
        }
        unset($m3["himpunan_kosong"]);
        return $m3;
    }
    public static function fod(){
        return static::where('id' ,'>' ,0)->pluck('id')->toArray();
    }
}
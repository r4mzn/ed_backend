<?php

use App\Models\Gangguan;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UnitTest extends TestCase
{
    /** @test */
    public function fungsi_identifikasi_test()
    {
        $kesimpulan = Gangguan::identifikasi([7,1,4,5])->getData();
        $this->assertEquals(array_column($kesimpulan->gangguan,'id'),[1]);
        $this->assertEquals(round($kesimpulan->nilai_keyakinan,2),0.3);
    }
}
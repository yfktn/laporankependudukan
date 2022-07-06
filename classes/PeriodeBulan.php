<?php namespace Yfktn\LaporanKependudukan\Classes;

class PeriodeBulan
{
    public static function namaBulan($value = 0)
    {
        $s = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'Nopember',
            12 => 'Desember'
        ];
        if($value == 0) {
            return $s;
        } 
        return $s[$value];
    }
}
<?php namespace Yfktn\LaporanKependudukan\Classes;

trait RomanRTRWTrait
{
    public function getNamaRtrwOptions()
    {
        return [
            '01' => 'I',
            '02' => 'II',
            '03' => 'III',
            '04' => 'IV',
            '05' => 'V',
            '06' => 'VI',
            '07' => 'VII',
            '08' => 'VIII',
            '09' => 'IX',
            '10' => 'X',
            '11' => 'XI',
            '12' => 'XII',
            '13' => 'XIII',
            '14' => 'XIV',
            '15' => 'XV',
            '16' => 'XVI',
            '17' => 'XVII',
            '18' => 'XVIII',
            '19' => 'XIX',
            '20' => 'XX',
        ];
    }

    public function getNamaRtrwLabelAttribute()
    {
        $roman = $this->getNamaRtrwOptions();
        return isset($roman[$this->nama_rtrw]) ? $roman[$this->nama_rtrw]: '?';
    }
}
<?php namespace Yfktn\LaporanKependudukan\Classes;

class NamaRTRWRender 
{
    use RomanRTRWTrait;

    public function getLabelOf($value)
    {
        $roman = $this->getNamaRtrwOptions();
        return isset($roman[$value])? $roman[$value]: '?';
    }
}
<?php namespace Yfktn\LaporanKependudukan\Models;

use Backend\Facades\BackendAuth;
use Model;
use October\Rain\Exception\ApplicationException;

/**
 * Model khusus untuk report kita hilangkan kebutuhan periode bulannya!
 */
class LaporanKependudukanReport extends LaporanKependudukan
{
    /**
     * @var array Validation rules
     */
    public $rules = [
        'desa_id' => 'required',
        'periode_tahun' => 'required|min:4'
    ];

    public function getJenisLaporanOptions()
    {
        return [
            'laporan_desa_berdasarkan_periode' => 'Laporan kependudukan desa berdasarkan periode',
            'summary_laporan_desa' => 'Ringkasan laporan kependudukan desa'
        ];
    }
}

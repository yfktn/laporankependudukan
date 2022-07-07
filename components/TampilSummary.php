<?php namespace Yfktn\LaporanKependudukan\Components;

use Cms\Classes\ComponentBase;
use Yfktn\LaporanKependudukan\Classes\ReportSummaryDesaFactory;

class TampilSummary extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Komponen Tampil Summary',
            'description' => 'Menampilkan summary kependudukan kepada masyarakat'
        ];
    }

    public function defineProperties()
    {
        return [
            'periodeTahun' => [
                'title' => 'Periode Tahun',
                'description' => 'Periode tahun laporan kependudukan ditampilkan',
                'type' => 'string',
                'default' => '{{ :periodeTahun }}'
            ],
        ];
    }

    public function onRun()
    {
        $periodeTahunTerpilih = $this->property('periodeTahun');
        $this->page['urlajax'] = \Backend::url("yfktn/laporankependudukan/renderlaporan/summaryLaporanDesa/{$periodeTahunTerpilih}");
    }
}

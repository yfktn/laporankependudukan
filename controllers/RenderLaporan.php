<?php namespace Yfktn\LaporanKependudukan\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Db;
use Exception;
use October\Rain\Exception\ApplicationException;
use October\Rain\Support\Facades\Flash;
use Yfktn\LaporanKependudukan\Models\Desa;

/**
 * Pilih pada saat mau melakukan render itu, menampilkan pilihan Tahun dan juga desanya.
 * @package Yfktn\LaporanKependudukan\Controllers
 */
class RenderLaporan extends Controller
{
    public $implement = [
        // 'Backend.Behaviors.RelationController',
        'Backend.Behaviors.FormController',
        // 'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Yfktn.LaporanKependudukan', 'laporankependudukan', 'sidemenu-render');
    }

    /**
     * Tampilkan laporan kependudukan tahun terpilih pada desa yang ditentukan.
     * @return void 
     */
    public function onClickRenderLaporanPeriode()
    {
        $pilihanUser = post('LaporanKependudukanReport');
        $jenisLaporan = $pilihanUser['jenis_laporan'];
        // panggil!
        return $this->$jenisLaporan($pilihanUser);
    }

    /**
     * Tampilkan laporan berdasarkan periode untuk desa terpilih!
     * @param mixed $pilihanUser 
     * @return array 
     * @throws ApplicationException 
     * @throws Exception 
     */
    protected function laporan_desa_berdasarkan_periode($pilihanUser, $tampilDownload = false)
    {
        if(empty($pilihanUser['periode_tahun'])) {
            throw new ApplicationException("Periode Tahun Tidak Didefinisikan, mohon pilih dahulu periode tahunnya!");
        }
        if(empty($pilihanUser['desa'])) {
            throw new ApplicationException("Desa belum dipilih! Mohon dipilih dulu desanya.");
        }
        $totals = [];
        $sql = <<<SQLQUERY
select l.periode_bulan, l.periode_tahun, rtrw.* from yfktn_laporankependudukan_ l
inner join yfktn_laporankependudukan_rtrw rtrw on rtrw.laporan_kependudukan_id = l.id
where l.desa_id = ? and l.periode_tahun = ?
order by l.periode_bulan
SQLQUERY;
        $dbSelectQuery = Db::select($sql, [$pilihanUser['desa'], $pilihanUser['periode_tahun']]);
        $this->vars['dataDesa'] = $dbSelectQuery;
        $this->vars['desaId'] = $pilihanUser['desa'];
        $this->vars['periodeTahun'] = $pilihanUser['periode_tahun'];
        $this->hitungTotal($totals, $dbSelectQuery);
        $this->vars['totals'] = $totals;
        $this->vars['tampilDownload'] = $tampilDownload;
        // sekarang untuk nama desa
        $this->vars['dataMasterDesa'] = Desa::selectRaw("id, nama, slug")->find($pilihanUser['desa']);
        if($tampilDownload) {
            return $this->makePartial('render_laporan_periode');
        }
        Flash::success("Report telah dirender, silahkan check pada tab Hasil.");
        return [
            '#hasilRender' => $this->makePartial('render_laporan_periode')
        ];
    }

    public function downloadLaporanDesaBerdasarkanPeriode($desaId, $periodeTahun)
    {
        $this->layout = "report";
        return response(
            $this->laporan_desa_berdasarkan_periode([ 'desa'=>$desaId, 'periode_tahun' => $periodeTahun], true), 
            200,
            [
                'Content-Type' => 'application/vnd-ms-excel',
                'Content-Disposition' => 'attachment; filename=laporan-berdasarkan-periode.xls'
            ]
        );
    }

    /**
     * Hitung total untuk  masing-masing periode!
     * @param mixed $totals 
     * @param mixed $hasilQuery 
     * @return void 
     */
    protected function hitungTotal(&$totals, $hasilQuery)
    {
        $currPeriodeBulan = 0;
        foreach($hasilQuery as $h) {
            if($currPeriodeBulan == 0 || $currPeriodeBulan != $h->periode_bulan) {
                $totals[$h->periode_bulan] = [
                    'total_awal_kk' => 0,
                    'total_awal_perempuan' => 0,
                    'total_awal_laki' => 0,
                    'total_lahir_laki' => 0,
                    'total_lahir_perempuan' => 0,
                    'total_mati_laki' => 0,
                    'total_mati_perempuan' => 0,
                    'total_pendatang_laki' => 0,
                    'total_pendatang_perempuan' => 0,
                    'total_pindah_laki' => 0,
                    'total_pindah_perempuan' => 0,
                    'total_akhir_kk' => 0,
                    'total_akhir_perempuan' => 0,
                    'total_akhir_laki' => 0,
                ];
                $currPeriodeBulan = $h->periode_bulan;
            }
            $totals[$currPeriodeBulan]['total_awal_kk'] += $h->jumlah_awal_kk;
            $totals[$currPeriodeBulan]['total_awal_perempuan'] += $h->jumlah_awal_perempuan;
            $totals[$currPeriodeBulan]['total_awal_laki'] += $h->jumlah_awal_laki;
            $totals[$currPeriodeBulan]['total_lahir_laki'] += $h->jumlah_lahir_laki;
            $totals[$currPeriodeBulan]['total_lahir_perempuan'] += $h->jumlah_lahir_perempuan;
            $totals[$currPeriodeBulan]['total_mati_laki'] += $h->jumlah_mati_laki;
            $totals[$currPeriodeBulan]['total_mati_perempuan'] += $h->jumlah_mati_perempuan;
            $totals[$currPeriodeBulan]['total_pendatang_laki'] += $h->jumlah_pendatang_laki;
            $totals[$currPeriodeBulan]['total_pendatang_perempuan'] += $h->jumlah_pendatang_perempuan;
            $totals[$currPeriodeBulan]['total_pindah_laki'] += $h->jumlah_pindah_laki;
            $totals[$currPeriodeBulan]['total_pindah_perempuan'] += $h->jumlah_pindah_perempuan;
            $totals[$currPeriodeBulan]['total_akhir_kk'] += $h->jumlah_awal_kk;
            $totals[$currPeriodeBulan]['total_akhir_perempuan'] += (
                $h->jumlah_awal_perempuan + $h->jumlah_lahir_perempuan - $h->jumlah_mati_perempuan + $h->jumlah_pendatang_perempuan - $h->jumlah_pindah_perempuan
            );
            $totals[$currPeriodeBulan]['total_akhir_laki'] += (
                $h->jumlah_awal_laki + $h->jumlah_lahir_laki - $h->jumlah_mati_laki + $h->jumlah_pendatang_laki - $h->jumlah_pindah_laki
            );
        }
    }

    /**
     * Ini menampilkan summary laporan masing-masing Desa.
     * @param mixed $pilihanUser 
     * @return array 
     * @throws ApplicationException 
     * @throws Exception 
     */
    protected function summary_laporan_desa($pilihanUser)
    {
        if(empty($pilihanUser['periode_tahun'])) {
            throw new ApplicationException("Periode Tahun Tidak Didefinisikan, mohon pilih dahulu periode tahunnya!");
        }
        $bulan = range(1, 12);
        $parameters = [];
        $parameters[] = $pilihanUser['periode_tahun'];
        $sqlQuerySummary = <<<QRY
select l.desa_id, l.periode_bulan, l.periode_tahun, rtrw.*,
  rtrw.jumlah_awal_laki + rtrw.jumlah_lahir_laki + rtrw.jumlah_pendatang_laki - rtrw.jumlah_mati_laki - rtrw.jumlah_pindah_laki as jumlah_akhir_laki,
  rtrw.jumlah_awal_perempuan + rtrw.jumlah_lahir_perempuan  + rtrw.jumlah_pendatang_perempuan - rtrw.jumlah_mati_perempuan - rtrw.jumlah_pindah_perempuan as jumlah_akhir_perempuan
from yfktn_laporankependudukan_ l
inner join (
    select laporan_kependudukan_id, 
    sum(jumlah_awal_laki) as jumlah_awal_laki, sum(jumlah_awal_perempuan) as jumlah_awal_perempuan,
    sum(jumlah_lahir_laki) as jumlah_lahir_laki, sum(jumlah_lahir_perempuan) as jumlah_lahir_perempuan,
    sum(jumlah_mati_laki) as jumlah_mati_laki, sum(jumlah_mati_perempuan) as jumlah_mati_perempuan,
    sum(jumlah_pendatang_laki) as jumlah_pendatang_laki, sum(jumlah_pendatang_perempuan) as jumlah_pendatang_perempuan,
    sum(jumlah_pindah_laki) as jumlah_pindah_laki, sum(jumlah_pindah_perempuan) as jumlah_pindah_perempuan
    from yfktn_laporankependudukan_rtrw rtrw
    group by laporan_kependudukan_id
) rtrw on rtrw.laporan_kependudukan_id = l.id
where l.periode_tahun = ?
QRY;
        $daftarDesa = Desa::selectRaw("id, nama, slug");
        if(!empty($pilihanUser['desa'])) {
            $sqlQuerySummary .= ' and l.desa_id = ?';
            $parameters[] = $pilihanUser['desa'];
            $daftarDesa = $daftarDesa->where('id', $pilihanUser['desa']);
        }
        $querySummary = Db::select($sqlQuerySummary, $parameters);
        $querySummaryCollection = collect($querySummary);
        $daftarDesa = $daftarDesa->get();
        $dataTable = [];
        foreach($daftarDesa as $desa) {
            $indexIs = "{$desa->slug}|{$desa->nama}";
            $dataTable[$indexIs] = [];
            for($i = 1; $i <= 12; $i++) {
                $dataTable[$indexIs][$i] = $this->getSummaryOf($desa->id, $i, $querySummaryCollection);
            }
        }
        $this->vars['dataTable'] = $dataTable;
        $this->vars['periodeTahun'] = $pilihanUser['periode_tahun'];
        $this->vars['desaId'] = $pilihanUser['desa'];

        Flash::success("Report telah dirender, silahkan check pada tab Hasil.");
        return [
            '#hasilRender' => $this->makePartial('render_summary_laporan_desa')
        ];
    }

    /**
     * Ini digunakan untuk membuat summary pada masing-masing item periode desa dengan mempergunakan collection
     * laravel melakukan querynya. Sehingga pada laporan sumnmary, tidak ada lagi terlihat RTRWnya.
     * @param mixed $desaId 
     * @param mixed $periodeBulan 
     * @param mixed $hasilQuerySummaryColl 
     * @return array 
     */
    protected function getSummaryOf($desaId, $periodeBulan, $hasilQuerySummaryColl)
    {
        $hasil = $hasilQuerySummaryColl->where('desa_id', $desaId)->where('periode_bulan', $periodeBulan)->first();
        if($hasil == null) {
            return [
                'jumlah_awal_perempuan' => 0,
                'jumlah_awal_laki' => 0,
                'jumlah_lahir_laki' => 0,
                'jumlah_lahir_perempuan' => 0,
                'jumlah_mati_laki' => 0,
                'jumlah_mati_perempuan' => 0,
                'jumlah_pendatang_laki' => 0,
                'jumlah_pendatang_perempuan' => 0,
                'jumlah_pindah_laki' => 0,
                'jumlah_pindah_perempuan' => 0,
                'jumlah_akhir_kk' => 0,
                'jumlah_akhir_perempuan' => 0,
                'jumlah_akhir_laki' => 0,
            ];
        }
        return [
            'jumlah_awal_perempuan' => $hasil->jumlah_awal_perempuan,
            'jumlah_awal_laki' => $hasil->jumlah_awal_laki,
            'jumlah_lahir_laki' => $hasil->jumlah_lahir_laki,
            'jumlah_lahir_perempuan' => $hasil->jumlah_lahir_perempuan,
            'jumlah_mati_laki' => $hasil->jumlah_mati_laki,
            'jumlah_mati_perempuan' => $hasil->jumlah_mati_perempuan,
            'jumlah_pendatang_laki' => $hasil->jumlah_pendatang_laki,
            'jumlah_pendatang_perempuan' => $hasil->jumlah_pendatang_perempuan,
            'jumlah_pindah_laki' => $hasil->jumlah_pindah_laki,
            'jumlah_pindah_perempuan' => $hasil->jumlah_pindah_perempuan,
            // 'jumlah_akhir_kk' => $hasil->jumlah_akhir_kk,
            'jumlah_akhir_perempuan' => $hasil->jumlah_akhir_perempuan,
            'jumlah_akhir_laki' => $hasil->jumlah_akhir_laki,
        ];
    }
}
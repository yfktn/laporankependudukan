<?php namespace Yfktn\LaporanKependudukan\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Db;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Container\BindingResolutionException;
use October\Rain\Exception\ApplicationException;
use October\Rain\Support\Facades\Flash;
use Yfktn\LaporanKependudukan\Classes\ReportPeriodeFactory;
use Yfktn\LaporanKependudukan\Classes\ReportSummaryDesaFactory;
use Yfktn\LaporanKependudukan\Models\Desa;

/**
 * Pilih pada saat mau melakukan render itu, menampilkan pilihan Tahun dan juga desanya.
 * @package Yfktn\LaporanKependudukan\Controllers
 */
class RenderLaporan extends Controller
{
    protected $reportPeriodeFactory;
    protected $reportRingkasanFactory;
    public $implement = [
        // 'Backend.Behaviors.RelationController',
        'Backend.Behaviors.FormController',
        // 'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';

    public $publicActions = ['summaryLaporanDesa'];

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
        if($jenisLaporan == 'laporan_desa_berdasarkan_periode') {
            $this->reportPeriodeFactory = new ReportPeriodeFactory;
        } else if($jenisLaporan == 'summary_laporan_desa') {
            $this->reportRingkasanFactory = new ReportSummaryDesaFactory;
        } else {
            throw new ApplicationException('Jenis Laporan tidak dikenali!');
        }
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
            throw new ApplicationException("Jenis laporan ini membutuhkan data desa, dan desa belum dipilih! Mohon dipilih dulu desanya.");
        }
        $totals = [];
        $dbSelectQuery = $this->reportPeriodeFactory->laporanDesaPeriode($pilihanUser['desa'], $pilihanUser['periode_tahun']);
        $this->reportPeriodeFactory->hitungkanTotal($totals, $dbSelectQuery);
        $this->vars['dataDesa'] = $dbSelectQuery;
        $this->vars['desaId'] = $pilihanUser['desa'];
        $this->vars['periodeTahun'] = $pilihanUser['periode_tahun'];
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

    /**
     * Ini user memilih link untuk melakukan download!
     * @param mixed $periodeTahun 
     * @param mixed $desaId 
     * @return Response|ResponseFactory 
     * @throws ApplicationException 
     * @throws Exception 
     * @throws BindingResolutionException 
     */
    public function downloadLaporanDesaBerdasarkanPeriode($periodeTahun, $desaId = null)
    {
        $this->layout = "report";
        $this->reportPeriodeFactory = new ReportPeriodeFactory;
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
     * Ini menampilkan summary laporan masing-masing Desa.
     * @param mixed $pilihanUser 
     * @return array 
     * @throws ApplicationException 
     * @throws Exception 
     */
    protected function summary_laporan_desa($pilihanUser, $tampilDownload = false)
    {
        if(empty($pilihanUser['periode_tahun'])) {
            throw new ApplicationException("Periode Tahun Tidak Didefinisikan, mohon pilih dahulu periode tahunnya!");
        }
        $this->vars['dataTable'] = $this->reportRingkasanFactory->getDataTableSummary(
            $pilihanUser['periode_tahun'], (empty($pilihanUser['desa']) ? []: [$pilihanUser['desa']]) );
        $this->vars['periodeTahun'] = $pilihanUser['periode_tahun'];
        $this->vars['desaId'] = $pilihanUser['desa'];
        $this->vars['tampilDownload'] = $tampilDownload;

        if($tampilDownload) {
            return $this->makePartial('render_summary_laporan_desa');
        }

        Flash::success("Report telah dirender, silahkan check pada tab Hasil.");
        return [
            '#hasilRender' => $this->makePartial('render_summary_laporan_desa')
        ];
    }

    /**
     * Supaya tidak dua tiga kali bikin setting, makanya berikan ini supaya bisa di load
     * oleh yang lain, agar bisa mendapatkan report yang telah digenerate.
     * @param mixed $periodeTahun 
     * @param mixed $desaId 
     * @return Response|ResponseFactory 
     * @throws ApplicationException 
     * @throws Exception 
     * @throws BindingResolutionException 
     */
    public function summaryLaporanDesa($periodeTahun, $desaId = null)
    {
        $this->layout = "report";
        $this->reportRingkasanFactory = new ReportSummaryDesaFactory;
        return response(
            $this->summary_laporan_desa([ 'desa'=>$desaId, 'periode_tahun' => $periodeTahun], true)
        );
    }

    /**
     * User memilih untuk melakukan download terhadap laporannya!
     * @param mixed $desaId 
     * @param mixed $periodeTahun 
     * @return Response|ResponseFactory 
     * @throws ApplicationException 
     * @throws Exception 
     * @throws BindingResolutionException 
     */
    public function downloadSummaryLaporanDesa($periodeTahun, $desaId = null)
    {
        $this->layout = "report";
        $this->reportRingkasanFactory = new ReportSummaryDesaFactory;
        return response(
            $this->summary_laporan_desa([ 'desa'=>$desaId, 'periode_tahun' => $periodeTahun], true), 
            200,
            [
                'Content-Type' => 'application/vnd-ms-excel',
                'Content-Disposition' => 'attachment; filename=laporan-summary-desa.xls'
            ]
        );
    }
}
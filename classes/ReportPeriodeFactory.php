<?php namespace Yfktn\LaporanKependudukan\Classes;

use Db;

class ReportPeriodeFactory
{
    /**
     * lakukan query ke database untuk mendapatkan nilai laporan untuk desa terpilih dlan
     * periode tahun yang ingin diambil laporannya.
     * @param mixed $pilihanDesa 
     * @param mixed $pilihanPeriodeTahun 
     * @return mixed 
     */
    public function laporanDesaPeriode($pilihanDesa, $pilihanPeriodeTahun, $pilihanPeriodeBulan = null)
    {
        $parameters = [];
        $parameters[] = $pilihanDesa;
        $parameters[] = $pilihanPeriodeTahun;
        $sql = <<<SQLQUERY
select l.periode_bulan, l.periode_tahun, rtrw.* from yfktn_laporankependudukan_ l
inner join yfktn_laporankependudukan_rtrw rtrw on rtrw.laporan_kependudukan_id = l.id
where l.desa_id = ? and l.periode_tahun = ?
SQLQUERY;
        if($pilihanPeriodeBulan !== null) {
            $sql .= ' and l.periode_bulan = ?';
            $parameters[] = $pilihanPeriodeBulan;
        }
        $sql .= " order by l.periode_bulan, rtrw.nama_rtrw";
        return Db::select($sql, $parameters);
    }

    /**
     * Total adalah nilai total untuk masing-masing periode bulan
     * @param mixed $totals 
     * @param mixed $hasilQueryKeDb 
     * @return void 
     */
    public function hitungkanTotal(&$totals, $hasilQueryKeDb)
    {
        $currPeriodeBulan = 0;
        foreach($hasilQueryKeDb as $h) {
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
}
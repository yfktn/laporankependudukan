<?php namespace Yfktn\LaporanKependudukan\Classes;

use Db;
use Yfktn\LaporanKependudukan\Models\Desa;

class ReportSummaryDesaFactory
{
    public function getDataTableSummary($periodeTahun, $pilihanDesa = [])
    {
        $parameters = [];
        $parameters[] = $periodeTahun;
        $sqlQuerySummary = <<<QRY
select l.desa_id, l.periode_bulan, l.periode_tahun, rtrw.*,
  rtrw.jumlah_awal_laki + rtrw.jumlah_lahir_laki + rtrw.jumlah_pendatang_laki - rtrw.jumlah_mati_laki - rtrw.jumlah_pindah_laki as jumlah_akhir_laki,
  rtrw.jumlah_awal_perempuan + rtrw.jumlah_lahir_perempuan  + rtrw.jumlah_pendatang_perempuan - rtrw.jumlah_mati_perempuan - rtrw.jumlah_pindah_perempuan as jumlah_akhir_perempuan,
  rtrw.jumlah_awal_kk + rtrw.jumlah_pendatang_kk - rtrw.jumlah_pindah_kk as jumlah_akhir_kk
from yfktn_laporankependudukan_ l
inner join (
    select laporan_kependudukan_id, 
    sum(jumlah_awal_laki) as jumlah_awal_laki, sum(jumlah_awal_perempuan) as jumlah_awal_perempuan,
    sum(jumlah_lahir_laki) as jumlah_lahir_laki, sum(jumlah_lahir_perempuan) as jumlah_lahir_perempuan,
    sum(jumlah_mati_laki) as jumlah_mati_laki, sum(jumlah_mati_perempuan) as jumlah_mati_perempuan,
    sum(jumlah_pendatang_laki) as jumlah_pendatang_laki, sum(jumlah_pendatang_perempuan) as jumlah_pendatang_perempuan,
    sum(jumlah_pindah_laki) as jumlah_pindah_laki, sum(jumlah_pindah_perempuan) as jumlah_pindah_perempuan,
    sum(jumlah_awal_kk) as jumlah_awal_kk, sum(jumlah_pindah_kk) as jumlah_pindah_kk, 
    sum(jumlah_pendatang_kk) as jumlah_pendatang_kk
    from yfktn_laporankependudukan_rtrw rtrw
    group by laporan_kependudukan_id
) rtrw on rtrw.laporan_kependudukan_id = l.id
where l.periode_tahun = ?
QRY;
        $daftarDesa = Desa::selectRaw("id, nama, slug");
        
        if(count($pilihanDesa) > 0) {
            // ini lebih dari satu desa maka generate query supaya bisa memilih semua 
            // gunakan or 
            $pilihanDesanya = [];
            foreach($pilihanDesa as $ds) { // render untuk query di dalam nya
                $pilihanDesanya[] = 'l.desa_id = ?';
                $parameters[] = $ds; // jangan lupa nilai parameternya
            }
            $sqlQuerySummary .= ' and (' . implode(' or ', $pilihanDesanya) . ')';
            $daftarDesa = $daftarDesa->whereIn('id', $pilihanDesa);
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
        return $dataTable;
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
                'jumlah_awal_kk' => 0,
                'jumlah_pendatang_kk' => 0,
                'jumlah_pindah_kk' => 0,
                'jumlah_akhir_kk' => 0,
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
            'jumlah_awal_kk' => $hasil->jumlah_awal_kk,
            'jumlah_pendatang_kk' => $hasil->jumlah_pendatang_kk,
            'jumlah_pindah_kk' => $hasil->jumlah_pindah_kk,
            'jumlah_akhir_kk' => $hasil->jumlah_akhir_kk,
        ];
    }
}
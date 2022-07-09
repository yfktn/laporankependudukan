<?php namespace Yfktn\LaporanKependudukan\Models;

use Illuminate\Database\Eloquent\Builder;
use Model;
use October\Rain\Exception\ApplicationException;
use Yfktn\LaporanKependudukan\Classes\RomanRTRWTrait;

/**
 * Model
 */
class LaporanKependudukanDiRtRw extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use RomanRTRWTrait;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'yfktn_laporankependudukan_rtrw';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'nama_rtrw' => 'required',
        'laporan_kependudukan_id' => 'required',
        'jumlah_lahir_laki' => 'required|numeric|min:0',
        'jumlah_lahir_perempuan' => 'required|numeric|min:0',
        'jumlah_mati_laki' => 'required|numeric|min:0',
        'jumlah_mati_perempuan' => 'required|numeric|min:0',
        'jumlah_pendatang_laki' => 'required|numeric|min:0',
        'jumlah_pendatang_perempuan' => 'required|numeric|min:0',
        'jumlah_pindah_laki' => 'required|numeric|min:0',
        'jumlah_pindah_perempuan' => 'required|numeric|min:0',
    ];

    public $belongsTo = [
        'laporanKependudukan' => [
            'Yfktn\LaporanKependudukan\Models\LaporanKependudukan',
            'key' => 'laporan_kependudukan_id'
        ]
    ];

    /**
     * dapatkan jumlah penduduk akhirnya berdasarkan jumlah penduduk awal.
     * @return array 
     */
    public function dapatkanJumlahPendudukAkhir()
    {
        $L = $this->jumlah_awal_laki + $this->jumlah_lahir_laki - $this->jumlah_mati_laki - $this->jumlah_pindah_laki;
        $P = $this->jumlah_awal_perempuan + $this->jumlah_lahir_perempuan - $this->jumlah_mati_perempuan - $this->jumlah_pindah_perempuan;
        return [
            'KK' => $this->jumlah_awal_kk,
            'L'  => $L,
            'P'  => $P,
            'LP' => $L + $P 
        ];
    }

    /**
     * Lakukan perhitungan terhadap posisi awal kependudukan. Lakukan proses perhitungan bila
     * semua nilainya adalah 0 untuk pengisian posisi awal.
     */
    protected function prosesPosisiAwalKependudukan(&$error)
    {
        if(!($this->jumlah_awal_kk <= 0 
            && $this->jumlah_awal_laki <= 0 
            && $this->jumlah_awal_perempuan <= 0)
        ) {
            // perhitungan dari periode sebelumnya hanya dilakukan
            // bila semua nilai awal adalah 0, berarti ini menyimpan isian!
            return true;
        }
        // dapatkan periode laporan kependudukan saat ini
        $laporanKependudukan = $this->laporanKependudukan;
        $periodeBulan = $laporanKependudukan->periode_bulan;
        $periodeTahun = $laporanKependudukan->periode_tahun;
        $desaIdNya = $laporanKependudukan->desa_id;
        if($periodeBulan == 1) {
            // ini bulan Januari? mundur kita
            $periodeBulan = 12;
            $periodeTahun = $periodeTahun - 1;
        } else {
            $periodeBulan = $periodeBulan - 1;
        }
        $periodeSebelumnya = LaporanKependudukanDiRtRw::whereHas('laporanKependudukan', 
                function(Builder $query) use($periodeBulan, $periodeTahun, $desaIdNya) {
                    $query->where('periode_bulan', $periodeBulan)
                        ->where('periode_tahun', $periodeTahun)
                        ->where('desa_id', $desaIdNya);
                })
            ->where('nama_rtrw', $this->nama_rtrw)
            ->first();
        if($periodeSebelumnya == null) {
            $error = "Gagal mendapatkan periode sebelumnya untuk RTRW {$this->nama_rtrw}, check bila isian jumlah untuk periode sebelumnya pada RTRW {$this->nama_rtrw} telah terisi!";
            trace_log($error);
            return false;
        }
        $jumlahPendudukAkhirPeriodeSebelumya = $periodeSebelumnya->dapatkanJumlahPendudukAkhir();
        // sekarang masukkan ke nilainya
        $this->jumlah_awal_kk = $jumlahPendudukAkhirPeriodeSebelumya['KK'];
        $this->jumlah_awal_laki = $jumlahPendudukAkhirPeriodeSebelumya['L'];
        $this->jumlah_awal_perempuan = $jumlahPendudukAkhirPeriodeSebelumya['P'];
        return true;
    }

    public function beforeSave()
    {
        // check untuk nilai posisi awal kependudukan
        $error = "";
        $checkNilaiAwal = $this->prosesPosisiAwalKependudukan($error);
        if(!$checkNilaiAwal) {
            throw new ApplicationException($error);
            return false;
        }
    }
}

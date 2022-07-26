<?php namespace Yfktn\LaporanKependudukan\Models;

use Illuminate\Database\Eloquent\Builder;
use Model;
use October\Rain\Exception\ApplicationException;
use Yfktn\LaporanKependudukan\Classes\JumlahPendudukAkhirTrait;
use Yfktn\LaporanKependudukan\Classes\RomanRTRWTrait;

/**
 * Model
 */
class LaporanKependudukanDiRtRw extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use RomanRTRWTrait;
    use JumlahPendudukAkhirTrait;

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
        'jumlah_pindah_kk' => 'required|numeric|min:0',
        'jumlah_pendatang_kk' => 'required|numeric|min:0',
    ];

    public $belongsTo = [
        'laporanKependudukan' => [
            'Yfktn\LaporanKependudukan\Models\LaporanKependudukan',
            'key' => 'laporan_kependudukan_id'
        ]
    ];

    public function dapatkanRTRWPeriodeSebelumnya(
        LaporanKependudukan $laporanKependudukan,
        $namaRtRw, $namaRtRwLabel,
        &$error)
    {
        
        // dapatkan periode laporan kependudukan saat ini
        // $laporanKependudukan = $this->laporanKependudukan;
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
            ->where('nama_rtrw', $namaRtRw)
            ->first();
        if($periodeSebelumnya == null) {
            $error = "Gagal mendapatkan periode sebelumnya untuk RTRW {$namaRtRwLabel}, check bila isian jumlah untuk periode sebelumnya pada RTRW {$this->nama_rtrw} telah terisi!";
            trace_log($error);
        }
        return $periodeSebelumnya;
    }

    /**
     * Lakukan perhitungan terhadap posisi awal kependudukan. Lakukan proses perhitungan bila
     * semua nilainya adalah 0 untuk pengisian posisi awal.
     * Bila diisikan untuk pengisian awal, pastikan bahwa nilainya sinkron dengan nilai akhir
     * dari satu periode sebelumnya.
     */
    protected function prosesPosisiAwalKependudukan(&$error)
    {
        // dapatkan lebih dulu periode sebelumnya!
        $periodeSebelumnya = $this->dapatkanRTRWPeriodeSebelumnya($this->laporanKependudukan,
            $this->nama_rtrw,
            $this->nama_rtrw_label,
             $error);
        // dapatkan jumlah akhir periode sebelumnya!
        $jumlahPendudukAkhirPeriodeSebelumya = [];
        if($periodeSebelumnya != null) {
            $jumlahPendudukAkhirPeriodeSebelumya = $periodeSebelumnya->dapatkanJumlahPendudukAkhir();
        }
        // check isian awal!
        if(!($this->jumlah_awal_kk <= 0 
            && $this->jumlah_awal_laki <= 0 
            && $this->jumlah_awal_perempuan <= 0)
        ) {
            // mari lakukan pengecekan terhadap nilai yang sebelumnya untuk melakukan check, 
            // apakah nilai yang dimasukkan saat ini sudah benar? dan sesuai dengan nilai akhir
            // pada periode sebelumnya?
            if($periodeSebelumnya == null) {
                // berarti ini tidak ada inputan sebelumnya, bisa kita asumsikan merupakan nilai
                // awal, jadi anggap benar. Kita hanya mengambil mundur 1 ... 
                return true;
            }
            if(
                (int) $this->jumlah_awal_kk != (int) $jumlahPendudukAkhirPeriodeSebelumya['KK'] 
                || (int) $this->jumlah_awal_laki != (int) $jumlahPendudukAkhirPeriodeSebelumya['L']
                || (int) $this->jumlah_awal_perempuan != (int) $jumlahPendudukAkhirPeriodeSebelumya['P'] 
                ) {
                $error = "Nilai awal diisikan tidak sinkron dengan nilai akhir dari satu periode sebelumnya! Coba klik Check Laporan Periode!";
                return false;
            }
            return true;
        }
        // sekarang memasukkan nilai baru!
        if($periodeSebelumnya == null) {
            return false;
        }
        // sekarang masukkan ke nilainya
        $this->jumlah_awal_kk = $jumlahPendudukAkhirPeriodeSebelumya['KK'];
        $this->jumlah_awal_laki = $jumlahPendudukAkhirPeriodeSebelumya['L'];
        $this->jumlah_awal_perempuan = $jumlahPendudukAkhirPeriodeSebelumya['P'];
        return true;
    }

    /**
     * Pastikan nilai yang masuk sudah valid!
     * @return false|void 
     * @throws ApplicationException 
     */
    public function beforeSave()
    {
        // check untuk nilai posisi awal kependudukan
        $error = "";
        $checkNilaiAwal = $this->prosesPosisiAwalKependudukan($error);
        if(!$checkNilaiAwal) {
            throw new ApplicationException($error);
            return false;
        }
        // check bila nama_rtrw dirubah!
        if($this->isDirty('nama_rtrw')) {
            // sekarang check apakah ada nama RT RW yang double?
            $jumlahNamaRTRWyangSama = LaporanKependudukanDiRtRw::where('nama_rtrw', $this->nama_rtrw)
                ->where('laporan_kependudukan_id', $this->laporan_kependudukan_id)->count();
            if($jumlahNamaRTRWyangSama + 1 > 1) { // jumlah 1 karena plus ini yang belum disimpan!
                throw new ApplicationException("Nama RT/RW tidak boleh sama pada periode pengisian yang sama! Nama '{$this->nama_rtrw_label}' telah ada dicatatkan sebelumnya!");
            }
        }
    }
}

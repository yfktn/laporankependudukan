<?php namespace Yfktn\LaporanKependudukan\Classes;

trait JumlahPendudukAkhirTrait 
{
    /**
     * Dapatkan jumlah penduduk akhir berdasarkan isian pergerakan kependudukan.
     * Kembalian fungsi ini akan mengembalikan array assoc
     * [
     *   'KK' => jumlah KK
     *   'L'  => Jumlah Laki-Laki
     *   'P'  => Jumlah Perempuan
     *   'LP' => Jumlah Laki + Jumlah Perempuan
     * ]
     * @param mixed $ownerObjModel  object model / class pemilik nilai jumlah 
     * @return array 
     */
    public function dapatkanJumlahPendudukAkhir($ownerObjModel = null)
    {
        if($ownerObjModel == null) {
            $ownerObjModel = $this;
        }
        $L = $ownerObjModel->jumlah_awal_laki 
            + $ownerObjModel->jumlah_lahir_laki 
            + $ownerObjModel->jumlah_pendatang_laki
            - $ownerObjModel->jumlah_mati_laki 
            - $ownerObjModel->jumlah_pindah_laki;
        $P = $ownerObjModel->jumlah_awal_perempuan 
            + $ownerObjModel->jumlah_lahir_perempuan 
            + $ownerObjModel->jumlah_pendatang_perempuan
            - $ownerObjModel->jumlah_mati_perempuan 
            - $ownerObjModel->jumlah_pindah_perempuan;
        $KK = $ownerObjModel->jumlah_awal_kk 
            + $ownerObjModel->jumlah_pendatang_kk 
            - $ownerObjModel->jumlah_pindah_kk;
        return [
            'KK' => $KK,
            'L'  => $L,
            'P'  => $P,
            'LP' => $L + $P 
        ];
    }
}
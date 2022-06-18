<?php namespace Yfktn\LaporanKependudukan\Models;

use Model;
use October\Rain\Exception\ApplicationException;

/**
 * Model
 */
class LaporanKependudukan extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'yfktn_laporankependudukan_';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'desa_id' => 'required',
        'periode_bulan' => 'required|min:1|max:12',
        'periode_tahun' => 'required|min:4'
    ];

    public $belongsTo = [
        'desa' => [
            'Yfktn\LaporanKependudukan\Models\Desa',
            'key' => 'desa_id'
        ]
    ];

    public $hasMany = [
        'laporanKependudukanDiRtRw' => [
            'Yfktn\LaporanKependudukan\Models\LaporanKependudukanDiRtRw',
            'key' => 'laporan_kependudukan_id'
        ]
    ];

    public function getPeriodeBulanLabelAttribute($value = null)
    {
        $all = $this->getPeriodeBulanOptions();
        if(isset($this->attributes['periode_bulan'])) {
            return $all[$this->attributes['periode_bulan']];
        } 
        return 'TidakAdaBulan';
    }
    
    public function getPeriodeBulanOptions()
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'Nopember',
            12 => 'Desember'
        ];
    }

    public function getPeriodeTahunOptions()
    {
        $seeder = [];
        for($i=2020;$i<2025;$i++) {
            $seeder[$i] = "Tahun {$i}";
        }
        return $seeder;
    }

    public function beforeSave()
    {
        // check bila terdapat periode yang double!
        if($this->isDirty('periode_bulan') || $this->isDirty('periode_tahun')) {
            // karena ada perubahan di bulan atau tahun maka ...
            // lakukan check! pastikan tidak double!
            if(LaporanKependudukan::where('periode_bulan', $this->periode_bulan)
                ->where('periode_tahun', $this->periode_tahun)
                ->where('desa_id', $this->desa_id)
                ->exists()) {
                throw new ApplicationException("Periode Bulan dan Tahun pada Desa Terpilih telah ada dimasukkan sebelumnya! Proses tidak dapat dilanjutkan!");
                return false;
            }
        }
    }
}

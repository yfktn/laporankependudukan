<?php namespace Yfktn\LaporanKependudukan\Models;

use Backend\Facades\BackendAuth;
use Model;
use October\Rain\Database\Traits\Sluggable;

/**
 * Model
 */
class Desa extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use Sluggable;

    protected $slugs = ['slug'=>'nama'];
    
    /**
     * @var string The database table used by the model.
     */
    public $table = 'yfktn_laporankependudukan_desa';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'nama' => 'required|unique:yfktn_laporankependudukan_desa'
    ];

    public $hasMany = [
        'laporanKependudukan' => [
            'Yfktn\LaporanKependudukan\Models\LaporanKependudukan',
            'key' => 'desa_id'
        ],
        'operatorDesa' => [
            'Yfktn\LaporanKependudukan\Models\OperatorDesa',
            'key' => 'desa_id'
        ]
    ];

    public function scopeTampilkanSesuaiHakAkses($query)
    {
        $backendUser = BackendAuth::getUser();
        if(!$backendUser->hasAnyAccess(['yfktn.laporankependudukan.adminkependudukan'])) {
            return $query->whereHas('operatorDesa', function($query) use($backendUser)  {
                $query->where('user_id', $backendUser->id);
            });
        }
    }
}

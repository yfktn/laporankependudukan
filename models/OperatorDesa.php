<?php namespace Yfktn\LaporanKependudukan\Models;

use Backend\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Model;

/**
 * Model
 */
class OperatorDesa extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'yfktn_laporankependudukan_operator_desa';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'user_id' => 'required',
        'desa_id' => 'required'
    ];

    public $belongsTo = [
        'desa' => [
            'Yfktn\LaporanKependudukan\Models\Desa',
            'key' => 'desa_id'
        ],
        'user' => [
            'Backend\Models\User',
            'key' => 'user_id'
        ]
    ];

    public function getUserIdOptions()
    {
        $idDesa = request()->segment(6);
        return User::whereNotIn('id', function(QueryBuilder $query) use($idDesa) {
            $query->select('user_id')
                ->from('yfktn_laporankependudukan_operator_desa')
                ->where('desa_id', $idDesa)
                ->get();
        })
        ->selectRaw("id, concat(first_name, ' ', last_name, ' ', email) as calonop")
        ->lists('calonop', 'id');
    }
}

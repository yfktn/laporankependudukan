<?php namespace Yfktn\LaporanKependudukan\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Illuminate\Database\Eloquent\Builder;
use Yfktn\LaporanKependudukan\Models\LaporanKependudukan as ModelsLaporanKependudukan;

/**
 * Laporan Kependudukan Back-end Controller
 */
class LaporanKependudukan extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.RelationController',
        'Backend.Behaviors.ListController'
    ];

    /**
     * @var string Configuration file for the `FormController` behavior.
     */
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    /**
     * @var string Configuration file for the `ListController` behavior.
     */
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Yfktn.LaporanKependudukan', 'laporankependudukan', 'laporankependudukan');
    }

    public function listExtendQuery($query) {
        // batasi kepada user yang memiliki akses saja!\
        if(!$this->user->hasAnyAccess(['yfktn.laporankependudukan.adminkependudukan'])) {
            $query->whereHas('desa.operatorDesa', function(Builder $query) {
                $query->where('user_id', $this->user->id);
            });
        }
    }

    public function update($recordId, $context = null)
    {
        $record = ModelsLaporanKependudukan::findOrFail($recordId);
        $this->vars['desaId'] = $record->desa_id;
        $this->vars['periodeTahun'] = $record->periode_tahun;
        $this->vars['periodeBulan'] = $record->periode_bulan;
        return $this->asExtension('FormController')->update($recordId, $context);
    }
}

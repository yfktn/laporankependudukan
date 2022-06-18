<?php namespace Yfktn\LaporanKependudukan\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

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
}

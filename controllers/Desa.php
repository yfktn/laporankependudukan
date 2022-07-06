<?php namespace Yfktn\LaporanKependudukan\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Desa Back-end Controller
 */
class Desa extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        'Backend.Behaviors.RelationController',
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    /**
     * @var string Configuration file for the `FormController` behavior.
     */
    public $relationConfig = 'config_relation.yaml';
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the `ListController` behavior.
     */
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Yfktn.LaporanKependudukan', 'laporankependudukan', 'sidemenu-desa');
    }
}

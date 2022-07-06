<?php namespace Yfktn\LaporanKependudukan;

use Backend;
use System\Classes\PluginBase;

/**
 * LaporanKependudukan Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Plugin Laporan Kependudukan',
            'description' => 'Laporan Kependudukan Kecamatan Katingan Tengah',
            'author'      => 'Yfktn',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Yfktn\LaporanKependudukan\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'yfktn.laporankependudukan.admindesa' => [
                'tab' => 'Laporan Kependudukan',
                'label' => 'Administrasi Desa'
            ],
            'yfktn.laporankependudukan.adminkependudukan' => [
                'tab' => 'Laporan Kependudukan',
                'label' => 'Administrator Catatan Kependudukan'
            ],
            'yfktn.laporankependudukan.kependudukan' => [
                'tab' => 'Laporan Kependudukan',
                'label' => 'Operator Desa Catatan Kependudukan'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {        
        return [
            'laporankependudukan' => [
                'label'       => 'Laporan Kependudukan',
                'url'         => Backend::url('yfktn/laporankependudukan/laporankependudukan'),
                'icon'        => 'icon-leaf',
                'permissions' => ['yfktn.laporankependudukan.*'],
                'order'       => 500,
                'sideMenu' => [
                    'sidemenu-desa' => [
                        'label'       => 'Master Desa',
                        'icon'        => 'icon-copy',
                        'url'         => Backend::url('yfktn/laporankependudukan/desa'),
                        'permissions' => ['yfktn.laporankependudukan.admindesa'],
                        // 'counter'     => 2,
                        // 'counterLabel'=> 'Label describing a static menu counter',
                    ],
                    'sidemenu-render' => [
                        'label'       => 'Render Laporan',
                        'icon'        => 'icon-industry',
                        'url'         => Backend::url('yfktn/laporankependudukan/renderlaporan/create'),
                        'permissions' => ['yfktn.laporankependudukan.*'],
                        // 'counter'     => 2,
                        // 'counterLabel'=> 'Label describing a static menu counter',
                    ],
                    // 'categories' => [
                    //     'label'       => 'Categories',
                    //     'icon'        => 'icon-copy',
                    //     'url'         => Backend::url('acme/blog/categories'),
                    //     'permissions' => ['acme.blog.access_categories'],
                    // ]
                ]
            ],
        ];
    }
}

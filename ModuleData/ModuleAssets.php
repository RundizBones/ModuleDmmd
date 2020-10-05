<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rdb\Modules\DemoManagementDialog\ModuleData;


/**
 * Module assets data.
 * 
 * @since 0.1
 */
class ModuleAssets
{


    /**
     * @var \Rdb\System\Container
     */
    protected $Container;


    /**
     * Class constructor.
     * 
     * @param \Rdb\System\Container $Container The DI container class.
     */
    public function __construct(\Rdb\System\Container $Container)
    {
        $this->Container = $Container;
    }// __construct


    /**
     * Get module's assets list.
     * 
     * @see \Rdb\Modules\RdbAdmin\Libraries\Assets::addMultipleAssets() See <code>\Rdb\Modules\RdbAdmin\Libraries\Assets</code> class at <code>addMultipleAssets()</code> method for data structure.
     * @return array Return associative array with `css` and `js` key with its values.
     */
    public function getModuleAssets(): array
    {
        $Url = new \Rdb\System\Libraries\Url($this->Container);
        $publicModuleUrl = $Url->getPublicModuleUrl(__FILE__);
        unset($Url);

        return [
            'js' => [
                // @TODO[dmmd]: write your own assets file name, path, and handle. Also change the handle in your PHP controller.
                // demo management dialog assets.
                [
                    'handle' => 'dmmdIndex',
                    'file' => $publicModuleUrl . '/assets/js/Controllers/Admin/Demo/indexAction.js',
                    'dependency' => ['rdta', 'rdbaDatatables', 'rdbaXhrDialog', 'datatables-plugins-pagination', 'rdbaCommon', 'rdbaUiXhrCommonData'],
                ],
                [
                    'handle' => 'dmmdAdd',
                    'file' => $publicModuleUrl . '/assets/js/Controllers/Admin/Demo/addAction.js',
                    'dependency' => ['rdta', 'rdbaCommon', 'rdbaUiXhrCommonData'],
                    'attributes' => [
                        'class' => 'ajaxInjectJs'
                    ],
                ],
                [
                    'handle' => 'dmmdEdit',
                    'file' => $publicModuleUrl . '/assets/js/Controllers/Admin/Demo/editAction.js',
                    'dependency' => ['rdta', 'rdbaCommon', 'rdbaUiXhrCommonData'],
                    'attributes' => [
                        'class' => 'ajaxInjectJs'
                    ],
                ],
                // end demo management dialog assets.
                // END TODO
            ],
        ];
    }// getModuleAssets


}

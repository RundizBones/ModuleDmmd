<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rdb\Modules\DemoManagementDialog\ModuleData;


/**
 * The module admin class for set permissions, menu items.
 */
class ModuleAdmin implements \Rdb\Modules\RdbAdmin\Interfaces\ModuleAdmin
{


    /**
     * @var \Rdb\System\Container
     */
    protected $Container;


    /**
     * {@inheritDoc}
     */
    public function __construct(\Rdb\System\Container $Container)
    {
        $this->Container = $Container;
    }// __construct


    /**
     * {@inheritDoc}
     */
    public function dashboardWidgets(): array
    {
        return [];
    }// dashboardWidgets


    /**
     * {@inheritDoc}
     */
    public function definePermissions(): array
    {
        // @TODO[dmmd]: write your own permissions. The code below is just for demonstration, please remove on your real project.
        return [
            'pageDemoManagementDialog' => ['add', 'edit', 'delete', 'list'],
        ];
    }// definePermissions


    /**
     * {@inheritDoc}
     */
    public function permissionDisplayText(string $key = '', bool $translate = false)
    {
        if ($this->Container->has('Languages')) {
            $Languages = $this->Container->get('Languages');
        } else {
            $Languages = new \Rdb\Modules\RdbAdmin\Libraries\Languages($this->Container);
        }
        $Languages->bindTextDomain(
            'demomanagementdialog', 
            dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'DemoManagementDialog' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'translations'
        );

        $keywords = [];

        // pages keywords
        // @TODO[dmmd]: write your own readable permission text. The code below is just for demonstration, please remove on your real project.
        $keywords['pageDemoManagementDialog'] = noop__('Demo management dialog');
        // END TODO

        // actions keywords
        // @TODO[dmmd]: write your own readable permission text. The code below is just for demonstration, please remove on your real project.
        $keywords['add'] = noop__('Add');
        $keywords['delete'] = noop__('Delete');
        $keywords['edit'] = noop__('Edit');
        $keywords['list'] = noop__('List items');
        // END TODO

        if (!empty($key)) {
            if (array_key_exists($key, $keywords)) {
                if ($translate === false) {
                    return $keywords[$key];
                } else {
                    return d__('demomanagementdialog', $keywords[$key]);
                }
            } else {
                return $key;
            }
        } else {
            return $keywords;
        }
    }// permissionDisplayText


    /**
     * {@inheritDoc}
     */
    public function menuItems(): array
    {
        $Url = new \Rdb\System\Libraries\Url($this->Container);

        // declare language object, set text domain to make sure that this is translation for your module.
        if ($this->Container->has('Languages')) {
            $Languages = $this->Container->get('Languages');
        } else {
            $Languages = new \Rdb\Modules\RdbAdmin\Libraries\Languages($this->Container);
        }
        $Languages->bindTextDomain(
            'demomanagementdialog', 
            dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'DemoManagementDialog' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'translations'
        );
        $Languages->getHelpers();

        $urlBaseWithLang = $Url->getAppBasedPath(true);
        $urlBase = $Url->getAppBasedPath();

        return [
            // @TODO[dmmd]: write your own menu items. The code below is just for demonstration, please remove on your real project.
            5 => [
                'id' => 'demomanagementdialog-home',
                'permission' => [],
                'icon' => 'fa-solid fa-table fa-fw',
                'name' => d__('demomanagementdialog', 'Management demo'),
                'link' => $urlBaseWithLang . '/admin/dmmd',
                'linksCurrent' => [
                    $urlBaseWithLang . '/admin/dmmd/*',
                ],
            ],// 5
            // END TODO
        ];
    }// menuItems


}

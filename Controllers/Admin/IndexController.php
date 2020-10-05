<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rdb\Modules\DemoManagementDialog\Controllers\Admin;


/**
 * Listing controller.
 */
class IndexController extends \Rdb\Modules\RdbAdmin\Controllers\Admin\AdminBaseController
{


    /**
     * Use `\Rdb\Modules\RdbAdmin\Controllers\Admin\UI\Traits\CommonDataTrait` to access method that is required for common admin pages.
     */
    use \Rdb\Modules\RdbAdmin\Controllers\Admin\UI\Traits\CommonDataTrait;


    /**
     * @TODO[dmmd]: change trait name to matched yours.
     */
    use Traits\DmmdTrait;


    /**
     * Get an item.
     * 
     * @param string $id The ID matched `id` column in DB.
     * @return string
     */
    public function doGetItemAction(string $id): string
    {
        // processing part ----------------------------------------------------------------------------------------------------
        $this->checkPermission('DemoManagementDialog', 'pageDemoManagementDialog', ['list', 'edit', 'delete']);// @TODO[dmmd]: change module and permissions to your own.

        if (session_id() === '') {
            session_start();
        }

        $Csrf = new \Rdb\Modules\RdbAdmin\Libraries\Csrf();
        $Url = new \Rdb\System\Libraries\Url($this->Container);

        // bind text domain file and you can use translation with functions that work for specific domain such as `d__()`.
        $this->Languages->bindTextDomain(
            'demomanagementdialog', 
            MODULE_PATH . DIRECTORY_SEPARATOR . 'DemoManagementDialog' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'translations'
        );
        $this->Languages->getHelpers();

        $output = [];

        // @TODO[dmmd]: write your own code to get an item data. The code below is just for demonstration, please remove on your real project.
        $id = (int) $id;
        $DmmdDb = new \Rdb\Modules\DemoManagementDialog\Models\DmmdDb($this->Container);
        $result = $DmmdDb->get($id);
        unset($DmmdDb);

        if (is_object($result) && !empty($result)) {
            $output['result'] = $result;
        } else {
            $output['result'] = null;
            $output['formResultStatus'] = 'error';
            $output['formResultMessage'] = d__('demomanagementdialog', 'Not found selected item.');
            http_response_code(404);
        }
        // END TODO

        // display, response part ---------------------------------------------------------------------------------------------
        return $this->responseAcceptType($output);
    }// doGetItemAction


    /**
     * Get multiple items.
     * 
     * This method was called from `indexAction()` method.
     * 
     * @param array $configDb
     * @return array
     */
    protected function doGetItems(array $configDb): array
    {
        $columns = $this->Input->get('columns', [], FILTER_UNSAFE_RAW, FILTER_REQUIRE_ARRAY);
        $order = $this->Input->get('order', [], FILTER_UNSAFE_RAW, FILTER_REQUIRE_ARRAY);
        $DataTablesJs = new \Rdb\Modules\RdbAdmin\Libraries\DataTablesJs();
        $sortOrders = $DataTablesJs->buildSortOrdersFromInput($columns, $order);
        unset($columns, $DataTablesJs, $order);

        $output = [];

        // @TODO[dmmd]: write your own db get items. The code below is just for demonstration, please remove on your real project.
        $DmmdDb = new \Rdb\Modules\DemoManagementDialog\Models\DmmdDb($this->Container);
        $options = [];
        $options['sortOrders'] = $sortOrders;
        $options['offset'] = $this->Input->get('start', 0, FILTER_SANITIZE_NUMBER_INT);
        $options['limit'] = $this->Input->get('length', $configDb['rdbadmin_AdminItemsPerPage'], FILTER_SANITIZE_NUMBER_INT);
        if (isset($_GET['search']['value']) && !empty(trim($_GET['search']['value']))) {
            $options['search'] = trim($_GET['search']['value']);
        }
        try {
            $result = $DmmdDb->listItems($options);
        } catch (\Exception $e) {
            $result = [];
        }
        unset($DmmdDb, $options, $sortOrders);
        // END TODO

        $output['draw'] = $this->Input->get('draw', 1, FILTER_SANITIZE_NUMBER_INT);
        $output['recordsTotal'] = ($result['total'] ?? 0);
        $output['recordsFiltered'] = $output['recordsTotal'];
        $output['listItems'] = ($result['items'] ?? []);

        return $output;
    }// doGetItems


    /**
     * Listing page action.
     * 
     * @return string
     */
    public function indexAction(): string
    {
        // processing part ----------------------------------------------------------------------------------------------------
        $this->checkPermission('DemoManagementDialog', 'pageDemoManagementDialog', ['list', 'add', 'edit', 'delete']);// @TODO[dmmd]: change module and permissions to your own.

        if (session_id() === '') {
            session_start();
        }

        $Csrf = new \Rdb\Modules\RdbAdmin\Libraries\Csrf();
        $Url = new \Rdb\System\Libraries\Url($this->Container);

        // bind text domain file and you can use translation with functions that work for specific domain such as `d__()`.
        $this->Languages->bindTextDomain(
            'demomanagementdialog', 
            MODULE_PATH . DIRECTORY_SEPARATOR . 'DemoManagementDialog' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'translations'
        );
        $this->Languages->getHelpers();

        $output = [];
        $output['configDb'] = $this->getConfigDb();
        $output['pageTitle'] = d__('demomanagementdialog', 'Demo management dialog');// @TODO[dmmd]: change to your page title.
        $output['pageHtmlTitle'] = $this->getPageHtmlTitle($output['pageTitle'], $output['configDb']['rdbadmin_SiteName']);
        $output['pageHtmlClasses'] = $this->getPageHtmlClasses();

        if ($this->Input->isNonHtmlAccept() || $this->Input->isXhr()) {
            // if custom HTTP accept, response content or AJAX.
            // get data via REST API.
            $output = array_merge($output, $this->doGetItems($output['configDb']));

            if (isset($_SESSION['formResult'])) {
                $formResult = json_decode($_SESSION['formResult'], true);
                if (is_array($formResult)) {
                    $output['formResultStatus'] = strip_tags(key($formResult));
                    $output['formResultMessage'] = current($formResult);
                }
                unset($formResult, $_SESSION['formResult']);
            }
        } else {
            $output = array_merge($output, $Csrf->createToken());
        }

        unset($Csrf);

        $output = array_merge($output, $this->getDmmdUrlsMethod());// @TODO[dmmd]: change method name to matched in your trait.

        // display, response part ---------------------------------------------------------------------------------------------
        if ($this->Input->isNonHtmlAccept() || $this->Input->isXhr()) {
            // if custom HTTP accept, response content or AJAX.
            $this->responseNoCache();
            return $this->responseAcceptType($output);
        } else {
            // if not custom HTTP accept.
            // get RdbAdmin module's assets data for render page correctly.
            $rdbAdminAssets = $this->getRdbAdminAssets();
            // get module's assets
            $ModuleAssets = new \Rdb\Modules\DemoManagementDialog\ModuleData\ModuleAssets($this->Container);
            $moduleAssetsData = $ModuleAssets->getModuleAssets();
            unset($ModuleAssets);
            // Assets class for add CSS and JS.
            $Assets = new \Rdb\Modules\RdbAdmin\Libraries\Assets($this->Container);

            // add CSS and JS assets to make basic functional and style on admin page works correctly.
            $this->setCssAssets($Assets, $rdbAdminAssets);
            $this->setJsAssetsAndObject($Assets, $rdbAdminAssets);

            // @TODO[dmmd]: change the call to asset handle below to matched your handles.
            $Assets->addMultipleAssets('css', ['datatables', 'rdbaCommonListDataPage'], $Assets->mergeAssetsData('css', $moduleAssetsData, $rdbAdminAssets));
            $Assets->addMultipleAssets('js', ['dmmdIndex'], $Assets->mergeAssetsData('js', $moduleAssetsData, $rdbAdminAssets));
            $Assets->addJsObject(
                'dmmdIndex',
                'DmmdIndexObject',// @TODO[dmmd]: rename js object here and in everywhere else such as other controllers, views, JS files.
                array_merge([
                    'isInDataTablesPage' => true,
                    'csrfName' => $output['csrfName'],
                    'csrfValue' => $output['csrfValue'],
                    'csrfKeyPair' => $output['csrfKeyPair'],
                    'txtConfirmDelete' => __('Are you sure to delete?'),
                    'txtPleaseSelectAction' => __('Please select an action.'),
                    'txtPleaseSelectAtLeastOne' => d__('demomanagementdialog', 'Please select at least one item.'),
                ], $this->getDmmdUrlsMethod())// @TODO[dmmd]: change method name to matched in your trait.
            );
            // END TODO

            $output['Assets'] = $Assets;
            $output['Modules'] = $this->Modules;
            $output['Url'] = $Url;
            $output['Views'] = $this->Views;
            $output['pageContent'] = $this->Views->render('Admin/Demo/index_v', $output);

            unset($Assets, $rdbAdminAssets, $Url);

            return $this->Views->render('common/Admin/mainLayout_v', $output, ['viewsModule' => 'RdbAdmin']);
        }
    }// indexAction


}

<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rdb\Modules\DemoManagementDialog\Controllers\Admin;


/**
 * Edit controller.
 */
class EditController extends \Rdb\Modules\RdbAdmin\Controllers\Admin\AdminBaseController
{


    /**
     * Use `\Rdb\Modules\RdbAdmin\Controllers\Admin\UI\Traits\CommonDataTrait` to access method that is required for common admin pages.
     */
    use \Rdb\Modules\RdbAdmin\Controllers\Admin\UI\Traits\CommonDataTrait;


    use Traits\DmmdTrait;


    public function doUpdateAction(string $id): string
    {
        // processing part ----------------------------------------------------------------------------------------------------
        $this->checkPermission('DemoManagementDialog', 'pageDemoManagementDialog', ['edit']);// @TODO[dmmd]: change permissions to your own.

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
        list($csrfName, $csrfValue) = $Csrf->getTokenNameValueKey(true);

        $output = array_merge($output, $this->getDmmdUrlsMethod());

        // make patch data into $_PATCH variable.
        $this->Input->patch('');
        global $_PATCH;

        if (
            isset($_PATCH[$csrfName]) &&
            isset($_PATCH[$csrfValue]) &&
            $Csrf->validateToken($_PATCH[$csrfName], $_PATCH[$csrfValue])
        ) {
            // if validate csrf passed.
            $id = (int) $id;// @TODO[dmmd]: write your own code. This code is just for demonstration, please remove on your real project.
            unset($_PATCH[$csrfName], $_PATCH[$csrfValue]);

            // prepare data for checking.
            $data = [];
            $data['title'] = trim(strip_tags($this->Input->patch('title', '')));
            if (empty($data['title'])) {
                $data['title'] = null;
            }

            // validate the form. -------------------------------------------------------------------------
            $formValidated = false;
            if (empty($data['title'])) {
                $output['formResultStatus'] = 'error';
                $output['formResultMessage'][] = d__('demomanagementdialog', 'Please enter the title.');
                http_response_code(400);
                $formValidated = false;
            } else {
                $formValidated = true;
            }
            // end validate the form. --------------------------------------------------------------------

            if (isset($formValidated) && $formValidated === true) {
                // @TODO[dmmd]: write your own code to update data here.
                $DmmdDb = new \Rdb\Modules\DemoManagementDialog\Models\DmmdDb($this->Container);
                try {
                    $saveResult = $DmmdDb->update($data, ['id' => $id]);
                } catch (\Exception $ex) {
                    $output['errorMessage'] = $ex->getMessage();
                    $saveResult = false;
                }
                unset($DmmdDb);

                if ($saveResult === true) {
                    $output['formResultStatus'] = 'success';
                    $output['formResultMessage'] = d__('demomanagementdialog', 'Updated successfully.');
                    http_response_code(200);

                    $_SESSION['formResult'] = json_encode([($output['formResultStatus'] ?? 'success') => $output['formResultMessage']]);
                    unset($output['formResultMessage'], $output['formResultStatus']);
                    $output['redirectBack'] = $output['getItemsUrl'];
                } else {
                    $output['formResultStatus'] = 'error';
                    $output['formResultMessage'] = d__('demomanagementdialog', 'Unable to update.');
                    if (isset($output['errorMessage'])) {
                        $output['formResultMessage'] .= '<br>' . $output['errorMessage'];
                    }
                    http_response_code(500);
                }
                unset($saveResult);
                // END TODO
            }

            unset($data, $formValidated);
        } else {
            // if unable to validate token.
            $output['formResultStatus'] = 'error';
            $output['formResultMessage'] = __('Unable to validate token, please try again. If this problem still occur please reload the page and try again.');
            http_response_code(400);
        }

        unset($csrfName, $csrfValue);
        // generate new token for re-submit the form continueously without reload the page.
        $output = array_merge($output, $Csrf->createToken());

        // display, response part ---------------------------------------------------------------------------------------------
        unset($Csrf, $Url);
        return $this->responseAcceptType($output);
    }// doUpdateAction


    /**
     * Edit page action.
     * 
     * @param string $id The ID matched `id` column in DB.
     * @return string
     */
    public function indexAction(string $id): string
    {
        // processing part ----------------------------------------------------------------------------------------------------
        $this->checkPermission('DemoManagementDialog', 'pageDemoManagementDialog', ['edit']);// @TODO[dmmd]: change permissions to your own.

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
        $output['pageTitle'] = d__('demomanagementdialog', 'Edit an item');// @TODO[dmmd]: change to your page title.
        $output['pageHtmlTitle'] = $this->getPageHtmlTitle($output['pageTitle'], $output['configDb']['rdbadmin_SiteName']);
        $output['pageHtmlClasses'] = $this->getPageHtmlClasses();

        $output['id'] = (int) $id;// @TODO[dmmd]: write your own code. This is for demonstration only.
        $output = array_merge($output, $this->getDmmdUrlsMethod());
        $output = array_merge($output, $Csrf->createToken());
        unset($Csrf);

        // @TODO[dmmd]: create your own breadcrumb.
        $urlBaseWithLang = $Url->getAppBasedPath(true);
        $output['breadcrumb'] = [
            [
                'item' => __('Admin home'),
                'link' => $urlBaseWithLang . '/admin',
            ],
            [
                'item' => d__('demomanagementdialog', 'List items'),
                'link' => $urlBaseWithLang . '/admin/dmmd',
            ],
            [
                'item' => $output['pageTitle'],
                'link' => $output['addItemUrl'],
            ],
        ];
        unset($urlBaseWithLang);
        // END TODO

        // display, response part ---------------------------------------------------------------------------------------------
        if ($this->Input->isNonHtmlAccept()) {
            // if custom HTTP accept, response content.
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

            $Assets->addMultipleAssets('css', ['datatables', 'rdbaCommonListDataPage'], $Assets->mergeAssetsData('css', $moduleAssetsData, $rdbAdminAssets));
            $Assets->addMultipleAssets('js', ['dmmdEdit', 'rdbaHistoryState'], $Assets->mergeAssetsData('js', $moduleAssetsData, $rdbAdminAssets));
            $Assets->addJsObject(
                'dmmdEdit',
                'DmmdIndexObject',
                array_merge([
                    'csrfName' => $output['csrfName'],
                    'csrfValue' => $output['csrfValue'],
                    'csrfKeyPair' => $output['csrfKeyPair'],
                ], $this->getDmmdUrlsMethod())
            );

            // include html functions file to use `renderBreadcrumbHtml()` function.
            include_once MODULE_PATH . '/RdbAdmin/Helpers/HTMLFunctions.php';

            $output['Assets'] = $Assets;
            $output['Modules'] = $this->Modules;
            $output['Url'] = $Url;
            $output['Views'] = $this->Views;
            $output['pageContent'] = $this->Views->render('Admin/Demo/edit_v', $output);
            $output['pageBreadcrumb'] = renderBreadcrumbHtml($output['breadcrumb']);

            unset($Assets, $rdbAdminAssets, $Url);

            return $this->Views->render('common/Admin/mainLayout_v', $output, ['viewsModule' => 'RdbAdmin']);
        }
    }// indexAction


}

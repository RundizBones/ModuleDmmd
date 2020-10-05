<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rdb\Modules\DemoManagementDialog\Controllers\Admin;


/**
 * Bulk actions controller.
 */
class ActionsController extends \Rdb\Modules\RdbAdmin\Controllers\Admin\AdminBaseController
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
     * delete action.
     * 
     * @return string
     */
    public function doDeleteAction(): string
    {
        // processing part ----------------------------------------------------------------------------------------------------
        $this->checkPermission('DemoManagementDialog', 'pageDemoManagementDialog', ['delete']);// @TODO[dmmd]: change module and permissions to your own.

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

        $output = array_merge($output, $this->getDmmdUrlsMethod());// @TODO[dmmd]: change method name to matched in your trait.

        // make delete data into $_DELETE variable.
        $this->Input->delete('');
        global $_DELETE;

        if (!isset($_DELETE['bulk-actions'])) {
            // if no action
            // don't waste time on this.
            return '';
        }

        if (
            isset($_DELETE[$csrfName]) &&
            isset($_DELETE[$csrfValue]) &&
            $Csrf->validateToken($_DELETE[$csrfName], $_DELETE[$csrfValue])
        ) {
            // if validated token to prevent CSRF.
            unset($_DELETE[$csrfName], $_DELETE[$csrfValue]);

            // @TODO[dmmd]: write your own delete data code.
            $idArray = $this->Input->delete('id', []);
            if (is_array($idArray)) {
                $DmmdDb = new \Rdb\Modules\DemoManagementDialog\Models\DmmdDb($this->Container);
                $count = 0;
                foreach ($idArray as $id) {
                    $deleteResult = $DmmdDb->delete($id);
                    if ($deleteResult === true) {
                        $count++;
                    }
                }// endforeach;
                unset($deleteResult, $DmmdDb, $id);
            }
            unset($idArray);

            if (isset($count) && $count > 0) {
                $output['formResultStatus'] = 'success';
                $output['formResultMessage'] = d__('demomanagementdialog', 'Deleted successfully.');
                $output['totalDeleted'] = $count;
                http_response_code(200);
            } else {
                $output['formResultStatus'] = 'error';
                $output['formResultMessage'] = d__('demomanagementdialog', 'Unable to delete.');
                $output['totalDeleted'] = $count;
                http_response_code(500);
            }
            // END TODO
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

    }// doDeleteAction


}

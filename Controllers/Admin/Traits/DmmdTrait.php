<?php
/**
 * Demo management dialog trait.
 * 
 * @TODO[dmmd]: change the trait file name and the trait name (including name space) to be yours.
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rdb\Modules\DemoManagementDialog\Controllers\Admin\Traits;


/**
 * Demo management dialog trait.
 */
trait DmmdTrait
{


    /**
     * Get URLs and methods for management controller.
     * 
     * @TODO[dmmd]: change this method name and also change in the controllers who called it.
     * @TODO[dmmd]: change URLs, methods to match your routes.
     * @return array
     */
    protected function getDmmdUrlsMethod(): array
    {
        $Url = new \Rdb\System\Libraries\Url($this->Container);
        $urlAppBased = $Url->getAppBasedPath(true);

        $output = [];

        $output['addItemUrl'] = $urlAppBased . '/admin/dmmd/add';
        $output['addItemRESTUrl'] = $urlAppBased . '/admin/dmmd';
        $output['addItemRESTMethod'] = 'POST';

        $output['editItemUrlBase'] = $urlAppBased . '/admin/dmmd/edit';
        $output['editItemRESTUrlBase'] = $urlAppBased . '/admin/dmmd';
        $output['editItemRESTMethod'] = 'PATCH';

        $output['deleteItemRESTUrlBase'] = $urlAppBased . '/admin/dmmd';
        $output['deleteItemRESTMethod'] = 'DELETE';

        $output['getItemsUrl'] = $urlAppBased . '/admin/dmmd';
        $output['getItemsRESTUrl'] = $urlAppBased . '/admin/dmmd';
        $output['getItemsRESTMethod'] = 'GET';

        $output['getItemRESTUrlBase'] = $urlAppBased . '/admin/dmmd';
        $output['getItemRESTMethod'] = 'GET';

        unset($Url, $urlAppBased);

        return $output;
    }// getDmmdUrlsMethod


}

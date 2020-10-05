<?php
/* @var $Rc \FastRoute\RouteCollector */
/* @var $this \Rdb\System\Router */


$Rc->addGroup('/admin', function(\FastRoute\RouteCollector $Rc) {
    // @TODO[dmmd]: write your own routes. The code below is just for demonstration, please remove on your real project.
    // demo management dialog pages. -----------------------------------------------------------------
    // /admin/dmmd page + REST API (listing page - get data via REST).
    $Rc->addRoute('GET', '/dmmd', '\\Rdb\\Modules\\DemoManagementDialog\\Controllers\\Admin\\Index:index');
    // /admin/dmmd/xx REST API (get a single item data).
    $Rc->addRoute('GET', '/dmmd/{id:\d+}', '\\Rdb\\Modules\\DemoManagementDialog\\Controllers\\Admin\\Index:doGetItem');

    // /admin/dmmd/add page.
    $Rc->addRoute('GET', '/dmmd/add', '\\Rdb\\Modules\\DemoManagementDialog\\Controllers\\Admin\\Add:index');
    // /admin/dmmd REST API (add an item).
    $Rc->addRoute('POST', '/dmmd', '\\Rdb\\Modules\\DemoManagementDialog\\Controllers\\Admin\\Add:doAdd');

    // /admin/dmmd/edit[/xx] page.
    $Rc->addRoute('GET', '/dmmd/edit[/{id:\d+}]', '\\Rdb\\Modules\\DemoManagementDialog\\Controllers\\Admin\\Edit:index');
    // /admin/dmmd/xx REST API (update an item).
    $Rc->addRoute('PATCH', '/dmmd/{id:\d+}', '\\Rdb\\Modules\\DemoManagementDialog\\Controllers\\Admin\\Edit:doUpdate');

    // /admin/dmmd/xx REST API (delete items - use comma for multiple items).
    $Rc->addRoute('DELETE', '/dmmd/{id:[0-9,]+}', '\\Rdb\\Modules\\DemoManagementDialog\\Controllers\\Admin\\Actions:doDelete');
    // end demo management dialog pages. ------------------------------------------------------------
    // END TODO
});
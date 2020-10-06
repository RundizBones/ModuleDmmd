Demonstration management dialog module for [RundizBones] framework.

This module will showing how management page (with add, edit, delete) work with dialog and AJAX.

This module required [RdbAdmin] module.

### Installation
* Download and extract files into **/Modules/DemoManagementDialog** folder.
* Run this command from framework folder. `php rdb system:module install --mname="DemoManagementDialog"`.

### Implementation
To use these functional (datatable management dialog and AJAX) as your module, please follow instruction step by step.

* Copy parts of routes in **config/routes.php** start from `@TODO[dmmd]` to `END TODO` and paste into your routes file.
    * Rewrite to be your routes.
    * Delete todo comments in routes file.
* Copy the files in **Controllers/Admin**, **Views/Admin/Demo**, **assets/js/Controllers/Admin/Demo** into your module's location.<br>
Example: **Controllers/Admin/Items**, **Views/Admin/Items**, **assets/js/Controllers/Admin/Items**.
    * Rename the namespace (case sensitive) from `Rdb\Modules\DemoManagementDialog\Controllers\Admin` to `Rdb\Modules\<YOUR MODULE FOLDER NAME>\Controllers\Admin[\ANY FOLDER (IF USE IN SUBFOLDERS.)]`.<br>
    Example: `Rdb\Modules\MyModule\Controllers\Admin\Items`.
    * Rename **Controllers/Admin/Traits/DmmdTrait.php** file name and its name to be yours.<br>
    Example: **Controllers/Admin/Items/Traits/MyItemsTrait.php** the trait namespace will be `Rdb\Modules\MyModule\Controllers\Admin\Items\Traits` and the trait name will be `MyItemsTrait`.
* Write your permissions (and maybe menu items) in **ModuleData/ModuleAdmin.php**. If you don't have yours, please see the example from this module.
* Copy parts of asset handles from **ModuleData/ModuleAssets.php** start from `@TODO[dmmd]` to `END TODO`. If you don't have yours, please see the example from this module.
    * Make sure that the URL to your assets (in the `file` array key) are correct.
* Rename these names.
    * `DemoManagementDialog` (case sensitive, whole word) - The module folder name.<br>
    This name is this module folder name, rename to match your module folder name with case sensitive and must be **StudlyCaps** to follow PSR [1][psr1], [4][psr4].
    * `'demomanagementdialog'` (case sensitive, include single quote) - The translation domain.<br>
    Rename this domain to be only for your module. Make sure that it is unique from other modules. Don't forget to wrap with single quote.
    * `getDmmdUrlsMethod` (case sensitive, whole word) - The method name in trait file.
    * `dmmdIndex` (case sensitive, whole word) - The asset handle (JS) for index controller.
    * `dmmdAdd` (case sensitive, whole word) - The asset handle (JS) for add controller.
    * `dmmdEdit` (case sensitive, whole word) - The asset handle (JS) for edit controller.
    * `DmmdIndexObject` (case sensitive, whole word) - The JS object for index JS. This will be common use with add, edit JS.
    * `demomanagementdialog-add-form` (case sensitive, whole word) - The form ID in **add_v.php** file.
    * `demomanagementdialog-edit-form` (case sensitive, whole word) - The form ID in **edit_v.php** file.
    * `demomanagementdialog-list-form` (case sensitive, whole word) - The form ID in **index_v.php** file.
    * `dmmdListItemsTable` (case sensitive, whole word) - The table ID and class.
    * `demomanagementdialog-editing-dialog-label` (case sensitive, whole word) - The dialog label ID.<br>
    This name MUST be rename before the dialog ID.
    * `demomanagementdialog-editing-dialog` (case sensitive, whole word) - The dialog ID.
    * `demomanagementdialog-list-actions` (case sensitive, whole word) - The select box ID and class of bulk actions.
    * `DmmdIndexController` (case sensitive, whole word) - The JS class name for index JS controller.
    * `DmmdAddController` (case sensitive, whole word) - The JS class name for add JS controller.
    * `DmmdEditController` (case sensitive, whole word) - The JS class name for edit JS controller.
    * `demomanagementdialog.editing.init` (case sensitive, whole word) - The editing dialog event name. This event will be fire when clicked on add or edit link and dialog opened.
* Search for `@TODO[dmmd]` for the rest and follow the instruction from each task, when finished you can remove these `@TODO` comments.

[RundizBones]:https://github.com/RundizBones/framework
[RdbAdmin]:https://github.com/RundizBones/ModuleAdmin
[psr1]:https://www.php-fig.org/psr/psr-1
[psr4]:https://www.php-fig.org/psr/psr-4
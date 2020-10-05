<?php
/* @var $Assets \Rdb\Modules\RdbAdmin\Libraries\Assets */
/* @var $Modules \Rdb\System\Modules */
/* @var $Views \Rdb\System\Views */
/* @var $Url \Rdb\System\Libraries\Url */
?>
                        <h1 class="rdba-page-content-header">
                            <?php echo $pageTitle; ?> 
                            <a class="rd-button rdba-listpage-addnew" href="<?php echo $addItemUrl; ?>">
                                <i class="fas fa-plus-circle"></i> <?php echo __('Add'); ?>
                            </a>
                        </h1>

                        <form id="demomanagementdialog-list-form" class="rdba-datatables-form"><!-- //@TODO[dmmd]: change the id value and also change in JS file. -->
                            <div class="form-result-placeholder"></div>
                            <table id="dmmdListItemsTable" class="dmmdListItemsTable rdba-datatables-js responsive hover" width="100%"><!-- //@TODO[dmmd]: change the id, class values and also change in JS file. -->
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="column-checkbox"><input type="checkbox" onclick="RdbaCommon.dataTableCheckboxToggler(jQuery('.dmmdListItemsTable'), jQuery(this));"></th><!-- //@TODO[dmmd]: change the call to class name to matched table class above. -->
                                        <th class="rd-hidden"><?php echo __('ID'); ?></th>
                                        <th class="column-primary" data-priority="1"><?php echo d__('demomanagementdialog', 'Title'); ?></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th class="column-checkbox"><input type="checkbox" onclick="RdbaCommon.dataTableCheckboxToggler(jQuery('.dmmdListItemsTable'), jQuery(this));"></th><!-- //@TODO[dmmd]: change the call to class name to matched table class above. -->
                                        <th class="rd-hidden"><?php echo __('ID'); ?></th>
                                        <th class="column-primary" data-priority="1"><?php echo d__('demomanagementdialog', 'Title'); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </form>

                        <div id="demomanagementdialog-editing-dialog" class="rd-dialog-modal" data-click-outside-not-close="true"><!-- //@TODO[dmmd]: change the id value and also change in JS file. -->
                            <div class="rd-dialog rd-dialog-size-large" data-esc-key-not-close="true" aria-labelledby="demomanagementdialog-editing-dialog-label"><!-- //@TODO[dmmd]: change the aria-labelledby value and also change its id value. -->
                                <div class="rd-dialog-header">
                                    <h4 id="demomanagementdialog-editing-dialog-label" class="rd-dialog-title"></h4><!-- //@TODO[dmmd]: change the id value to matched aria-labelledby value. -->
                                    <button class="rd-dialog-close" type="button" aria-label="Close" data-dismiss="dialog">
                                        <i class="fas fa-times" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div class="rd-dialog-body">
                                </div>
                            </div>
                        </div><!--.rd-dialog-modal-->


                        <template id="rdba-datatables-row-actions">
                            <div class="row-actions">
                                <span class="action"><?php echo __('ID'); ?> {{id}}</span>
                                <span class="action"><a class="rdba-listpage-edit" href="{{DmmdIndexObject.editItemUrlBase}}/{{id}}"><?php echo __('Edit'); ?></a></span> <!-- //@TODO[dmmd]: change the js object to matched in your controller. -->
                            </div>
                        </template>

                        <template id="rdba-datatables-result-controls">
                            <div class="col-xs-12 col-sm-6">
                                <label>
                                    <?php echo __('Search'); ?>
                                    <input id="rdba-filter-search" class="rdba-datatables-input-search" type="search" name="search" aria-control="dmmdListItemsTable"><!-- //@TODO[dmmd]: rename aria-control to matched table id value. -->
                                </label>
                                <div class="rd-button-group">
                                    <button id="rdba-datatables-filter-button" class="rdba-datatables-filter-button rd-button" type="button"><?php echo __('Filter'); ?></button>
                                    <button class="rd-button dropdown-toggler" type="button" data-placement="bottom right">
                                        <i class="fas fa-caret-down"></i>
                                        <span class="sr-only"><?php echo __('More'); ?></span>
                                    </button>
                                    <ul class="rd-dropdown">
                                        <li><a href="#reset" onclick="return DmmdIndexController.resetDataTable();"><?php echo __('Reset'); ?></a></li><!-- //@TODO[dmmd]: rename class to matched in JS class. -->
                                    </ul>
                                </div>
                            </div>
                        </template>
                        <template id="rdba-datatables-result-controls-pagination">
                            <span class="rdba-datatables-result-controls-info">
                            {{#ifGE recordsFiltered 2}}
                                {{recordsFiltered}} <?php echo __('items'); ?>
                            {{else}}
                                {{recordsFiltered}} <?php echo __('item'); ?>
                            {{/ifGE}}
                            </span>
                        </template>
                        <template id="rdba-datatables-actions-controls">
                            <div class="col-xs-12 col-sm-6">
                                <label>
                                    <select id="demomanagementdialog-list-actions" class="demomanagementdialog-list-actions rdba-actions-selectbox" name="bulk-actions"><!-- //@TODO[dmmd]: rename id, class value and also change in JS file. maybe rename the `name` value and also rename in the controller. -->
                                        <option value=""><?php echo __('Action'); ?></option>
                                        <option value="delete"><?php echo __('Delete'); ?></option>
                                    </select>
                                </label>
                                <button id="demomanagementdialog-list-actions-button" class="rd-button" type="submit"><?php echo __('Apply'); ?></button>
                                <span class="action-status-placeholder"></span>
                            </div>
                        </template>
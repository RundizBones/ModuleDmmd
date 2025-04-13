/**
 * Demo management dialog JS for its controller.
 */



class DmmdIndexController extends RdbaDatatables {


    /**
     * Class constructor.
     * 
     * @param {object} options
     * @returns {RdbaRolesController}
     */
    constructor(options) {
        super(options);

        this.formIDSelector = '#demomanagementdialog-list-form';
        this.datatableIDSelector = '#dmmdListItemsTable';
        this.defaultSortOrder = [[2, 'desc']];
    }// constructor


    /**
     * Activate data table.
     * 
     * @returns {undefined}
     */
    activateDataTable() {
        let $ = jQuery.noConflict();
        let thisClass = this;
        let addedCustomResultControls = false;

        $.when(uiXhrCommonData)// uiXhrCommonData is variable from /assets/js/Controllers/Admin/UI/XhrCommonDataController/indexAction.js file
        .done(function() {
            let dataTableOptions = {
                'ajax': {
                    'url': DmmdIndexObject.getItemsRESTUrl,
                    'method': DmmdIndexObject.getItemsRESTMethod,
                    'dataSrc': 'listItems'// change array key of data source. see https://datatables.net/examples/ajax/custom_data_property.html
                },
                // @TODO[dmmd]: write your own code for `columnDefs`.
                'columnDefs': [
                    {
                        'orderable': false,// make checkbox column not sortable.
                        'searchable': false,// make checkbox column can't search.
                        'targets': [0, 1]
                    },
                    {
                        'className': 'control',
                        'data': 'id',
                        'targets': 0,
                        'render': function () {
                            // make first column render nothing (for responsive expand/collapse button only).
                            // this is for working with responsive expand/collapse column and AJAX.
                            return '';
                        }
                    },
                    {
                        'className': 'column-checkbox',
                        'data': 'id',
                        'targets': 1,
                        'render': function(data, type, row, meta) {
                            return '<input type="checkbox" name="id[]" value="' + row.id + '">';
                        }
                    },
                    {
                        'data': 'id',
                        'targets': 2,
                        'visible': false
                    },
                    {
                        'data': 'title',
                        'targets': 3,
                        'render': function(data, type, row, meta) {
                            let source = document.getElementById('rdba-datatables-row-actions').innerHTML;
                            let template = Handlebars.compile(source);
                            row.DmmdIndexObject = DmmdIndexObject;
                            let html = data + template(row);
                            return html;
                        }
                    }
                ],
                // END TODO
                'order': thisClass.defaultSortOrder,
                'paging': true,
                'serverSide': true,
            };
            dataTableOptions = thisClass.applyToDefaultDataTableOptions(dataTableOptions);
            let dataTable = new DataTable(thisClass.datatableIDSelector, dataTableOptions);

            // datatables events
            dataTable.on('xhr.dt', function(e, settings, json, xhr) {
                if (addedCustomResultControls === false) {
                    // if it was not added custom result controls yet.
                    // set additional data.
                    json.DmmdIndexObject = DmmdIndexObject;
                    // add search controls.
                    thisClass.addCustomResultControls(json);
                    // add bulk actions controls.
                    thisClass.addActionsControls(json);
                    addedCustomResultControls = true;
                }

                // add pagination.
                thisClass.addCustomResultControlsPagination(json);

                if (json && json.formResultMessage) {
                    RdbaCommon.displayAlertboxFixed(json.formResultMessage, json.formResultStatus);
                }

                if (json) {
                    if (typeof(json.csrfKeyPair) !== 'undefined') {
                        DmmdIndexObject.csrfKeyPair = json.csrfKeyPair;
                    }
                }
            })// datatables on xhr complete.
            .on('draw', function() {
                // add listening events.
                thisClass.addCustomResultControlsEvents(dataTable);
            })// datatables on draw complete.
            ;
        });// uiXhrCommonData.done()
    }// activateDataTable


    /**
     * Initialize the class.
     * 
     * @returns {undefined}
     */
    init() {
        // activate data table.
        this.activateDataTable();
    }// init


    /**
     * Listen on bulk action form submit and open as ajax inside dialog.
     * 
     * @returns {undefined}
     */
    listenFormSubmit() {
        let thisClass = this;

        document.addEventListener('submit', function(event) {
            if (event.target && event.target.id === 'demomanagementdialog-list-form') {
                event.preventDefault();
                let thisForm = event.target;

                // validate selected item.
                let formValidated = false;
                let itemIdsArray = [];
                thisForm.querySelectorAll('input[type="checkbox"][name="id[]"]:checked').forEach(function(item, index) {
                    itemIdsArray.push(item.value);
                });
                if (itemIdsArray.length <= 0) {
                    RDTAAlertDialog.alert({
                        'text': DmmdIndexObject.txtPleaseSelectAtLeastOne,
                        'type': 'error'
                    });
                    formValidated = false;
                } else {
                    formValidated = true;
                }

                // validate selected action.
                let selectAction = thisForm.querySelector('#demomanagementdialog-list-actions');
                if (formValidated === true) {
                    if (selectAction && selectAction.value === '') {
                        RDTAAlertDialog.alert({
                            'text': DmmdIndexObject.txtPleaseSelectAction,
                            'type': 'error'
                        });
                        formValidated = false;
                    } else {
                        formValidated = true;
                    }
                }

                if (formValidated === true) {
                    // if form validated.
                    thisClass.listenFormSubmitConfirmDelete(itemIdsArray, selectAction.value);// @TODO[dmmd]: write your own code for delete process.
                }
            }
        });
    }// listenFormSubmit


    /**
     * Ask for confirm delete.
     * 
     * @TODO[dmmd]: write your own code for delete process.
     * @private This method was called from `listenFormSubmit()` method.
     * @param {array} ids
     * @param {string} action
     * @returns {Boolean}
     */
    listenFormSubmitConfirmDelete(ids, action) {
        if (!_.isArray(ids)) {
            console.error('The IDs are not array.');
            return false;
        }

        let thisClass = this;

        if (action === 'delete') {
            // if selected action is delete.
            let confirmValue = confirm(DmmdIndexObject.txtConfirmDelete);
            let thisForm = document.querySelector(thisClass.formIDSelector);
            let submitBtn = thisForm.querySelector('button[type="submit"]');

            let formData = new FormData(thisForm);
            formData.append('ids', ids.join(','));
            formData.append(DmmdIndexObject.csrfName, DmmdIndexObject.csrfKeyPair[DmmdIndexObject.csrfName])
            formData.append(DmmdIndexObject.csrfValue, DmmdIndexObject.csrfKeyPair[DmmdIndexObject.csrfValue])

            if (confirmValue === true) {
                // reset form result placeholder
                thisForm.querySelector('.form-result-placeholder').innerHTML = '';
                // add spinner icon
                thisForm.querySelector('.action-status-placeholder').insertAdjacentHTML('beforeend', '<i class="fa-solid fa-spinner fa-pulse fa-fw loading-icon" aria-hidden="true"></i>');
                // lock submit button
                submitBtn.disabled = true;

                RdbaCommon.XHR({
                    'url': DmmdIndexObject.deleteItemRESTUrlBase + '/' + ids.join(','),
                    'method': DmmdIndexObject.deleteItemRESTMethod,
                    'contentType': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'data': new URLSearchParams(_.toArray(formData)).toString(),
                    'dataType': 'json'
                })
                .catch(function(responseObject) {
                    // XHR failed.
                    let response = responseObject.response;

                    if (response && response.formResultMessage) {
                        RDTAAlertDialog.alert({
                            'type': 'danger',
                            'text': response.formResultMessage
                        });
                    }

                    if (typeof(response) !== 'undefined' && typeof(response.csrfKeyPair) !== 'undefined') {
                        DmmdIndexObject.csrfKeyPair = response.csrfKeyPair;
                    }

                    return Promise.reject(responseObject);
                })
                .then(function(responseObject) {
                    // XHR success.
                    let response = responseObject.response;

                    // reload datatable.
                    new DataTable(thisClass.datatableIDSelector).ajax.reload(null, false);

                    if (typeof(response) !== 'undefined') {
                        if (typeof(response.formResultMessage) !== 'undefined') {
                            RdbaCommon.displayAlertboxFixed(response.formResultMessage, response.formResultStatus);
                        }
                    }

                    if (typeof(response) !== 'undefined' && typeof(response.csrfKeyPair) !== 'undefined') {
                        DmmdIndexObject.csrfKeyPair = response.csrfKeyPair;
                    }

                    return Promise.resolve(responseObject);
                })
                .finally(function() {
                    // remove loading icon
                    thisForm.querySelector('.loading-icon').remove();
                    // unlock submit button
                    submitBtn.disabled = false;
                });
            }
            // end action == delete
        }// endif action
    }// listenFormSubmitConfirmDelete


    /**
     * Reset data tables.
     * 
     * Call from HTML button.<br>
     * Example: <pre>
     * &lt;button onclick=&quot;return ThisClassName.resetDataTable();&quot;&gt;Reset&lt;/button&gt;
     * </pre>
     * 
     * @returns {false}
     */
    static resetDataTable() {
        let $ = jQuery.noConflict();
        let thisClass = new this();

        // reset form
        document.getElementById('rdba-filter-search').value = '';

        // datatables have to call with jQuery.
        new DataTable(thisClass.datatableIDSelector).order(thisClass.defaultSortOrder).search('').draw();// .order must match in columnDefs.

        return false;
    }// resetDataTable


}// DmmdIndexController


document.addEventListener('DOMContentLoaded', function() {
    let indexController = new DmmdIndexController();
    let rdbaXhrDialog = new RdbaXhrDialog({
        'dialogIDSelector': '#demomanagementdialog-editing-dialog',
        'dialogNewInitEvent': 'demomanagementdialog.editing.newinit',
        'dialogReInitEvent': 'demomanagementdialog.editing.reinit',
        'xhrLinksSelector': '.rdba-listpage-addnew, .rdba-listpage-edit'
    });
    indexController.setRdbaXhrDialogObject(rdbaXhrDialog);

    // initialize datatables.
    indexController.init();

    // set of methods to work on click add, edit and open as dialog instead of new page. -----------------
    // links to be ajax.
    rdbaXhrDialog.listenAjaxLinks();
    // listen on closed dialog and maybe change URL.
    rdbaXhrDialog.listenDialogClose();
    // listen on popstate and controls dialog.
    rdbaXhrDialog.listenPopStateControlsDialog();
    // end set of methods to open page as dialog. --------------------------------------------------------------

    // listen form submit (bulk actions).
    indexController.listenFormSubmit();
}, false);
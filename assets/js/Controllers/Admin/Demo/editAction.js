/**
 * Demo management dialog - edit page JS for its controller.
 * 
 * @TODO[dmmd]: rename this class to yours.
 */


class DmmdEditController {


    /**
     * Class constructor.
     * 
     * @param {object} options
     * @returns {StoreManageContentsCategories}
     */
    constructor(options) {
        if (typeof(options) === 'undefined') {
            options = {};
        }

        if (
            !RdbaCommon.isset(() => options.formIDSelector) || 
            (RdbaCommon.isset(() => options.formIDSelector) && _.isEmpty(options.formIDSelector))
        ) {
            options.formIDSelector = '#demomanagementdialog-edit-form'// @TODO[dmmd]: change the form id to matched in your views file.
        }
        this.formIDSelector = options.formIDSelector;

        if (
            !RdbaCommon.isset(() => options.dialogIDSelector) || 
            (RdbaCommon.isset(() => options.dialogIDSelector) && _.isEmpty(options.dialogIDSelector))
        ) {
            options.dialogIDSelector = '#demomanagementdialog-editing-dialog'// @TODO[dmmd]: change the dialog id to matched in your views file.
        }
        this.dialogIDSelector = options.dialogIDSelector;

        if (
            !RdbaCommon.isset(() => options.datatableIDSelector) || 
            (RdbaCommon.isset(() => options.datatableIDSelector) && _.isEmpty(options.datatableIDSelector))
        ) {
            options.datatableIDSelector = '#dmmdListItemsTable';// @TODO[dmmd]: change the data table id to matched in your views file.
        }
        this.datatableIDSelector = options.datatableIDSelector;
    }// constructor


    /**
     * XHR get form data and set it to form fields.
     * 
     * This method was called from `staticInit()` method and outside.
     * 
     * @returns {undefined}
     */
    ajaxGetFormData() {
        if (!document.querySelector(this.formIDSelector)) {
            // if no editing form, do not working to waste cpu.
            return false;
        }

        let thisClass = this;
        let thisForm = document.querySelector(this.formIDSelector);
        let formId = thisForm.querySelector('#id');// @TODO[dmmd]: rename to matched your form input ID.

        RdbaCommon.XHR({
            'url': DmmdIndexObject.getItemRESTUrlBase + '/' + (formId ? formId.value : ''),// @TODO[dmmd]: change to your js object.
            'method': DmmdIndexObject.getItemRESTMethod,// @TODO[dmmd]: change to your js object.
            'contentType': 'application/x-www-form-urlencoded;charset=UTF-8',
            'dataType': 'json'
        })
        .catch(function(responseObject) {
            console.error(responseObject);
            let response = (responseObject ? responseObject.response : {});

            if (typeof(response) !== 'undefined') {
                if (typeof(response.formResultMessage) !== 'undefined') {
                    let alertClass = RdbaCommon.getAlertClassFromStatus(response.formResultStatus);
                    let alertBox = RdbaCommon.renderAlertHtml(alertClass, response.formResultMessage);
                    thisForm.querySelector('.form-result-placeholder').innerHTML = alertBox;
                }
            }

            if (responseObject && responseObject.status && responseObject.status === 404) {
                // if not found.
                // disable form.
                let formElements = (thisForm ? thisForm.elements : []);
                for (var i = 0, len = formElements.length; i < len; ++i) {
                    formElements[i].disabled = true;
                }// endfor;
            }
        })
        .then(function(responseObject) {
            let response = (responseObject ? responseObject.response : {});
            let resultRow = response.result;

            if (typeof(response) !== 'undefined' && typeof(response.csrfKeyPair) !== 'undefined') {
                DmmdIndexObject.csrfKeyPair = response.csrfKeyPair;// @TODO[dmmd]: change to your js object.
                if (typeof(response.csrfName) !== 'undefined' && typeof(response.csrfValue) !== 'undefined') {
                    thisForm.querySelector('#rdba-form-csrf-name').value = response.csrfKeyPair[response.csrfName];
                    thisForm.querySelector('#rdba-form-csrf-value').value = response.csrfKeyPair[response.csrfValue];
                }
            }

            // set the data that have got via ajax to form fields.
            for (let prop in resultRow) {
                if (
                    Object.prototype.hasOwnProperty.call(resultRow, prop) && 
                    document.getElementById(prop) && 
                    prop !== 'id' &&
                    resultRow[prop] !== null
                ) {
                    document.getElementById(prop).value = RdbaCommon.unEscapeHtml(resultRow[prop]);
                }
            }// endfor;
        });
    }// ajaxGetFormData



    /**
     * Listen on form submit and make it XHR.
     * 
     * @returns {undefined}
     */
    listenFormSubmit() {
        let thisClass = this;

        document.addEventListener('submit', function(event) {
            if (
                event.target && 
                event.target.getAttribute('id') && // @TODO[dmmd]: change to event.target.id if your form don't have input name `id`.
                '#' + event.target.getAttribute('id') === thisClass.formIDSelector
            ) {

                event.preventDefault();

                let thisForm = event.target;
                let submitBtn = thisForm.querySelector('button[type="submit"]');
                let formId = thisForm.querySelector('#id');// @TODO[dmmd]: rename to matched your form input ID.

                // set csrf again to prevent firefox form cached.
                if (!DmmdIndexObject.isInDataTablesPage) {// @TODO[dmmd]: change to your js object.
                    thisForm.querySelector('#rdba-form-csrf-name').value = DmmdIndexObject.csrfKeyPair[DmmdIndexObject.csrfName];// @TODO[dmmd]: change to your js object.
                    thisForm.querySelector('#rdba-form-csrf-value').value = DmmdIndexObject.csrfKeyPair[DmmdIndexObject.csrfValue];// @TODO[dmmd]: change to your js object.
                }

                // reset form result placeholder
                thisForm.querySelector('.form-result-placeholder').innerHTML = '';
                // add spinner icon
                thisForm.querySelector('.submit-button-row .control-wrapper').insertAdjacentHTML('beforeend', '<i class="fas fa-spinner fa-pulse fa-fw loading-icon" aria-hidden="true"></i>');
                // lock submit button
                submitBtn.disabled = true;

                let formData = new FormData(thisForm);

                RdbaCommon.XHR({
                    'url': DmmdIndexObject.editItemRESTUrlBase + '/' + (formId ? formId.value : ''),// @TODO[dmmd]: change to your js object.
                    'method': DmmdIndexObject.editItemRESTMethod,// @TODO[dmmd]: change to your js object.
                    'contentType': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'data': new URLSearchParams(_.toArray(formData)).toString(),
                    'dataType': 'json'
                })
                .catch(function(responseObject) {
                    // XHR failed.
                    let response = responseObject.response;

                    if (response && response.formResultMessage) {
                        let alertClass = RdbaCommon.getAlertClassFromStatus(response.formResultStatus);
                        let alertBox = RdbaCommon.renderAlertHtml(alertClass, response.formResultMessage);
                        thisForm.querySelector('.form-result-placeholder').innerHTML = alertBox;
                    }

                    if (typeof(response) !== 'undefined' && typeof(response.csrfKeyPair) !== 'undefined') {
                        DmmdIndexObject.csrfKeyPair = response.csrfKeyPair;// @TODO[dmmd]: change to your js object.
                        if (typeof(response.csrfName) !== 'undefined' && typeof(response.csrfValue) !== 'undefined') {
                            thisForm.querySelector('#rdba-form-csrf-name').value = response.csrfKeyPair[response.csrfName];
                            thisForm.querySelector('#rdba-form-csrf-value').value = response.csrfKeyPair[response.csrfValue];
                        }
                    }

                    return Promise.reject(responseObject);
                })
                .then(function(responseObject) {
                    // XHR success.
                    let response = responseObject.response;

                    if (response.redirectBack) {
                        if (DmmdIndexObject && DmmdIndexObject.isInDataTablesPage && DmmdIndexObject.isInDataTablesPage === true) {
                            // @TODO[dmmd]: change `DmmdIndexObject` to your js object.
                            // this is opening in dialog, close the dialog and reload page.
                            document.querySelector(thisClass.dialogIDSelector + ' [data-dismiss="dialog"]').click();
                            // reload datatable.
                            jQuery(thisClass.datatableIDSelector).DataTable().ajax.reload(null, false);
                        } else {
                            // this is in its page, redirect to the redirect back url.
                            window.location.href = response.redirectBack;
                        }
                    }

                    if (response && response.formResultMessage) {
                        // if there is form result message, display it.
                        RdbaCommon.displayAlertboxFixed(response.formResultMessage, response.formResultStatus);
                    }

                    if (typeof(response) !== 'undefined' && typeof(response.csrfKeyPair) !== 'undefined') {
                        DmmdIndexObject.csrfKeyPair = response.csrfKeyPair;// @TODO[dmmd]: change to your js object.
                        if (typeof(response.csrfName) !== 'undefined' && typeof(response.csrfValue) !== 'undefined') {
                            thisForm.querySelector('#rdba-form-csrf-name').value = response.csrfKeyPair[response.csrfName];
                            thisForm.querySelector('#rdba-form-csrf-value').value = response.csrfKeyPair[response.csrfValue];
                        }
                    }

                    return Promise.resolve(responseObject);
                }, function(error) {
                    // prevent Uncaught (in promise) error.
                })
                .finally(function() {
                    // remove loading icon
                    thisForm.querySelector('.loading-icon').remove();
                    // unlock submit button
                    submitBtn.disabled = false;
                });
            }
        }, false);
    }// listenFormSubmit


    /**
     * Static initialize the class.
     * 
     * This is useful for ajax page.
     * 
     * @returns {undefined}
     */
    static staticInit() {
        let thisClass = new this() ;

        // ajax get form data.
        thisClass.ajaxGetFormData();
        // listen on form submit and make it AJAX request.
        thisClass.listenFormSubmit();
   }// staticInit


}// DmmdEditController


if (document.readyState !== 'loading') {
    // if document loaded.
    // equivalent to jquery document ready.
    // must use together with `document.addEventListener('DOMContentLoaded')`
    // because this condition will be working on js loaded via ajax,
    // but 'DOMContentLoaded' will be working on load the full page.
    DmmdEditController.staticInit();// @TODO[dmmd]: change the class name to matched your class in this file.
}
document.addEventListener('DOMContentLoaded', function() {
    DmmdEditController.staticInit();// @TODO[dmmd]: change the class name to matched your class in this file.
}, false);
document.addEventListener('demomanagementdialog.editing.init', function() {// @TODO[dmmd]: the event name must matched in indexAction.js file.
    // manual trigger initialize class.
    // this is required when... user click edit > save > close dialog > click edit other > now it won't load if there is no this listener.
    let editController = new DmmdEditController();
    // ajax get form data.
    editController.ajaxGetFormData();
});
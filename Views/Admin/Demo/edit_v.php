<?php
/* @var $Assets \Rdb\Modules\RdbAdmin\Libraries\Assets */
/* @var $Modules \Rdb\System\Modules */
/* @var $Views \Rdb\System\Views */
/* @var $Url \Rdb\System\Libraries\Url */
?>
                        <h1 class="rdba-page-content-header"><?php echo $pageTitle; ?></h1>

                        <form id="demomanagementdialog-edit-form" class="rd-form horizontal rdba-edit-form" method="<?php echo (isset($editItemRESTMethod) ? strtolower($editItemRESTMethod) : 'post'); ?>" action="<?php if (isset($editItemRESTUrlBase)) {echo htmlspecialchars($editItemRESTUrlBase, ENT_QUOTES);} ?>">
                            <!-- //@TODO[dmmd]: change method and action PHP variables to matched in the trait. -->

                            <?php 
                            // use form html CSRF because this page can load via XHR, REST by HTML type and this can reduce double call to get CSRF values in JSON type again.
                            if (
                                isset($csrfName) && 
                                isset($csrfValue) && 
                                isset($csrfKeyPair[$csrfName]) &&
                                isset($csrfKeyPair[$csrfValue])
                            ) {
                            ?> 
                            <input id="rdba-form-csrf-name" type="hidden" name="<?php echo $csrfName; ?>" value="<?php echo $csrfKeyPair[$csrfName]; ?>">
                            <input id="rdba-form-csrf-value" type="hidden" name="<?php echo $csrfValue; ?>" value="<?php echo $csrfKeyPair[$csrfValue]; ?>">
                            <?php
                            }
                            ?> 
                            <input id="id" type="hidden" name="id" value="<?php echo ($id ?? ''); ?>">
                            <div class="form-result-placeholder"></div>

                            <!-- //@TODO[dmmd]: write your own form -->
                            <div class="form-group">
                                <label class="control-label" for="user_login"><?php echo d__('demomanagementdialog', 'Title'); ?> <em>*</em></label>
                                <div class="control-wrapper">
                                    <input id="title" type="text" name="title" maxlength="191" required="">
                                </div>
                            </div>
                            <!-- END TODO -->

                            <div class="form-group submit-button-row">
                                <label class="control-label"></label>
                                <div class="control-wrapper">
                                    <button class="rd-button primary rdba-submit-button" type="submit"><?php echo __('Save'); ?></button>
                                </div>
                            </div>
                        </form>
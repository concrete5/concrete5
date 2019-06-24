<?php defined('C5_EXECUTE') or die('Access Denied.');?>
<form method="post" id="login-redirect-form" action="<?php echo $view->url('/dashboard/system/registration/postlogin', 'update_login_redirect')?>">
    <?php echo $this->controller->token->output('update_login_redirect')?>
    <fieldset>
    <legend><?php echo t('After login')?></legend>
    <div class="form-group">
        <div class="input">
            <div class="radio">
              <label>
                <input type="radio" name="LOGIN_REDIRECT" value="HOMEPAGE"  <?php echo (!strlen($site_login_redirect) || $site_login_redirect == 'HOMEPAGE') ? 'checked' : ''?> />
                <span><?php echo t('Redirect to Home')?></span>
              </label>
            </div>

            <div class="radio">
              <label>
                <input type="radio" name="LOGIN_REDIRECT" value="DESKTOP" <?php echo ($site_login_redirect == 'DESKTOP') ? 'checked' : ''?> />
                <span><?php echo t('Redirect to user\'s Desktop')?></span>
              </label>
            </div>

            <div class="radio">
              <label>
                <input type="radio" name="LOGIN_REDIRECT" value="CUSTOM" <?php echo ($site_login_redirect == 'CUSTOM') ? 'checked' : ''?> />
                <span><?php echo t('Redirect to a specific page')?></span>
              </label>
                <div id="login_redirect_custom_cid_wrap" style="display:<?php echo ($site_login_redirect == 'CUSTOM') ? 'block' : 'none'?>">
                <?php
                $formPageSelector = Loader::helper('form/page_selector');
                echo $formPageSelector->selectPage('LOGIN_REDIRECT_CID', $login_redirect_cid);
                ?>
                </div>
            </div>

        </div>
    </div>
    </fieldset>
    <div class="form-group">
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
            </div>
        </div>
    </div>

</form>
<script type="text/javascript">
    $(function(){
        $("#login_redirect_custom_cid_wrap .dialog-launch").dialog();

        $("input[name='LOGIN_REDIRECT']").each(function(i,el){
            el.onchange=function(){isLoginRedirectCustom();}
        })
    });

    function isLoginRedirectCustom(){
        if($("input[name='LOGIN_REDIRECT']:checked").val()=='CUSTOM'){
            $('#login_redirect_custom_cid_wrap').css('display','block');
        }else{
            $('#login_redirect_custom_cid_wrap').css('display','none');
        }
    }
</script>
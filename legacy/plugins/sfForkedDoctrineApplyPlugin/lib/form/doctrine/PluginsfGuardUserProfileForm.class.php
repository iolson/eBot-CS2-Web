<?php

/**
 * PluginsfGuardUserProfile form.
 *
 * @author     ##AUTHOR_NAME##
 *
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginsfGuardUserProfileForm extends BasesfGuardUserProfileForm
{
    public function setup()
    {
        parent::setup();

        // unset type field if it's set.
        if (isset($this['type'])) {
            unset($this['type']);
        }
    }

    protected function isCaptchaEnabled()
    {
        return sfConfig::get('app_recaptcha_enabled');
    }

    protected function addCaptcha()
    {
        $this->widgetSchema['captcha'] = new sfWidgetFormReCaptcha([
            'public_key' => sfConfig::get('app_recaptcha_public_key'),
        ]);

        $this->validatorSchema['captcha'] = new sfValidatorReCaptcha([
            'private_key' => sfConfig::get('app_recaptcha_private_key'),
        ]);
        $this->validatorSchema['captcha']
            ->setMessage('captcha', sfContext::getInstance()->getI18N()->
                __('The captcha is not valid (%error%).', [], 'sfForkedApply'))
            ->setMessage('server_problem', sfContext::getInstance()->getI18N()->
                __('Unable to check the captcha from the server (%error%).', [], 'sfForkedApply'));
    }

    public function getStylesheets()
    {
        return ['/sfForkedDoctrineApplyPlugin/css/forked' => 'all'];
    }
}

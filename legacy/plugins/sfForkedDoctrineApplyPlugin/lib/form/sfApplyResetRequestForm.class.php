<?php

class sfApplyResetRequestForm extends sfForm
{
    public function configure()
    {
        parent::configure();

        $this->setWidget('username_or_email',
            new sfWidgetFormInput([], ['maxlength' => 100]));

        $this->setValidator('username_or_email',
            new sfValidatorOr([
                new sfValidatorAnd([
                    new sfValidatorString([
                        'required' => true,
                        'trim' => true,
                        'min_length' => 4,
                        'max_length' => 16]),
                    new sfValidatorDoctrineChoice([
                        'model' => 'sfGuardUser',
                        'column' => 'username'],
                        ['invalid' => 'There is no such user.'])]),
                new sfValidatorEmail(['required' => true])]
            ));

        $this->widgetSchema->setNameFormat('sfApplyResetRequest[%s]');
        $this->widgetSchema->setFormFormatterName('list');

        // Include captcha if enabled
        if ($this->isCaptchaEnabled()) {
            $this->addCaptcha();
        }
    }

    public function isCaptchaEnabled()
    {
        return sfConfig::get('app_recaptcha_enabled');
    }

    public function addCaptcha()
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

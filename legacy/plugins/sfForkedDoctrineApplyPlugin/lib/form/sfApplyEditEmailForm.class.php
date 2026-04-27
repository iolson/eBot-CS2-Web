<?php

class sfApplyEditEmailForm extends sfForm
{
    public function configure()
    {
        $this->setWidget('email', new sfWidgetFormInputText);
        $this->setValidator('email', new sfValidatorAnd([
            new sfValidatorEmail(['required' => true, 'trim' => true]),
            new sfValidatorString(['required' => true, 'max_length' => 255]),
            new sfValidatorApplyEditMail(
                ['id' => sfContext::getInstance()->getUser()->getGuardUser()->getId()],
                ['invalid' => 'An account with that email address already exists. If you have forgotten your password, click "cancel", then "Reset My Password."']),
        ]));

        $this->widgetSchema->setNameFormat('sfApplyEditEmail[%s]');
        $this->widgetSchema->setFormFormatterName('list');
    }

    public function getStylesheets()
    {
        return ['/sfForkedDoctrineApplyPlugin/css/forked' => 'all'];
    }
}

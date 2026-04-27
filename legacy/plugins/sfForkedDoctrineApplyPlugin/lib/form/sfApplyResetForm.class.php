<?php

class sfApplyResetForm extends sfForm
{
    public function configure()
    {
        $this->setWidget('password', new sfWidgetFormInputPassword(
            [], ['maxlength' => 128]));
        $this->setWidget('password2', new sfWidgetFormInputPassword(
            [], ['maxlength' => 128]));

        $this->widgetSchema->setLabels([
            'password' => 'Choose NEW Password',
            'password2' => 'Confirm NEW Password']);

        $this->widgetSchema->setNameFormat('sfApplyReset[%s]');
        $this->widgetSchema->setFormFormatterName('list');

        $this->setValidator('password', new sfValidatorApplyPassword);
        $this->setValidator('password2', new sfValidatorApplyPassword);

        $this->validatorSchema->setPostValidator(
            new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL,
                'password2', [], ['invalid' => 'The passwords did not match.']));
    }

    public function getStylesheets()
    {
        return ['/sfForkedDoctrineApplyPlugin/css/forked' => 'all'];
    }
}

<?php

/**
 * Seasons form.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SeasonsForm extends BaseSeasonsForm
{
    public function configure()
    {
        unset($this['created_at'], $this['updated_at']);

        $this->widgetSchema['start'] = new sfWidgetFormInputText(['default' => date('d.m.Y')], ['id' => 'start', 'style' => 'width:150px;']);
        $this->widgetSchema['end'] = new sfWidgetFormInputText(['default' => date('d.m.Y')], ['id' => 'end', 'style' => 'width:150px;']);
        $this->validatorSchema['start'] = new sfValidatorDateTime(['required' => false]);
        $this->validatorSchema['end'] = new sfValidatorDateTime(['required' => false]);

        $this->widgetSchema['logo'] = new sfWidgetFormInputFile(['label' => 'Season Logo']);
        $this->validatorSchema['logo'] = new sfValidatorFile(['required' => false, 'path' => sfConfig::get('sf_upload_dir').'/seasons', 'mime_types' => 'web_images']);

        $this->widgetSchema['active']->setDefault(true);
    }
}

<?php

/**
 * Configs filter form base class.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseConfigsFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'name' => new sfWidgetFormFilterInput,
            'description' => new sfWidgetFormFilterInput,
            'content' => new sfWidgetFormFilterInput,
            'created_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'updated_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
        ]);

        $this->setValidators([
            'name' => new sfValidatorPass(['required' => false]),
            'description' => new sfValidatorPass(['required' => false]),
            'content' => new sfValidatorPass(['required' => false]),
            'created_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'updated_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
        ]);

        $this->widgetSchema->setNameFormat('configs_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Configs';
    }

    public function getFields()
    {
        return [
            'id' => 'Number',
            'name' => 'Text',
            'description' => 'Text',
            'content' => 'Text',
            'created_at' => 'Date',
            'updated_at' => 'Date',
        ];
    }
}

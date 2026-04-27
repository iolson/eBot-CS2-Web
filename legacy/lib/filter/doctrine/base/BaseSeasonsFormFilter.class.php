<?php

/**
 * Seasons filter form base class.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSeasonsFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'name' => new sfWidgetFormFilterInput(['with_empty' => false]),
            'event' => new sfWidgetFormFilterInput(['with_empty' => false]),
            'start' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'end' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'link' => new sfWidgetFormFilterInput,
            'logo' => new sfWidgetFormFilterInput,
            'active' => new sfWidgetFormChoice(['choices' => ['' => 'yes or no', 1 => 'yes', 0 => 'no']]),
            'created_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'updated_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
        ]);

        $this->setValidators([
            'name' => new sfValidatorPass(['required' => false]),
            'event' => new sfValidatorPass(['required' => false]),
            'start' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'end' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'link' => new sfValidatorPass(['required' => false]),
            'logo' => new sfValidatorPass(['required' => false]),
            'active' => new sfValidatorChoice(['required' => false, 'choices' => ['', 1, 0]]),
            'created_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'updated_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
        ]);

        $this->widgetSchema->setNameFormat('seasons_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Seasons';
    }

    public function getFields()
    {
        return [
            'id' => 'Number',
            'name' => 'Text',
            'event' => 'Text',
            'start' => 'Date',
            'end' => 'Date',
            'link' => 'Text',
            'logo' => 'Text',
            'active' => 'Boolean',
            'created_at' => 'Date',
            'updated_at' => 'Date',
        ];
    }
}

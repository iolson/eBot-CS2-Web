<?php

/**
 * Teams filter form base class.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTeamsFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'name' => new sfWidgetFormFilterInput(['with_empty' => false]),
            'shorthandle' => new sfWidgetFormFilterInput(['with_empty' => false]),
            'flag' => new sfWidgetFormFilterInput(['with_empty' => false]),
            'link' => new sfWidgetFormFilterInput,
            'created_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'updated_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
        ]);

        $this->setValidators([
            'name' => new sfValidatorPass(['required' => false]),
            'shorthandle' => new sfValidatorPass(['required' => false]),
            'flag' => new sfValidatorPass(['required' => false]),
            'link' => new sfValidatorPass(['required' => false]),
            'created_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'updated_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
        ]);

        $this->widgetSchema->setNameFormat('teams_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Teams';
    }

    public function getFields()
    {
        return [
            'id' => 'Number',
            'name' => 'Text',
            'shorthandle' => 'Text',
            'flag' => 'Text',
            'link' => 'Text',
            'created_at' => 'Date',
            'updated_at' => 'Date',
        ];
    }
}

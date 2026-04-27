<?php

/**
 * Servers filter form base class.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseServersFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'ip' => new sfWidgetFormFilterInput(['with_empty' => false]),
            'rcon' => new sfWidgetFormFilterInput(['with_empty' => false]),
            'hostname' => new sfWidgetFormFilterInput(['with_empty' => false]),
            'tv_ip' => new sfWidgetFormFilterInput,
            'created_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'updated_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
        ]);

        $this->setValidators([
            'ip' => new sfValidatorPass(['required' => false]),
            'rcon' => new sfValidatorPass(['required' => false]),
            'hostname' => new sfValidatorPass(['required' => false]),
            'tv_ip' => new sfValidatorPass(['required' => false]),
            'created_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'updated_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
        ]);

        $this->widgetSchema->setNameFormat('servers_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Servers';
    }

    public function getFields()
    {
        return [
            'id' => 'Number',
            'ip' => 'Text',
            'rcon' => 'Text',
            'hostname' => 'Text',
            'tv_ip' => 'Text',
            'created_at' => 'Date',
            'updated_at' => 'Date',
        ];
    }
}

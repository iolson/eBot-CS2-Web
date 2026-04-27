<?php

/**
 * ServerMessages filter form base class.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseServerMessagesFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'season_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Season'), 'add_empty' => true]),
            'message' => new sfWidgetFormFilterInput,
            'active' => new sfWidgetFormChoice(['choices' => ['' => 'yes or no', 1 => 'yes', 0 => 'no']]),
            'created_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'updated_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
        ]);

        $this->setValidators([
            'season_id' => new sfValidatorDoctrineChoice(['required' => false, 'model' => $this->getRelatedModelName('Season'), 'column' => 'id']),
            'message' => new sfValidatorPass(['required' => false]),
            'active' => new sfValidatorChoice(['required' => false, 'choices' => ['', 1, 0]]),
            'created_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'updated_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
        ]);

        $this->widgetSchema->setNameFormat('server_messages_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'ServerMessages';
    }

    public function getFields()
    {
        return [
            'id' => 'Number',
            'season_id' => 'ForeignKey',
            'message' => 'Text',
            'active' => 'Boolean',
            'created_at' => 'Date',
            'updated_at' => 'Date',
        ];
    }
}

<?php

/**
 * Maps form base class.
 *
 * @method Maps getObject() Returns the current form's model object
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMapsForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id' => new sfWidgetFormInputHidden,
            'match_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Match'), 'add_empty' => false]),
            'map_name' => new sfWidgetFormInputText,
            'score_1' => new sfWidgetFormInputText,
            'score_2' => new sfWidgetFormInputText,
            'current_side' => new sfWidgetFormChoice(['choices' => ['ct' => 'ct', 't' => 't']]),
            'status' => new sfWidgetFormInputText,
            'maps_for' => new sfWidgetFormChoice(['choices' => ['default' => 'default', 'team1' => 'team1', 'team2' => 'team2']]),
            'nb_ot' => new sfWidgetFormInputText,
            'identifier_id' => new sfWidgetFormInputText,
            'tv_record_file' => new sfWidgetFormInputText,
            'created_at' => new sfWidgetFormDateTime,
            'updated_at' => new sfWidgetFormDateTime,
        ]);

        $this->setValidators([
            'id' => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'match_id' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Match')]),
            'map_name' => new sfValidatorPass(['required' => false]),
            'score_1' => new sfValidatorInteger(['required' => false]),
            'score_2' => new sfValidatorInteger(['required' => false]),
            'current_side' => new sfValidatorChoice(['choices' => [0 => 'ct', 1 => 't'], 'required' => false]),
            'status' => new sfValidatorInteger(['required' => false]),
            'maps_for' => new sfValidatorChoice(['choices' => [0 => 'default', 1 => 'team1', 2 => 'team2'], 'required' => false]),
            'nb_ot' => new sfValidatorInteger(['required' => false]),
            'identifier_id' => new sfValidatorInteger(['required' => false]),
            'tv_record_file' => new sfValidatorPass(['required' => false]),
            'created_at' => new sfValidatorDateTime,
            'updated_at' => new sfValidatorDateTime,
        ]);

        $this->widgetSchema->setNameFormat('maps[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Maps';
    }
}

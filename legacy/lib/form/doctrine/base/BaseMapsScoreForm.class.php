<?php

/**
 * MapsScore form base class.
 *
 * @method MapsScore getObject() Returns the current form's model object
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMapsScoreForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id' => new sfWidgetFormInputHidden,
            'map_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Map'), 'add_empty' => false]),
            'type_score' => new sfWidgetFormChoice(['choices' => ['normal' => 'normal', 'ot' => 'ot']]),
            'score1_side1' => new sfWidgetFormInputText,
            'score1_side2' => new sfWidgetFormInputText,
            'score2_side1' => new sfWidgetFormInputText,
            'score2_side2' => new sfWidgetFormInputText,
            'created_at' => new sfWidgetFormDateTime,
            'updated_at' => new sfWidgetFormDateTime,
        ]);

        $this->setValidators([
            'id' => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'map_id' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Map')]),
            'type_score' => new sfValidatorChoice(['choices' => [0 => 'normal', 1 => 'ot'], 'required' => false]),
            'score1_side1' => new sfValidatorInteger(['required' => false]),
            'score1_side2' => new sfValidatorInteger(['required' => false]),
            'score2_side1' => new sfValidatorInteger(['required' => false]),
            'score2_side2' => new sfValidatorInteger(['required' => false]),
            'created_at' => new sfValidatorDateTime,
            'updated_at' => new sfValidatorDateTime,
        ]);

        $this->widgetSchema->setNameFormat('maps_score[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'MapsScore';
    }
}

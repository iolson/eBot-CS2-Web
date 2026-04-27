<?php

/**
 * PlayerKill form base class.
 *
 * @method PlayerKill getObject() Returns the current form's model object
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePlayerKillForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id' => new sfWidgetFormInputHidden,
            'match_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Match'), 'add_empty' => false]),
            'map_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Map'), 'add_empty' => false]),
            'killer_name' => new sfWidgetFormInputText,
            'killer_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Killer'), 'add_empty' => true]),
            'killer_team' => new sfWidgetFormInputText,
            'killed_name' => new sfWidgetFormInputText,
            'killed_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Killed'), 'add_empty' => true]),
            'killed_team' => new sfWidgetFormInputText,
            'weapon' => new sfWidgetFormInputText,
            'headshot' => new sfWidgetFormInputCheckbox,
            'round_id' => new sfWidgetFormInputText,
            'created_at' => new sfWidgetFormDateTime,
            'updated_at' => new sfWidgetFormDateTime,
        ]);

        $this->setValidators([
            'id' => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'match_id' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Match')]),
            'map_id' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Map')]),
            'killer_name' => new sfValidatorPass(['required' => false]),
            'killer_id' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Killer'), 'required' => false]),
            'killer_team' => new sfValidatorPass(['required' => false]),
            'killed_name' => new sfValidatorPass(['required' => false]),
            'killed_id' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Killed'), 'required' => false]),
            'killed_team' => new sfValidatorPass(['required' => false]),
            'weapon' => new sfValidatorPass(['required' => false]),
            'headshot' => new sfValidatorBoolean(['required' => false]),
            'round_id' => new sfValidatorInteger(['required' => false]),
            'created_at' => new sfValidatorDateTime,
            'updated_at' => new sfValidatorDateTime,
        ]);

        $this->widgetSchema->setNameFormat('player_kill[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'PlayerKill';
    }
}

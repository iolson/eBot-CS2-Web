<?php

/**
 * PlayersSnapshot form base class.
 *
 * @method PlayersSnapshot getObject() Returns the current form's model object
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePlayersSnapshotForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id' => new sfWidgetFormInputHidden,
            'player_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Player'), 'add_empty' => false]),
            'player_key' => new sfWidgetFormInputText,
            'first_side' => new sfWidgetFormChoice(['choices' => ['ct' => 'ct', 't' => 't', 'other' => 'other']]),
            'current_side' => new sfWidgetFormChoice(['choices' => ['ct' => 'ct', 't' => 't', 'other' => 'other']]),
            'nb_kill' => new sfWidgetFormInputText,
            'assist' => new sfWidgetFormInputText,
            'death' => new sfWidgetFormInputText,
            'point' => new sfWidgetFormInputText,
            'hs' => new sfWidgetFormInputText,
            'defuse' => new sfWidgetFormInputText,
            'bombe' => new sfWidgetFormInputText,
            'tk' => new sfWidgetFormInputText,
            'nb1' => new sfWidgetFormInputText,
            'nb2' => new sfWidgetFormInputText,
            'nb3' => new sfWidgetFormInputText,
            'nb4' => new sfWidgetFormInputText,
            'nb5' => new sfWidgetFormInputText,
            'nb1kill' => new sfWidgetFormInputText,
            'nb2kill' => new sfWidgetFormInputText,
            'nb3kill' => new sfWidgetFormInputText,
            'nb4kill' => new sfWidgetFormInputText,
            'nb5kill' => new sfWidgetFormInputText,
            'pluskill' => new sfWidgetFormInputText,
            'firstkill' => new sfWidgetFormInputText,
            'round_id' => new sfWidgetFormInputText,
            'created_at' => new sfWidgetFormDateTime,
            'updated_at' => new sfWidgetFormDateTime,
        ]);

        $this->setValidators([
            'id' => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'player_id' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Player')]),
            'player_key' => new sfValidatorPass(['required' => false]),
            'first_side' => new sfValidatorChoice(['choices' => [0 => 'ct', 1 => 't', 2 => 'other'], 'required' => false]),
            'current_side' => new sfValidatorChoice(['choices' => [0 => 'ct', 1 => 't', 2 => 'other'], 'required' => false]),
            'nb_kill' => new sfValidatorInteger(['required' => false]),
            'assist' => new sfValidatorInteger(['required' => false]),
            'death' => new sfValidatorInteger(['required' => false]),
            'point' => new sfValidatorInteger(['required' => false]),
            'hs' => new sfValidatorInteger(['required' => false]),
            'defuse' => new sfValidatorInteger(['required' => false]),
            'bombe' => new sfValidatorInteger(['required' => false]),
            'tk' => new sfValidatorInteger(['required' => false]),
            'nb1' => new sfValidatorInteger(['required' => false]),
            'nb2' => new sfValidatorInteger(['required' => false]),
            'nb3' => new sfValidatorInteger(['required' => false]),
            'nb4' => new sfValidatorInteger(['required' => false]),
            'nb5' => new sfValidatorInteger(['required' => false]),
            'nb1kill' => new sfValidatorInteger(['required' => false]),
            'nb2kill' => new sfValidatorInteger(['required' => false]),
            'nb3kill' => new sfValidatorInteger(['required' => false]),
            'nb4kill' => new sfValidatorInteger(['required' => false]),
            'nb5kill' => new sfValidatorInteger(['required' => false]),
            'pluskill' => new sfValidatorInteger(['required' => false]),
            'firstkill' => new sfValidatorInteger(['required' => false]),
            'round_id' => new sfValidatorInteger(['required' => false]),
            'created_at' => new sfValidatorDateTime,
            'updated_at' => new sfValidatorDateTime,
        ]);

        $this->widgetSchema->setNameFormat('players_snapshot[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'PlayersSnapshot';
    }
}

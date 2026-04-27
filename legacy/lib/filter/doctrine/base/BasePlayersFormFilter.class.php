<?php

/**
 * Players filter form base class.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePlayersFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'match_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Match'), 'add_empty' => true]),
            'map_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Map'), 'add_empty' => true]),
            'player_key' => new sfWidgetFormFilterInput,
            'team' => new sfWidgetFormChoice(['choices' => ['' => '', 'a' => 'a', 'b' => 'b', 'other' => 'other']]),
            'ip' => new sfWidgetFormFilterInput,
            'steamid' => new sfWidgetFormFilterInput,
            'first_side' => new sfWidgetFormChoice(['choices' => ['' => '', 'ct' => 'ct', 't' => 't', 'other' => 'other']]),
            'current_side' => new sfWidgetFormChoice(['choices' => ['' => '', 'ct' => 'ct', 't' => 't', 'other' => 'other']]),
            'pseudo' => new sfWidgetFormFilterInput,
            'nb_kill' => new sfWidgetFormFilterInput,
            'assist' => new sfWidgetFormFilterInput,
            'death' => new sfWidgetFormFilterInput,
            'point' => new sfWidgetFormFilterInput,
            'hs' => new sfWidgetFormFilterInput,
            'defuse' => new sfWidgetFormFilterInput,
            'bombe' => new sfWidgetFormFilterInput,
            'tk' => new sfWidgetFormFilterInput,
            'nb1' => new sfWidgetFormFilterInput,
            'nb2' => new sfWidgetFormFilterInput,
            'nb3' => new sfWidgetFormFilterInput,
            'nb4' => new sfWidgetFormFilterInput,
            'nb5' => new sfWidgetFormFilterInput,
            'nb1kill' => new sfWidgetFormFilterInput,
            'nb2kill' => new sfWidgetFormFilterInput,
            'nb3kill' => new sfWidgetFormFilterInput,
            'nb4kill' => new sfWidgetFormFilterInput,
            'nb5kill' => new sfWidgetFormFilterInput,
            'pluskill' => new sfWidgetFormFilterInput,
            'firstkill' => new sfWidgetFormFilterInput,
            'created_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'updated_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
        ]);

        $this->setValidators([
            'match_id' => new sfValidatorDoctrineChoice(['required' => false, 'model' => $this->getRelatedModelName('Match'), 'column' => 'id']),
            'map_id' => new sfValidatorDoctrineChoice(['required' => false, 'model' => $this->getRelatedModelName('Map'), 'column' => 'id']),
            'player_key' => new sfValidatorPass(['required' => false]),
            'team' => new sfValidatorChoice(['required' => false, 'choices' => ['a' => 'a', 'b' => 'b', 'other' => 'other']]),
            'ip' => new sfValidatorPass(['required' => false]),
            'steamid' => new sfValidatorPass(['required' => false]),
            'first_side' => new sfValidatorChoice(['required' => false, 'choices' => ['ct' => 'ct', 't' => 't', 'other' => 'other']]),
            'current_side' => new sfValidatorChoice(['required' => false, 'choices' => ['ct' => 'ct', 't' => 't', 'other' => 'other']]),
            'pseudo' => new sfValidatorPass(['required' => false]),
            'nb_kill' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'assist' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'death' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'point' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'hs' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'defuse' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'bombe' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'tk' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'nb1' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'nb2' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'nb3' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'nb4' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'nb5' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'nb1kill' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'nb2kill' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'nb3kill' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'nb4kill' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'nb5kill' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'pluskill' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'firstkill' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'created_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'updated_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
        ]);

        $this->widgetSchema->setNameFormat('players_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Players';
    }

    public function getFields()
    {
        return [
            'id' => 'Number',
            'match_id' => 'ForeignKey',
            'map_id' => 'ForeignKey',
            'player_key' => 'Text',
            'team' => 'Enum',
            'ip' => 'Text',
            'steamid' => 'Text',
            'first_side' => 'Enum',
            'current_side' => 'Enum',
            'pseudo' => 'Text',
            'nb_kill' => 'Number',
            'assist' => 'Number',
            'death' => 'Number',
            'point' => 'Number',
            'hs' => 'Number',
            'defuse' => 'Number',
            'bombe' => 'Number',
            'tk' => 'Number',
            'nb1' => 'Number',
            'nb2' => 'Number',
            'nb3' => 'Number',
            'nb4' => 'Number',
            'nb5' => 'Number',
            'nb1kill' => 'Number',
            'nb2kill' => 'Number',
            'nb3kill' => 'Number',
            'nb4kill' => 'Number',
            'nb5kill' => 'Number',
            'pluskill' => 'Number',
            'firstkill' => 'Number',
            'created_at' => 'Date',
            'updated_at' => 'Date',
        ];
    }
}

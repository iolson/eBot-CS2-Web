<?php

/**
 * PlayersSnapshot filter form base class.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePlayersSnapshotFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'player_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Player'), 'add_empty' => true]),
            'player_key' => new sfWidgetFormFilterInput,
            'first_side' => new sfWidgetFormChoice(['choices' => ['' => '', 'ct' => 'ct', 't' => 't', 'other' => 'other']]),
            'current_side' => new sfWidgetFormChoice(['choices' => ['' => '', 'ct' => 'ct', 't' => 't', 'other' => 'other']]),
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
            'round_id' => new sfWidgetFormFilterInput,
            'created_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'updated_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
        ]);

        $this->setValidators([
            'player_id' => new sfValidatorDoctrineChoice(['required' => false, 'model' => $this->getRelatedModelName('Player'), 'column' => 'id']),
            'player_key' => new sfValidatorPass(['required' => false]),
            'first_side' => new sfValidatorChoice(['required' => false, 'choices' => ['ct' => 'ct', 't' => 't', 'other' => 'other']]),
            'current_side' => new sfValidatorChoice(['required' => false, 'choices' => ['ct' => 'ct', 't' => 't', 'other' => 'other']]),
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
            'round_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'created_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'updated_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
        ]);

        $this->widgetSchema->setNameFormat('players_snapshot_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'PlayersSnapshot';
    }

    public function getFields()
    {
        return [
            'id' => 'Number',
            'player_id' => 'ForeignKey',
            'player_key' => 'Text',
            'first_side' => 'Enum',
            'current_side' => 'Enum',
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
            'round_id' => 'Number',
            'created_at' => 'Date',
            'updated_at' => 'Date',
        ];
    }
}

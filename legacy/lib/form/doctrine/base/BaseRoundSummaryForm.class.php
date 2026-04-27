<?php

/**
 * RoundSummary form base class.
 *
 * @method RoundSummary getObject() Returns the current form's model object
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseRoundSummaryForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id' => new sfWidgetFormInputHidden,
            'match_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Match'), 'add_empty' => false]),
            'map_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Map'), 'add_empty' => false]),
            'bomb_planted' => new sfWidgetFormInputCheckbox,
            'bomb_defused' => new sfWidgetFormInputCheckbox,
            'bomb_exploded' => new sfWidgetFormInputCheckbox,
            'win_type' => new sfWidgetFormChoice(['choices' => ['bombdefused' => 'bombdefused', 'bombeexploded' => 'bombeexploded', 'normal' => 'normal', 'saved' => 'saved']]),
            'team_win' => new sfWidgetFormChoice(['choices' => ['a' => 'a', 'b' => 'b']]),
            'ct_win' => new sfWidgetFormInputCheckbox,
            't_win' => new sfWidgetFormInputCheckbox,
            'score_a' => new sfWidgetFormInputText,
            'score_b' => new sfWidgetFormInputText,
            'best_killer' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('BestKiller'), 'add_empty' => true]),
            'best_killer_nb' => new sfWidgetFormInputText,
            'best_killer_fk' => new sfWidgetFormInputCheckbox,
            'best_action_type' => new sfWidgetFormInputText,
            'best_action_param' => new sfWidgetFormInputText,
            'backup_file_name' => new sfWidgetFormInputText,
            'round_id' => new sfWidgetFormInputText,
            'created_at' => new sfWidgetFormDateTime,
            'updated_at' => new sfWidgetFormDateTime,
        ]);

        $this->setValidators([
            'id' => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'match_id' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Match')]),
            'map_id' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Map')]),
            'bomb_planted' => new sfValidatorBoolean(['required' => false]),
            'bomb_defused' => new sfValidatorBoolean(['required' => false]),
            'bomb_exploded' => new sfValidatorBoolean(['required' => false]),
            'win_type' => new sfValidatorChoice(['choices' => [0 => 'bombdefused', 1 => 'bombeexploded', 2 => 'normal', 3 => 'saved'], 'required' => false]),
            'team_win' => new sfValidatorChoice(['choices' => [0 => 'a', 1 => 'b'], 'required' => false]),
            'ct_win' => new sfValidatorBoolean(['required' => false]),
            't_win' => new sfValidatorBoolean(['required' => false]),
            'score_a' => new sfValidatorInteger(['required' => false]),
            'score_b' => new sfValidatorInteger(['required' => false]),
            'best_killer' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('BestKiller'), 'required' => false]),
            'best_killer_nb' => new sfValidatorInteger(['required' => false]),
            'best_killer_fk' => new sfValidatorBoolean(['required' => false]),
            'best_action_type' => new sfValidatorPass(['required' => false]),
            'best_action_param' => new sfValidatorPass(['required' => false]),
            'backup_file_name' => new sfValidatorPass(['required' => false]),
            'round_id' => new sfValidatorInteger(['required' => false]),
            'created_at' => new sfValidatorDateTime,
            'updated_at' => new sfValidatorDateTime,
        ]);

        $this->widgetSchema->setNameFormat('round_summary[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'RoundSummary';
    }
}

<?php

/**
 * TeamsInSeasons form base class.
 *
 * @method TeamsInSeasons getObject() Returns the current form's model object
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTeamsInSeasonsForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id' => new sfWidgetFormInputHidden,
            'season_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Seasons'), 'add_empty' => true]),
            'team_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Teams'), 'add_empty' => true]),
            'created_at' => new sfWidgetFormDateTime,
            'updated_at' => new sfWidgetFormDateTime,
        ]);

        $this->setValidators([
            'id' => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'season_id' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Seasons'), 'required' => false]),
            'team_id' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Teams'), 'required' => false]),
            'created_at' => new sfValidatorDateTime,
            'updated_at' => new sfValidatorDateTime,
        ]);

        $this->widgetSchema->setNameFormat('teams_in_seasons[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'TeamsInSeasons';
    }
}

<?php

/**
 * TeamsInSeasons filter form base class.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTeamsInSeasonsFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'season_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Seasons'), 'add_empty' => true]),
            'team_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Teams'), 'add_empty' => true]),
            'created_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'updated_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
        ]);

        $this->setValidators([
            'season_id' => new sfValidatorDoctrineChoice(['required' => false, 'model' => $this->getRelatedModelName('Seasons'), 'column' => 'id']),
            'team_id' => new sfValidatorDoctrineChoice(['required' => false, 'model' => $this->getRelatedModelName('Teams'), 'column' => 'id']),
            'created_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'updated_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
        ]);

        $this->widgetSchema->setNameFormat('teams_in_seasons_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'TeamsInSeasons';
    }

    public function getFields()
    {
        return [
            'id' => 'Number',
            'season_id' => 'ForeignKey',
            'team_id' => 'ForeignKey',
            'created_at' => 'Date',
            'updated_at' => 'Date',
        ];
    }
}

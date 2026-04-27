<?php

/**
 * sfGuardGroup filter form base class.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasesfGuardGroupFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'name' => new sfWidgetFormFilterInput,
            'description' => new sfWidgetFormFilterInput,
            'created_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'updated_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'users_list' => new sfWidgetFormDoctrineChoice(['multiple' => true, 'model' => 'sfGuardUser']),
            'permissions_list' => new sfWidgetFormDoctrineChoice(['multiple' => true, 'model' => 'sfGuardPermission']),
        ]);

        $this->setValidators([
            'name' => new sfValidatorPass(['required' => false]),
            'description' => new sfValidatorPass(['required' => false]),
            'created_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'updated_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'users_list' => new sfValidatorDoctrineChoice(['multiple' => true, 'model' => 'sfGuardUser', 'required' => false]),
            'permissions_list' => new sfValidatorDoctrineChoice(['multiple' => true, 'model' => 'sfGuardPermission', 'required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('sf_guard_group_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function addUsersListColumnQuery(Doctrine_Query $query, $field, $values)
    {
        if (! is_array($values)) {
            $values = [$values];
        }

        if (! count($values)) {
            return;
        }

        $query
            ->leftJoin($query->getRootAlias().'.sfGuardUserGroup sfGuardUserGroup')
            ->andWhereIn('sfGuardUserGroup.user_id', $values);
    }

    public function addPermissionsListColumnQuery(Doctrine_Query $query, $field, $values)
    {
        if (! is_array($values)) {
            $values = [$values];
        }

        if (! count($values)) {
            return;
        }

        $query
            ->leftJoin($query->getRootAlias().'.sfGuardGroupPermission sfGuardGroupPermission')
            ->andWhereIn('sfGuardGroupPermission.permission_id', $values);
    }

    public function getModelName()
    {
        return 'sfGuardGroup';
    }

    public function getFields()
    {
        return [
            'id' => 'Number',
            'name' => 'Text',
            'description' => 'Text',
            'created_at' => 'Date',
            'updated_at' => 'Date',
            'users_list' => 'ManyKey',
            'permissions_list' => 'ManyKey',
        ];
    }
}

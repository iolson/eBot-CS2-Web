<?php

/**
 * sfGuardUser filter form base class.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasesfGuardUserFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'first_name' => new sfWidgetFormFilterInput,
            'last_name' => new sfWidgetFormFilterInput,
            'email_address' => new sfWidgetFormFilterInput(['with_empty' => false]),
            'username' => new sfWidgetFormFilterInput(['with_empty' => false]),
            'algorithm' => new sfWidgetFormFilterInput(['with_empty' => false]),
            'salt' => new sfWidgetFormFilterInput,
            'password' => new sfWidgetFormFilterInput,
            'is_active' => new sfWidgetFormChoice(['choices' => ['' => 'yes or no', 1 => 'yes', 0 => 'no']]),
            'is_super_admin' => new sfWidgetFormChoice(['choices' => ['' => 'yes or no', 1 => 'yes', 0 => 'no']]),
            'last_login' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate]),
            'created_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'updated_at' => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate, 'to_date' => new sfWidgetFormDate, 'with_empty' => false]),
            'groups_list' => new sfWidgetFormDoctrineChoice(['multiple' => true, 'model' => 'sfGuardGroup']),
            'permissions_list' => new sfWidgetFormDoctrineChoice(['multiple' => true, 'model' => 'sfGuardPermission']),
        ]);

        $this->setValidators([
            'first_name' => new sfValidatorPass(['required' => false]),
            'last_name' => new sfValidatorPass(['required' => false]),
            'email_address' => new sfValidatorPass(['required' => false]),
            'username' => new sfValidatorPass(['required' => false]),
            'algorithm' => new sfValidatorPass(['required' => false]),
            'salt' => new sfValidatorPass(['required' => false]),
            'password' => new sfValidatorPass(['required' => false]),
            'is_active' => new sfValidatorChoice(['required' => false, 'choices' => ['', 1, 0]]),
            'is_super_admin' => new sfValidatorChoice(['required' => false, 'choices' => ['', 1, 0]]),
            'last_login' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'created_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'updated_at' => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'groups_list' => new sfValidatorDoctrineChoice(['multiple' => true, 'model' => 'sfGuardGroup', 'required' => false]),
            'permissions_list' => new sfValidatorDoctrineChoice(['multiple' => true, 'model' => 'sfGuardPermission', 'required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('sf_guard_user_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function addGroupsListColumnQuery(Doctrine_Query $query, $field, $values)
    {
        if (! is_array($values)) {
            $values = [$values];
        }

        if (! count($values)) {
            return;
        }

        $query
            ->leftJoin($query->getRootAlias().'.sfGuardUserGroup sfGuardUserGroup')
            ->andWhereIn('sfGuardUserGroup.group_id', $values);
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
            ->leftJoin($query->getRootAlias().'.sfGuardUserPermission sfGuardUserPermission')
            ->andWhereIn('sfGuardUserPermission.permission_id', $values);
    }

    public function getModelName()
    {
        return 'sfGuardUser';
    }

    public function getFields()
    {
        return [
            'id' => 'Number',
            'first_name' => 'Text',
            'last_name' => 'Text',
            'email_address' => 'Text',
            'username' => 'Text',
            'algorithm' => 'Text',
            'salt' => 'Text',
            'password' => 'Text',
            'is_active' => 'Boolean',
            'is_super_admin' => 'Boolean',
            'last_login' => 'Date',
            'created_at' => 'Date',
            'updated_at' => 'Date',
            'groups_list' => 'ManyKey',
            'permissions_list' => 'ManyKey',
        ];
    }
}

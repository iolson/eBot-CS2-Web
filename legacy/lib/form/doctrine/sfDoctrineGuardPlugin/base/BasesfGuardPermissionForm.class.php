<?php

/**
 * sfGuardPermission form base class.
 *
 * @method sfGuardPermission getObject() Returns the current form's model object
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasesfGuardPermissionForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id' => new sfWidgetFormInputHidden,
            'name' => new sfWidgetFormInputText,
            'description' => new sfWidgetFormTextarea,
            'created_at' => new sfWidgetFormDateTime,
            'updated_at' => new sfWidgetFormDateTime,
            'groups_list' => new sfWidgetFormDoctrineChoice(['multiple' => true, 'model' => 'sfGuardGroup']),
            'users_list' => new sfWidgetFormDoctrineChoice(['multiple' => true, 'model' => 'sfGuardUser']),
        ]);

        $this->setValidators([
            'id' => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'name' => new sfValidatorString(['max_length' => 255, 'required' => false]),
            'description' => new sfValidatorString(['max_length' => 1000, 'required' => false]),
            'created_at' => new sfValidatorDateTime,
            'updated_at' => new sfValidatorDateTime,
            'groups_list' => new sfValidatorDoctrineChoice(['multiple' => true, 'model' => 'sfGuardGroup', 'required' => false]),
            'users_list' => new sfValidatorDoctrineChoice(['multiple' => true, 'model' => 'sfGuardUser', 'required' => false]),
        ]);

        $this->validatorSchema->setPostValidator(
            new sfValidatorDoctrineUnique(['model' => 'sfGuardPermission', 'column' => ['name']])
        );

        $this->widgetSchema->setNameFormat('sf_guard_permission[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'sfGuardPermission';
    }

    public function updateDefaultsFromObject()
    {
        parent::updateDefaultsFromObject();

        if (isset($this->widgetSchema['groups_list'])) {
            $this->setDefault('groups_list', $this->object->Groups->getPrimaryKeys());
        }

        if (isset($this->widgetSchema['users_list'])) {
            $this->setDefault('users_list', $this->object->Users->getPrimaryKeys());
        }

    }

    protected function doSave($con = null)
    {
        $this->saveGroupsList($con);
        $this->saveUsersList($con);

        parent::doSave($con);
    }

    public function saveGroupsList($con = null)
    {
        if (! $this->isValid()) {
            throw $this->getErrorSchema();
        }

        if (! isset($this->widgetSchema['groups_list'])) {
            // somebody has unset this widget
            return;
        }

        if ($con === null) {
            $con = $this->getConnection();
        }

        $existing = $this->object->Groups->getPrimaryKeys();
        $values = $this->getValue('groups_list');
        if (! is_array($values)) {
            $values = [];
        }

        $unlink = array_diff($existing, $values);
        if (count($unlink)) {
            $this->object->unlink('Groups', array_values($unlink));
        }

        $link = array_diff($values, $existing);
        if (count($link)) {
            $this->object->link('Groups', array_values($link));
        }
    }

    public function saveUsersList($con = null)
    {
        if (! $this->isValid()) {
            throw $this->getErrorSchema();
        }

        if (! isset($this->widgetSchema['users_list'])) {
            // somebody has unset this widget
            return;
        }

        if ($con === null) {
            $con = $this->getConnection();
        }

        $existing = $this->object->Users->getPrimaryKeys();
        $values = $this->getValue('users_list');
        if (! is_array($values)) {
            $values = [];
        }

        $unlink = array_diff($existing, $values);
        if (count($unlink)) {
            $this->object->unlink('Users', array_values($unlink));
        }

        $link = array_diff($values, $existing);
        if (count($link)) {
            $this->object->link('Users', array_values($link));
        }
    }
}

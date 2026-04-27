<?php

/**
 * ServerMessages form base class.
 *
 * @method ServerMessages getObject() Returns the current form's model object
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseServerMessagesForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id' => new sfWidgetFormInputHidden,
            'season_id' => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Season'), 'add_empty' => true]),
            'message' => new sfWidgetFormInputText,
            'active' => new sfWidgetFormInputCheckbox,
            'created_at' => new sfWidgetFormDateTime,
            'updated_at' => new sfWidgetFormDateTime,
        ]);

        $this->setValidators([
            'id' => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'season_id' => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Season'), 'required' => false]),
            'message' => new sfValidatorPass(['required' => false]),
            'active' => new sfValidatorBoolean(['required' => false]),
            'created_at' => new sfValidatorDateTime,
            'updated_at' => new sfValidatorDateTime,
        ]);

        $this->widgetSchema->setNameFormat('server_messages[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'ServerMessages';
    }
}

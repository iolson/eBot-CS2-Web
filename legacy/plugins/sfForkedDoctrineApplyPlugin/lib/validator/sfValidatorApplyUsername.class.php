<?php

class sfValidatorApplyUsername extends sfValidatorAnd
{
    public function __construct()
    {
        parent::__construct();
        $this->setValidators();

    }

    public function setValidators()
    {
        // Setting string validator first.
        // It should be required, got trimmed, and min and max length set.
        $this->addValidator(
            new sfValidatorString(
                [
                    'required' => true,
                    'trim' => true,
                    'min_length' => 4,
                    'max_length' => 16,
                ]
            )
        );

        // Usernames should be safe to output without escaping and generally username-like.
        $this->addValidator(
            new sfValidatorRegex(
                ['pattern' => '/^\w+$/'],
                ['invalid' => 'Usernames must contain only letters, numbers and underscores.']
            )
        );

        // Checking for existance of given username in database
        $this->addValidator(
            new sfValidatorDoctrineUnique(
                ['model' => 'sfGuardUser', 'column' => 'username'],
                ['invalid' => 'There is already a user by that name. Choose another.']
            )
        );
    }
}

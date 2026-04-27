<?php

/**
 * Teams form.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TeamsForm extends BaseTeamsForm
{
    public function configure()
    {
        $this->useFields(['id', 'name', 'shorthandle', 'flag', 'link']);

        $flags = sfConfig::get('app_flag_team'.((sfContext::getInstance()->getUser()->getCulture() != 'fr') ? '_en' : ''));
        $aFlags = [];
        $aFlags[''] = '';
        foreach ($flags as $k => $flag) {
            $aFlags[$k] = $flag;
        }
        $this->widgetSchema['flag'] = new sfWidgetFormSelect(['choices' => $aFlags]);
    }
}

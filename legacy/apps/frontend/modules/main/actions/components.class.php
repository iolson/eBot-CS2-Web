<?php

class mainComponents extends sfComponents
{
    public function executeMenu()
    {
        $this->nbMatchs = MatchsTable::getInstance()->createQuery()->andWhere('status >= ? AND status <= ?', [Matchs::STATUS_NOT_STARTED, Matchs::STATUS_END_MATCH])->andWhere('enable = ?', 1)->count();
    }
}

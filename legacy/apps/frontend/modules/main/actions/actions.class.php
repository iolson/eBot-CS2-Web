<?php

/**
 * main actions.
 *
 * @author     Your name here
 *
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class mainActions extends sfActions
{
    /**
     * Executes index action
     *
     * @param  sfRequest  $request  A request object
     */
    public function executeIndex(sfWebRequest $request)
    {
        $this->filter = new MatchsActiveFormFilter($this->getFilters());
        $query = $this->filter->buildQuery($this->getFilters());
        $this->filterValues = $this->getFilters();

        $this->matchs = $query->andWhere('status >= ?', [Matchs::STATUS_NOT_STARTED])->andWhere('status <= ?', [Matchs::STATUS_END_MATCH])->orderBy('status DESC')->limit(10)->execute();
    }

    public function executeIngame(sfWebRequest $request) {}

    public function executeFilters(sfWebRequest $request)
    {
        $this->filter = new MatchsFormFilter;
        $this->filter->bind($request->getPostParameter($this->filter->getName()));
        if ($this->filter->isValid()) {
            $this->setFilters($this->filter->getValues());
        }

        $this->redirect($request->getReferer());
    }

    public function executeFiltersClear(sfWebRequest $request)
    {
        $this->setFilters([]);
        $this->redirect($request->getReferer());
    }

    private function getFilters()
    {
        return $this->getUser()->getAttribute('matchs.filters', [], 'admin_module');
    }

    private function setFilters($filters)
    {
        return $this->getUser()->setAttribute('matchs.filters', $filters, 'admin_module');
    }
}

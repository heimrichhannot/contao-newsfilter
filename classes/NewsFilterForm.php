<?php

namespace HeimrichHannot\NewsFilter;


class NewsFilterForm extends \HeimrichHannot\FormHybrid\Form
{
    protected $strTable = 'tl_news';

    protected $strTemplate = 'formhybrid_newsfilter';

    public function __construct($objModule)
    {
        $this->strMethod = FORMHYBRID_METHOD_GET;
        $this->isFilterForm = true;
        parent::__construct($objModule);
    }

    protected function onSubmitCallback(\DataContainer $dc){}

    protected function compile(){}
}
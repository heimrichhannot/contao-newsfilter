<?php

namespace HeimrichHannot\NewsFilter;

class ModuleNewsFilter extends \ModuleNewsList
{

    protected $strTemplate = 'mod_newsfilter';

    protected $objForm;

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['newsfilter'][0]) . ' ###';
    		$objTemplate->title = $this->headline;
    		$objTemplate->id = $this->id;
    		$objTemplate->link = $this->name;
    		$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

    		return $objTemplate->parse();
    	}

        return parent::generate();
    }

    protected function compile()
    {
        $this->objForm = new NewsFilterForm($this->objModel);
        $this->Template->form = $this->objForm->generate();

        $this->getArticles();
    }

    protected function getArticles()
    {
        $offset = intval($this->skipFirst);
        $limit = null;

        // Maximum number of items
        if ($this->numberOfItems > 0)
        {
            $limit = $this->numberOfItems;
        }

        // Handle featured news
        if ($this->news_featured == 'featured')
        {
            $blnFeatured = true;
        }
        elseif ($this->news_featured == 'unfeatured')
        {
            $blnFeatured = false;
        }
        else
        {
            $blnFeatured = null;
        }

        $this->Template->articles = array();
        $this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyList'];

        $objSubmission = $this->objForm->getSubmission(false);

        if($objSubmission === null)
        {
            $objSubmission = new \HeimrichHannot\FormHybrid\Submission();
        }
        
        $objSubmission->search = trim($objSubmission->search);
        $objSubmission->search = preg_replace('/\{\{[^\}]*\}\}/', '', $objSubmission->search);

        if($objSubmission->from)
        {
            $from = strtotime($objSubmission->from);
            $objSubmission->from = mktime(0,0,0, date('m', $from), date('d', $from), date('Y', $from));
        }

        if($objSubmission->to)
        {
            $to = strtotime($objSubmission->to);
            $objSubmission->to = mktime(23,59,59, date('m', $to), date('d', $to), date('Y', $to));
        }
    
        $arrAliases = array();

        if($objSubmission->search != '')
        {
            $objSearch = \Search::searchFor($objSubmission->search);
            $arrResults = $objSearch->fetchAllAssoc();

            if(!empty($arrResults))
            {
                foreach($arrResults as $arrResult)
                {
                    $arrAliases[] = str_replace(\Config::get('urlSuffix'), '', basename($arrResult['url']));
                }
            }
        }


        // Get the total number of items
        $intTotal = NewsFilterModel::countPublishedByPidsAndSearch($this->news_archives, $objSubmission, $arrAliases, $blnFeatured);

        if ($intTotal < 1)
        {
            return;
        }

        $total = $intTotal - $offset;

        // Split the results
        if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage))
        {
            // Adjust the overall limit
            if (isset($limit))
            {
                $total = min($limit, $total);
            }

            // Get the current page
            $id = 'page_n' . $this->id;
            $page = \Input::get($id) ?: 1;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
            {
                global $objPage;
                $objPage->noSearch = 1;
                $objPage->cache = 0;

                // Send a 404 header
                header('HTTP/1.1 404 Not Found');
                return;
            }

            // Set limit and offset
            $limit = $this->perPage;
            $offset += (max($page, 1) - 1) * $this->perPage;
            $skip = intval($this->skipFirst);

            // Overall limit
            if ($offset + $limit > $total + $skip)
            {
                $limit = $total + $skip - $offset;
            }

            // Add the pagination menu
            $objPagination = new \Pagination($total, $this->perPage, \Config::get('maxPaginationLinks'), $id);
            $this->Template->pagination = $objPagination->generate("\n  ");
        }

        // Get the items
        if (isset($limit))
        {
            $objArticles = NewsFilterModel::findPublishedByPidsAndSearch($this->news_archives, $objSubmission, $arrAliases, $blnFeatured, $limit, $offset);
        }
        else
        {
            $objArticles = NewsFilterModel::findPublishedByPidsAndSearch($this->news_archives, $objSubmission, $arrAliases, $blnFeatured, 0, $offset);
        }

        // Add the articles
        if ($objArticles !== null)
        {
            $this->Template->articles = $this->parseArticles($objArticles);
        }

        $this->Template->archives = $this->news_archives;
    }

}
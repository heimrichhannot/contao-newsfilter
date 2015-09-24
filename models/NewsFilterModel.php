<?php

namespace HeimrichHannot\NewsFilter;

class NewsFilterModel extends \NewsModel
{

    /**
     * Count published news items by their parent ID
     *
     * @param array   $arrPids     An array of news archive IDs
     * @param boolean $blnFeatured If true, return only featured news, if false, return only unfeatured news
     * @param array   $arrOptions  An optional options array
     *
     * @return integer The number of news items
     */
    public static function countPublishedByPidsAndSearch($arrPids, $objSubmission, $arrSearchAliases=array(), $blnFeatured=null, array $arrOptions=array())
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return 0;
        }

        // search term provided, but nothing found within tl_search_index
        if($objSubmission->search && empty($arrSearchAliases))
        {
            return 0;
        }

        $t = static::$strTable;
        $arrColumns = array("$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")");

        if ($blnFeatured === true)
        {
            $arrColumns[] = "$t.featured=1";
        }
        elseif ($blnFeatured === false)
        {
            $arrColumns[] = "$t.featured=''";
        }

        if (!BE_USER_LOGGED_IN)
        {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }


        $objSubmission->from = empty($objSubmission->from) ? "NULL" : $objSubmission->from;
        $objSubmission->to = empty($objSubmission->to) ? "NULL" : $objSubmission->to;

        $arrColumns[] = "($objSubmission->from IS NULL OR $t.time>=$objSubmission->from) AND ($objSubmission->to IS NULL OR $t.time<=$objSubmission->to)";

        if(!empty($arrSearchAliases))
        {
            $arrColumns[] = "($t.alias IN('" . implode("','", $arrSearchAliases) . "'))";
        }

        return static::countBy($arrColumns, null, $arrOptions);
    }

    /**
     * Find published news items by their parent ID
     *
     * @param array   $arrPids     An array of news archive IDs
     * @param boolean $blnFeatured If true, return only featured news, if false, return only unfeatured news
     * @param integer $intLimit    An optional limit
     * @param integer $intOffset   An optional offset
     * @param array   $arrOptions  An optional options array
     *
     * @return \Model\Collection|null A collection of models or null if there are no news
     */
    public static function findPublishedByPidsAndSearch($arrPids, $objSubmission, $arrSearchAliases=array(), $blnFeatured=null, $intLimit=0, $intOffset=0, array $arrOptions=array())
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        // search term provided, but nothing found within tl_search_index
        if($objSubmission->search && empty($arrSearchAliases))
        {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = array("$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")");

        if ($blnFeatured === true)
        {
            $arrColumns[] = "$t.featured=1";
        }
        elseif ($blnFeatured === false)
        {
            $arrColumns[] = "$t.featured=''";
        }

        // Never return unpublished elements in the back end, so they don't end up in the RSS feed
        if (!BE_USER_LOGGED_IN || TL_MODE == 'BE')
        {
            $time = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        $objSubmission->from = empty($objSubmission->from) ? "NULL" : $objSubmission->from;
        $objSubmission->to = empty($objSubmission->to) ? "NULL" : $objSubmission->to;

        $arrColumns[] = "($objSubmission->from IS NULL OR $t.time>=$objSubmission->from) AND ($objSubmission->to IS NULL OR $t.time<=$objSubmission->to)";

        if(!empty($arrSearchAliases))
        {
            $arrColumns[] = "($t.alias IN('" . implode("','", $arrSearchAliases) . "'))";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order']  = "$t.date DESC";
        }

        $arrOptions['limit']  = $intLimit;
        $arrOptions['offset'] = $intOffset;

        return static::findBy($arrColumns, null, $arrOptions);
    }
}
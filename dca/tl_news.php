<?php

$dc = &$GLOBALS['TL_DCA']['tl_news'];


$dc['palettes']['filter'] = '{search_legend},search;{date_legend},from,to,submitFilter';

$arrFields = array
(
    'search'    => array
    (
        'label'                     => &$GLOBALS['TL_LANG']['tl_news']['search'],
        'inputType'                 => 'text',
    ),
    'from'  => array
    (
        'label'                     => &$GLOBALS['TL_LANG']['tl_news']['from'],
        'inputType'                 => 'text',
        'eval'                      => array
        (
            'rgxp'=>'date',
            'datepicker'=>true,
            'placeholder' => \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], mktime(0,0,0,1,1, date('Y', time()) - 1)),
            'groupClass' => 'col-xs-12 col-sm-6 col-md-6 col-lg-6'
        ),
    ),
    'to'    => array
    (
        'label'                     => &$GLOBALS['TL_LANG']['tl_news']['to'],
        'inputType'                 => 'text',
        'eval'                      => array
        (
            'rgxp'=>'date',
            'datepicker'=>true,
            'placeholder' => \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], time()),
            'groupClass' => 'col-xs-12 col-sm-6 col-md-6 col-lg-6'
        ),
    ),
    'submitFilter'    => array
    (
        'label'                     => &$GLOBALS['TL_LANG']['tl_news']['submitFilter'],
        'inputType'                 => 'submit',
        'eval'                      => array('class' => 'btn btn-primary')
    )
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);
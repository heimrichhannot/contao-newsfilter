<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Newsfilter
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Models
	'HeimrichHannot\NewsFilter\NewsFilterModel'  => 'system/modules/newsfilter/models/NewsFilterModel.php',

	// Modules
	'HeimrichHannot\NewsFilter\ModuleNewsFilter' => 'system/modules/newsfilter/modules/ModuleNewsFilter.php',

	// Classes
	'HeimrichHannot\NewsFilter\NewsFilterForm'   => 'system/modules/newsfilter/classes/NewsFilterForm.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_newsfilter'        => 'system/modules/newsfilter/templates/modules',
	'formhybrid_newsfilter' => 'system/modules/newsfilter/templates/form',
));

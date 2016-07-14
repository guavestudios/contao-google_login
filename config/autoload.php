<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

//NamespaceClassLoader::add('Guave', 'system/modules/google_login/library/');

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Guave\GoogleLogin\Oauth'      => 'system/modules/google_login/classes/Oauth.php',

));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_login'        => 'system/modules/google_login/templates',
));

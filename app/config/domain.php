<?php

/**
 * Default domains configuration
 * 



 * Content:
 * 	- default domain configuration
 *  - basic components configuration
 *  - site and admin configuration (this configuration is not cached and is used in all domains)
 *  
 *  !!! Warning !!!
 *  This domain configuration is used only in first App run. After this is saved to domain_configs table and is cached in /app/tmp/persistent/domain.default.php.
 *  If you need some changes make it from administration system settings for current domain. 
 *  If you need to reset configuration remove /app/tmp/persistent/domain.default.php and records for default domain in domain_configs table.
 *  
 *  !!! Reset domain configuration data on production server can affect system stability.
 *  
 *  @author Martin Bucko  (bucko at treecom dot net)
 *  @copyright Copyright 2012 (c) Treecom s.r.o.
 */


/**
 * Default domain name
 * Value later changed automatic
 * Don't change it!
 * @var string
 */
Configure::write('Domain.name', 'default');

/**
 * Domain default language
 * This value change only on installation of app. Posible to use cake DEFAULT_LANGUAGE constant.
 * Don't change this value in production server!
 * Code list: http://www.w3.org/WAI/ER/IG/ert/iso639.htm
 * @var string
 */
Configure::write('Domain.language', 'eng');

/**
 * Domain available languages
 * For add languages need to create localization files in app/localization
 * Don't remove values in production server!
 * Code list: /cake/libs/l10n.php @ $__l10nCatalog
 * @var array
 */
Configure::write('Domain.availableLanguages', array('eng','sk'));

/**
 * Default theme for domain
 * null = no-theme or system default
 * @var string
 */
Configure::write('Domain.theme', null);

/**
 * Default layout for domain
 * @var string
 */
Configure::write('Domain.layout', 'default');

/**
 * Default view tempate
 * @var string
 */
Configure::write('Domain.view', 'default');

/**
 * Default view class
 * @var string
 */
Configure::write('Domain.viewClass', null);


/**
 * Domain components to load in context controller
 * @var array
 */
Configure::write('Domain.components', null);

/**
 * Domain models to load in context controller
 * @var array
 */
Configure::write('Domain.models', null);

/**
 * Domain helpers to load in context controller
 * @var array
 */
Configure::write('Domain.helpers', null);

/**
 * Domain context controller properties
 * be careful for if overriding controller properties!
 * @var array 
 */
Configure::write('Domain.properties', null);
 
/**
 * Domain context defaultTitleBefore 
 * @var string
 */
Configure::write('Domain.defaultTitleBefore', '');

/**
 * Domain context defaultTitleAfter 
 * @var string
 */
Configure::write('Domain.defaultTitleAfter', '');
 

/**
 * Domain default logo
 * @var string
 */
Configure::write('Domain.logoPath', '/img/logo.png');

/**
 * Domain default logo alternative text 
 * @var string
 */
Configure::write('Domain.logoAlt', 'Screenbox Server');

/**
 * Domain default logo alternative text 
 * @var string
 */
Configure::write('Domain.logoLink', '/');

/**
 * Domain footer copyright content 
 * @var string
 */
Configure::write('Domain.copyright', 'Copyright (c) 2012 Screenbox.org');

/**
 * Domain administrator email.
 * Used for admin massages to users. 
 * @var string
 */
Configure::write('Domain.adminEmail', 'support@treecom.net');

/**
 * Domain server email. 
 * Used for automatic ganarated emails. (example: noreply@treecom.net)
 * @var string
 */
Configure::write('Domain.serverEmail', (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'].' ' : '') .'<noreply@'.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').'>');

/**
 * Domain administrator email 
 * @var string
 */
Configure::write('Domain.supportEmail', 'support@treecom.net');

/**
 * Domain smtp options for sending mails
 * @var string
 */
Configure::write('Domain.smtpOptions', array(
		        	'port'=>'25', 
		        	'timeout'=>'30',
		        	'host' => 'localhost',
		        	'username'=>null,
		        	'password'=>null		        
		   		));
				

/**
 * Domain images thumbnail sizes for resizing after upload.
 * Default named sizes is icon,small and big. This sizes is required. Others can be added and used in views. 
 * Use preferred sizes defined in 960 css grid. Width and height are defined maximum of image size.
 * Warning!: More defined size need more disk space for resized images copy. 
 * @var array
 */
Configure::write('Domain.thumbSizes', array(
		        	'icon' => '64x64',
                	'small' => '100x100',
					'big' => '640x480'	        
		   		));				
				

/**
 * Domain files groups
 * @var array
 */
Configure::write('Domain.filesGroups', array(
					'image' => 'jpg,jpe,jpeg,gif,bmp,png',
					'video' => 'avi,mpg,mpe,mpeg,mp4,mov,flv,qt,ram,ra,rm,mkv',
					'audio' => 'au,wav,mp2,mp3,mpga,ogg,aac,mid,midi',
					'archive' => 'zip,rar,7z,bz2,tar,gtar,gzip',
					'text' => 'txt,js,css,html,htm,xml,ini,ctp,tpl,rtf',
					'document' => 'pdf,doc,docx,xls,xlsx,xlm,ppt,rtf',
					'flash' => 'swf'
				));		



/**
 * Domain content add to layout html
 * @var string  
 */
Configure::write('Domain.metaForLayout','');
Configure::write('Domain.cssForLayout','');
Configure::write('Domain.headerContent','');
Configure::write('Domain.footerContent','');

/********************************* 
 *  ADMIN 
 *  - values are not saved to database and are not cached with Domain values
 */

/**
 * Admin UI default language
 * @var string
 */ 
Configure::write('Admin.language', 'eng');

/**
 * Admin default edited language. It can be other language than default admin UI language.
 * @var string
 */
Configure::write('Admin.languageEdit', 'eng');

/**
 * Admin available languages
 * @var array
 */
Configure::write('Admin.availableLanguages', array('eng', 'sk'));
 
/**
 * Missing config variable error fix. 
 * @var array
 */
$config = array();



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
 *  @version 1.0.2
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
Configure::write('Domain.language', 'sk');

/**
 * Domain available languages
 * For add languages need to create localization files in app/localization
 * Don't remove values in production server!
 * Code list: /cake/libs/l10n.php @ $__l10nCatalog
 * @var array
 */
Configure::write('Domain.availableLanguages', array('sk','eng'));

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
 * Default stackMode in view
 * Value dependent on Domain.view 
 * @var boolean
 */
Configure::write('Domain.stackMode', true);

/**
 * Default stacks count in view
 * Value dependent on Domain.view and Domain.stackMode
 * @var int
 */
Configure::write('Domain.stackPlaces', 1);

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
 * Domain id
 * Value set Domain Model id for default domain.
 * @var int
 */
Configure::write('Domain.domainId', 1);

/**
 * Domain context
 * Value set domain Context Model id for default domain, represent the main root of context tree.
 * null value mean auto-find first context 
 * @var integer
 */
Configure::write('Domain.domainContextId', 1);

/**
 * Domain first context id
 * Value set first visible Context Model id in domain root or title page.
 * zero value mean auto-find first context 
 * @var integer 
 */
Configure::write('Domain.firstContextId', 0);

/**
 * Domain top menu options
 * See Navigation helper for options description
 * @var array
 */
Configure::write('Domain.menuOptionsTop', array(	
					'Model'=>'Context', 
					'title'=>'name', 
					'link'=>'id,full_path', 
					'url'=> '{full_path}',
					'prefix' => '/{lang}',
					'qmenu'=>true,  
					'divider' => '<span class="qmdivider qmdividery"> </span>',
					'divider-after'=> true 
));

/**
 * Domain bottom menu options
 * See Navigation helper for options description
 * @var array
 */
Configure::write('Domain.menuOptionsBottom', array(	
					'Model'=>'Context', 
					'title'=>'name', 
					'link'=>'id,full_path', 
					'url'=> '{full_path}',
					'prefix' => '/{lang}',
					'qmenu'=>false,  
					'ulClass'=>'botom_menu_ul'
));

/**
 * Domain default logo
 * @var string
 */
Configure::write('Domain.logoPath', '/img/basic_template/logo.png');

/**
 * Domain default logo alternative text 
 * @var string
 */
Configure::write('Domain.logoAlt', 'Company logo');

/**
 * Domain default logo alternative text 
 * @var string
 */
Configure::write('Domain.logoLink', '/');

/**
 * Domain footer copyright content 
 * @var string
 */
Configure::write('Domain.copyright', '(c) Domain.com %s - Powered by UpDate CMS');

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
Configure::write('Admin.language', 'sk');

/**
 * Admin default edited language. It can be other language than default admin UI language.
 * @var string
 */
Configure::write('Admin.languageEdit', 'sk');

/**
 * Admin available languages
 * @var array
 */
Configure::write('Admin.availableLanguages', array('sk','eng'));

/**
 * Admin loadComponents
 * Load this components in admin view as JS. Order is important!
 * Example: array('config','context','users','files','media','page','news','newsletter','payment','coupon','example')
 * @var array
 */
Configure::write('Admin.loadComponents', array('config','context','users','files','media','page','news','newsletter','mediaserver'));

  
/**
 * Missing config variable error fix. 
 * @var array
 */
$config = array();



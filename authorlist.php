<?php
/**
 * @package    Joomla.Site
 * @subpackage plg_content_authorlist
 * @author     Jesus Vargas Garita
 * @copyright  Copyright (C) 2018 Jesus Vargas Garita
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

if (is_file(JPATH_SITE.'/components/com_authorlist/helpers/route.php')) :
	require_once JPATH_SITE.'/components/com_authorlist/helpers/route.php';
	require_once __DIR__ . '/authorlist_class.php';
else :
	JFactory::getApplication()->enqueueMessage('Author List Component not found. <br />Please install com_authorlist or disable this this plugin: Content - Author List.', 'notice');
endif;

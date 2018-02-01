<?php
/**
 * @package    Joomla.Site
 * @subpackage plg_content_authorlist
 * @author     Jesus Vargas Garita
 * @copyright  Copyright (C) 2018 Jesus Vargas Garita
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.application.component.modellist');

JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_authorlist/models', 'AuthorListModel');

class plgContentAuthorList extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();

    	$this->aParams = JComponentHelper::getParams('com_authorlist', true);
	}

	public function onContentBeforeDisplay($context, &$row, &$params, $page=0)
	{
		if (is_object($params) && !empty($params) && $params->get('show_author') && $this->params->get('display') != 2) :

			if (!empty($row->created_by_alias) && $this->params->get('alias') == 0) return;

			if (!empty($row->contactid) && $this->params->get('display') == 1) return;

			if (!isset($row->created_by) || intval($row->created_by) <= 0) return;

			$author = new stdClass();
			$user = JUser::getInstance($row->created_by);
			$db	  = JFactory::getDbo();
			$db->setQuery('SELECT * FROM #__authorlist WHERE userid=' . $row->created_by);

			if ($author = $db->loadObject()) :

				if(property_exists('author', 'display_alias') && $author->display_alias) {
					$author->displayName = $author->display_alias;
				} elseif ($this->aParams->get('show_author_name') == 1) {
					$author->displayName = $user->username;
				} else {
					$author->displayName = $user->name;
				}

				if ( $author->displayName && $author->state == 1 ) :

					$link = JRoute::_(AuthorListHelperRoute::getAuthorRoute(($author->alias?$author->id.':'.$author->alias:$author->id)));
					if ($this->params->get('alias') == 1)
						$name = $row->created_by_alias?$row->created_by_alias:$author->displayName;
					else
						$name = $author->displayName;

					$row->author = JHtml::_('link',$link,$name, array('title' => sprintf(JText::_('PLG_CONTENT_AUTHORLIST_TITLE_VIEW_AUTHOR'), $name)));

					if ($this->params->get('meta_author', 1) == '1')
					{
						JFactory::getDocument()->setMetaData('author', $name);
					}

					$row->created_by_alias = '';
					$row->contactid = '';

				endif;

			endif;

		endif;

		return $this->checkOptions($context, $row, $params, $page, $this->params->get('show_block'), $this->params->get('show_block2'), 2);
	}

	public function onContentAfterDisplay($context, &$row, &$params, $page=0)
	{
		return $this->checkOptions($context, $row, $params, $page, $this->params->get('show_block'), $this->params->get('show_block2'), 1);
	}

	public function onContentAfterTitle($context, &$row, &$params, $page=0)
	{
		return $this->checkOptions($context, $row, $params, $page, $this->params->get('show_block'), $this->params->get('show_block2'), 3);
	}

	public function checkOptions($context, &$row, &$params, $page=0, $block1, $block2, $event)
	{
		if (JRequest::getCmd('view') != 'article') return;

		if ($block1==$event || $block2==$event) :

      		$html = '';

			if ($block1==$event) :

				$html .= $this->getBlock1($context, $row, $params, $page);

      		endif;

			if ($block2==$event) :

				$html .= $this->getBlock2($context, $row, $params, $page);

			endif;

			return $html;

		endif;
	}

	public function getBlock1($context, &$row, &$params, $page=0)
	{

		$author = $this->getAuthor($row->created_by);
		if(!$author) return;

		$app = JFactory::getApplication();
		$template = $app->getTemplate();

		$show_name  = $this->params->get('show_name');
		$show_image = $this->params->get('show_image');
		$title = $link = $image = $desc = $gplus = $clr = '';

		if((!$name = $author->displayName) || $author->state != 1) return;

		if($show_name == 1 || $show_image == 1) :
			if ($author->display_alias) {
				$slug = $author->id.':'.JApplication::stringURLSafe($author->display_alias);
			} else {
				$slug = ($author->alias?$author->id.':'.$author->alias:$author->id);
			}
			$link = JRoute::_(AuthorListHelperRoute::getAuthorRoute($slug));
		endif;

		if ($this->params->get('show_desc')) :
			$desc = $author->description;
			$limit = $this->params->get('limit_desc');
			if ($limit && is_numeric($limit)) :
				$desc = strip_tags($desc);
				if (strlen($desc)>$limit) :
					$desc = substr($desc, 0, $limit) . '...';
				endif;
			endif;
		endif;

		$tmpl = $this->getTmpl($template,'default');

		ob_start();
		require $tmpl;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function getBlock2($context, &$row, &$params, $page=0)
	{
		$app = JFactory::getApplication();
		$template = $app->getTemplate();

		$items = $this->getArticles($row->created_by, $row->id);

		$tmpl = $this->getTmpl($template,'default_links');

		ob_start();
		require $tmpl;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function getTmpl($template,$name) {

		$search = JPATH_SITE . '/templates/'.$template.'/html/plg_authorlist_content/'.$name.'.php';

		if (is_file($search)) {
			$tmpl = $search;
		} else {
			$tmpl = JPATH_SITE . '/plugins/content/authorlist/tmpl/'.$name.'.php';
		}

		return $tmpl;
	}

	public function getAuthor($id) {

		$model = JModelLegacy::getInstance('Author', 'AuthorListModel', array('ignore_request' => true));
		$model->setState('user.id', $id);
		$model->setState('params', $this->aParams);

    	$author = $model->getAuthor();

		return $author;
	}

	public function getArticles($author_id, $item_id) {

  		$model = JModelLegacy::getInstance('Articles', 'AuthorListModel', array('ignore_request' => true));
		$model->setState('filter.published', 1);
  		$model->setState('filter.author_id', $author_id);
  		$model->setState('params', $this->aParams);
		$model->setState('list.limit', $this->params->get('limit_items'));
		$model->setState('query.where', 'a.id!='.$item_id);

		$queryDate = $this->params->get('order_date','published');
		switch ($this->params->get('orderby','rdate'))
		{
			case 'date' :
				$orderby  = $queryDate;
				$orderdir = '';
				break;

			case 'rdate' :
				$orderby  = $queryDate;
				$orderdir = 'DESC';
				break;

			case 'alpha' :
				$orderby  = 'a.title';
				$orderdir = '';
				break;

			case 'ralpha' :
				$orderby  = 'a.title';
				$orderdir = 'DESC';
				break;

			case 'hits' :
				$orderby = 'a.hits';
				$orderdir = 'DESC';
				break;

			case 'rhits' :
				$orderby = 'a.hits';
				$orderdir = '';
				break;

			case 'order' :
				$orderby = 'a.ordering';
				$orderdir = '';
				break;

			case 'author' :
				$orderby = 'author';
				$orderdir = '';
				break;

			case 'rauthor' :
				$orderby = 'author';
				$orderdir = 'DESC';
				break;

			case 'front' :
				$orderby = 'fp.ordering';
				$orderdir = '';
				break;

			case 'rand' :
				$orderby = 'RAND()';
				$orderdir = '';
				break;

			default :
				$orderby = 'a.ordering';
				$orderdir = '';
				break;
		}
		$model->setState('list.ordering', $orderby);
		$model->setState('list.direction', $orderdir);

		$catids = $this->params->get('catid');
		$model->setState('filter.category_id.include', (bool) $this->params->get('category_filtering_type', 1));

		if ($catids)
		{
			$model->setState('filter.category_id', $catids);
		}

      	return $model->getItems();
  	}
}

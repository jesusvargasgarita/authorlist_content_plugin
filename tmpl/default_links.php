<?php
/**
 * @package    Joomla.Site
 * @subpackage plg_content_authorlist
 * @author     Jesus Vargas Garita
 * @copyright  Copyright (C) 2018 Jesus Vargas Garita
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

if( count($items) ) :
?>
<div class="plg_al_more" style="clear:both;">
  <h3><?php echo JText::_('PLG_CONTENT_AUTHORLIST_TITLE_MORE_ARTICLES'); ?></h3>
  <ul>
    <?php foreach ($items as $item) : ?>
      <li><?php if($item->id != $row->id) : ?><a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid)); ?>"><?php endif; ?><?php echo $item->title; ?><?php if($item->id != $row->id) : ?></a><?php endif; ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
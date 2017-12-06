<?php
/**
 * @package    Joomla.Site
 * @subpackage plg_content_authorlist
 * @author     Jesus Vargas Garita
 * @copyright  Copyright (C) 2018 Jesus Vargas Garita
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;
?>
<div class="plg_al_about" style="clear:both;">
	<?php if($this->params->get('show_title')) : ?><h3><?php echo JText::_('PLG_CONTENT_AUTHORLIST_TITLE_ABOUT_AUTHOR'); ?></h3><?php endif; ?>
	<?php if($show_image && $author->thumb) : ?>
		<?php if($show_image==1) : ?><a href="<?php echo $link; ?>" title="<?php echo sprintf(JText::_('PLG_CONTENT_AUTHORLIST_TITLE_VIEW_AUTHOR'), $name); ?>"><?php endif; ?>
			<img class="authorlist_thumb" src="<?php echo $author->thumb; ?>" alt="<?php echo $name; ?>" style="float:left;margin-right:10px;" />
		<?php if($show_image==1) : ?></a><?php endif; ?>
	<?php endif; ?>
	<h4>
		<?php if($show_name==1) : ?><a href="<?php echo $link; ?>" title="<?php echo sprintf(JText::_('PLG_CONTENT_AUTHORLIST_TITLE_VIEW_AUTHOR'), $name); ?>"><?php endif; ?>
			<?php echo $name; ?>
		<?php if($show_name==1) : ?></a><?php endif; ?>
		<?php if ($author->gplus_url) : ?>
			<a href="<?php echo $author->gplus_url; ?>?rel=author" target="_blank" title="<?php echo sprintf(JText::_('PLG_CONTENT_AUTHORLIST_AUTHOR_GOOGLE_PLUS'),$name); ?>" style="display:inline-block;"><img src="<?php echo $author->gplus_icon; ?>" alt="Google Plus" style="margin-left:5px;vertical-align:text-top;" /></a>
		<?php endif; ?>
	</h4>
	<?php if($this->params->get('show_desc')) : ?>
		<div class="plg_al_desc"><?php echo $desc; ?></div>
	<?php endif; ?>
	<?php if($show_image) : ?><div style="clear:both;"></div><?php endif; ?>
</div>
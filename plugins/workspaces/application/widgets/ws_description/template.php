<?php  /* @var $workspace Workspace */ 
	if ( $workspace->getColumnValue('show_description_in_overview') ) : ?> 
<div class="widget-persons widget">

	<div class="widget-header" onclick="og.dashExpand('<?php echo $genid?>');">
		<?php echo (isset($widget_title)) ? $widget_title : lang("workspace description");?>
		<div class="dash-expander ico-dash-expanded" id="<?php echo $genid; ?>expander"></div>
	</div>
	
	<div class="widget-body" id="<?php echo $genid; ?>_widget_body">
		<?php echo escape_html_whitespace(convert_to_links(clean($workspace->getDescription()))); ?>
	</div>
</div>
<?php  endif ;?>
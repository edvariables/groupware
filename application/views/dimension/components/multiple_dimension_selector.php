<?php $hidden_field_name = array_var($options, 'hidden_field_name', 'members');?>
<div id='<?php echo $component_id ?>-container'>
	<input id='<?php echo $genid . $hidden_field_name ?>' name='<?php echo $hidden_field_name ?>' type='hidden' value="<?php echo str_replace('"', "'", $selected_members_json); ?>"></input>

<?php

	$is_ie = isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false);

	$members_dimension = array();
	$sel_mem_ids = array();
	foreach ($dimensions as $dimension) :
	
		$dimension_id = $dimension['dimension_id'];
		if (isset($skipped_dimensions) && is_array($skipped_dimensions) && in_array($dimension_id, $skipped_dimensions)) continue;
		
		if ( is_array(array_var($options, 'allowedDimensions')) && array_search($dimension_id, $options['allowedDimensions']) === false ){
			continue;	 
		}

		if (!array_var($options, 'allow_non_manageable') && !$dimension['is_manageable']) continue;
		
		$is_required = array_var($dimension, 'is_required');
		$dimension_name = array_var($dimension, 'dimension_name');
		if ($is_required) $dimension_name .= " *";
		
		if (isset($simulate_required) && is_array($simulate_required) && in_array($dimension_id, $simulate_required)) $is_required = true;

		$dimension_selected_members = array();
		foreach ($selected_members as $selected_member) {
			if ($selected_member->getDimensionId() == $dimension_id) $dimension_selected_members[] = $selected_member;
		}
		$autocomplete_options = array();

		$expgenid = gen_id();

		// Render view by obj type
		if(!$default_view && file_exists(get_template_path("components/small_view", "dimension"))){
			include get_template_path("components/small_view", "dimension");
		}else{
			include get_template_path("components/default_view", "dimension");
		}		
?>
	

	<script>
	if (!member_selector['<?php echo $genid; ?>']) member_selector['<?php echo $genid; ?>'] = {};
	if (!member_selector['<?php echo $genid; ?>'].properties) member_selector['<?php echo $genid; ?>'].properties = {};
	member_selector['<?php echo $genid; ?>'].hiddenFieldName = '<?php echo $hidden_field_name; ?>';
	member_selector['<?php echo $genid; ?>'].otid = '<?php echo $content_object_type_id; ?>';
	<?php
	
	$listeners_str = "{";
	foreach ($listeners as $event => $function) {
		$listeners_str .= $event .' : \''. escape_single_quotes($function) .'\',';
	}
	if (str_ends_with($listeners_str, ",")) $listeners_str = substr($listeners_str, 0, -1);
	$listeners_str .= "}";
	?>

	member_selector['<?php echo $genid; ?>'].properties['<?php echo $dimension_id ?>'] = {
		title: '<?php echo $dimension_name ?>',
		dimensionId: <?php echo $dimension_id ?>,
		objectTypeId: '<?php echo $content_object_type_id ?>',
		required: <?php echo $is_required ? '1' : '0'?>,
		reloadDimensions: <?php echo json_encode( DimensionMemberAssociations::instance()->getDimensionsToReload($dimension_id) ); ?>,
		isMultiple: <?php echo $dimension['is_multiple'] ? '1' : '0'?>,
		listeners: <?php echo $listeners_str ?>
	};

	if (member_selector['<?php echo $genid; ?>'].properties['<?php echo $dimension_id ?>'].listeners.after_render) {
		eval(member_selector['<?php echo $genid; ?>'].properties['<?php echo $dimension_id ?>'].listeners.after_render);
	}

	</script>
<?php endforeach;

	foreach ($listeners as $event => $function) {
		if ($event == 'after_render_all') {
			echo '<script>'.escape_single_quotes($function).';</script>';
		}
	}
?>
	<div class="clear"></div>
</div>
<script>

member_selector['<?php echo $genid; ?>'].members_dimension = Ext.util.JSON.decode('<?php echo json_encode($members_dimension)?>');
member_selector['<?php echo $genid; ?>'].context = og.contextManager.plainContext();

member_selector.init('<?php echo $genid; ?>');

</script>
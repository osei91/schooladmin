<?php

// Data functions (insert, update, delete, form) for table timetable


function timetable_insert(){
	global $Translation;

	// mm: can member insert record?
	$arrPerm=getTablePermissions('timetable');
	if(!$arrPerm[1]){
		return false;
	}

	$data['Class'] = makeSafe($_REQUEST['Class']);
		if($data['Class'] == empty_lookup_value){ $data['Class'] = ''; }
	$data['Stream'] = makeSafe($_REQUEST['Stream']);
		if($data['Stream'] == empty_lookup_value){ $data['Stream'] = ''; }
	$data['Time_Table'] = PrepareUploadedFile('Time_Table', 20480000,'txt|doc|docx|docm|odt|pdf|rtf', false, '');
	if($data['Time_Table']== ''){
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">" . $Translation['error:'] . " 'Time Table': " . $Translation['field not null'] . '<br><br>';
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	if($data['Class']== ''){
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">" . $Translation['error:'] . " 'Class': " . $Translation['field not null'] . '<br><br>';
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}

	/* for empty upload fields, when saving a copy of an existing record, copy the original upload field */
	if($_REQUEST['SelectedID']){
		$res = sql("select * from timetable where id='" . makeSafe($_REQUEST['SelectedID']) . "'", $eo);
		if($row = db_fetch_assoc($res)){
			if(!$data['Time_Table']) $data['Time_Table'] = makeSafe($row['Time_Table']);
		}
	}

	// hook: timetable_before_insert
	if(function_exists('timetable_before_insert')){
		$args=array();
		if(!timetable_before_insert($data, getMemberInfo(), $args)){ return false; }
	}

	$o = array('silentErrors' => true);
	sql('insert into `timetable` set       ' . ($data['Time_Table'] != '' ? "`Time_Table`='{$data['Time_Table']}'" : '`Time_Table`=NULL') . ', `Class`=' . (($data['Class'] !== '' && $data['Class'] !== NULL) ? "'{$data['Class']}'" : 'NULL') . ', `Stream`=' . (($data['Stream'] !== '' && $data['Stream'] !== NULL) ? "'{$data['Stream']}'" : 'NULL'), $o);
	if($o['error']!=''){
		echo $o['error'];
		echo "<a href=\"timetable_view.php?addNew_x=1\">{$Translation['< back']}</a>";
		exit;
	}

	$recID = db_insert_id(db_link());

	// hook: timetable_after_insert
	if(function_exists('timetable_after_insert')){
		$res = sql("select * from `timetable` where `id`='" . makeSafe($recID, false) . "' limit 1", $eo);
		if($row = db_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args=array();
		if(!timetable_after_insert($data, getMemberInfo(), $args)){ return $recID; }
	}

	// mm: save ownership data
	set_record_owner('timetable', $recID, getLoggedMemberID());

	return $recID;
}

function timetable_delete($selected_id, $AllowDeleteOfParents=false, $skipChecks=false){
	// insure referential integrity ...
	global $Translation;
	$selected_id=makeSafe($selected_id);

	// mm: can member delete record?
	$arrPerm=getTablePermissions('timetable');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='timetable' and pkValue='$selected_id'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='timetable' and pkValue='$selected_id'");
	if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
		// delete allowed, so continue ...
	}else{
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: timetable_before_delete
	if(function_exists('timetable_before_delete')){
		$args=array();
		if(!timetable_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'];
	}

	// delete file stored in the 'Time_Table' field
	$res = sql("select `Time_Table` from `timetable` where `id`='$selected_id'", $eo);
	if($row=@db_fetch_row($res)){
		if($row[0]!=''){
			@unlink(getUploadDir('').$row[0]);
		}
	}

	sql("delete from `timetable` where `id`='$selected_id'", $eo);

	// hook: timetable_after_delete
	if(function_exists('timetable_after_delete')){
		$args=array();
		timetable_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("delete from membership_userrecords where tableName='timetable' and pkValue='$selected_id'", $eo);
}

function timetable_update($selected_id){
	global $Translation;

	// mm: can member edit record?
	$arrPerm=getTablePermissions('timetable');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='timetable' and pkValue='".makeSafe($selected_id)."'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='timetable' and pkValue='".makeSafe($selected_id)."'");
	if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){ // allow update?
		// update allowed, so continue ...
	}else{
		return false;
	}

	$data['Time_Table'] = PrepareUploadedFile('Time_Table', 20480000, 'txt|doc|docx|docm|odt|pdf|rtf', false, "");
	$existing_Time_Table = sqlValue("select `Time_Table` from `timetable` where `id`='" . makeSafe($selected_id) . "'");
	if($data['Time_Table'] == '' && !$existing_Time_Table){
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Time Table': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	$data['Class'] = makeSafe($_REQUEST['Class']);
		if($data['Class'] == empty_lookup_value){ $data['Class'] = ''; }
	if($data['Class']==''){
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Class': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	$data['Stream'] = makeSafe($_REQUEST['Stream']);
		if($data['Stream'] == empty_lookup_value){ $data['Stream'] = ''; }
	$data['selectedID']=makeSafe($selected_id);
		// delete file from server
		if($data['Time_Table'] != ''){
			$res = sql("select `Time_Table` from `timetable` where `id`='".makeSafe($selected_id)."'", $eo);
			if($row=@db_fetch_row($res)){
				if($row[0]!=''){
					@unlink(getUploadDir('').$row[0]);
				}
			}
		}

	// hook: timetable_before_update
	if(function_exists('timetable_before_update')){
		$args=array();
		if(!timetable_before_update($data, getMemberInfo(), $args)){ return false; }
	}

	$o=array('silentErrors' => true);
	sql('update `timetable` set       ' . ($data['Time_Table']!='' ? "`Time_Table`='{$data['Time_Table']}'" : '`Time_Table`=`Time_Table`') . ', `Class`=' . (($data['Class'] !== '' && $data['Class'] !== NULL) ? "'{$data['Class']}'" : 'NULL') . ', `Stream`=' . (($data['Stream'] !== '' && $data['Stream'] !== NULL) ? "'{$data['Stream']}'" : 'NULL') . " where `id`='".makeSafe($selected_id)."'", $o);
	if($o['error']!=''){
		echo $o['error'];
		echo '<a href="timetable_view.php?SelectedID='.urlencode($selected_id)."\">{$Translation['< back']}</a>";
		exit;
	}


	// hook: timetable_after_update
	if(function_exists('timetable_after_update')){
		$res = sql("SELECT * FROM `timetable` WHERE `id`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = $data['id'];
		$args = array();
		if(!timetable_after_update($data, getMemberInfo(), $args)){ return; }
	}

	// mm: update ownership data
	sql("update membership_userrecords set dateUpdated='".time()."' where tableName='timetable' and pkValue='".makeSafe($selected_id)."'", $eo);

}

function timetable_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $ShowCancel = 0, $TemplateDV = '', $TemplateDVP = ''){
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;

	// mm: get table permissions
	$arrPerm=getTablePermissions('timetable');
	if(!$arrPerm[1] && $selected_id==''){ return ''; }
	$AllowInsert = ($arrPerm[1] ? true : false);
	// print preview?
	$dvprint = false;
	if($selected_id && $_REQUEST['dvprint_x'] != ''){
		$dvprint = true;
	}

	$filterer_Class = thisOr(undo_magic_quotes($_REQUEST['filterer_Class']), '');
	$filterer_Stream = thisOr(undo_magic_quotes($_REQUEST['filterer_Stream']), '');

	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: Class
	$combo_Class = new DataCombo;
	// combobox: Stream
	$combo_Stream = new DataCombo;

	if($selected_id){
		// mm: check member permissions
		if(!$arrPerm[2]){
			return "";
		}
		// mm: who is the owner?
		$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='timetable' and pkValue='".makeSafe($selected_id)."'");
		$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='timetable' and pkValue='".makeSafe($selected_id)."'");
		if($arrPerm[2]==1 && getLoggedMemberID()!=$ownerMemberID){
			return "";
		}
		if($arrPerm[2]==2 && getLoggedGroupID()!=$ownerGroupID){
			return "";
		}

		// can edit?
		if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){
			$AllowUpdate=1;
		}else{
			$AllowUpdate=0;
		}

		$res = sql("select * from `timetable` where `id`='".makeSafe($selected_id)."'", $eo);
		if(!($row = db_fetch_array($res))){
			return error_message($Translation['No records found'], 'timetable_view.php', false);
		}
		$urow = $row; /* unsanitized data */
		$hc = new CI_Input();
		$row = $hc->xss_clean($row); /* sanitize data */
		$combo_Class->SelectedData = $row['Class'];
		$combo_Stream->SelectedData = $row['Stream'];
	}else{
		$combo_Class->SelectedData = $filterer_Class;
		$combo_Stream->SelectedData = $filterer_Stream;
	}
	$combo_Class->HTML = '<span id="Class-container' . $rnd1 . '"></span><input type="hidden" name="Class" id="Class' . $rnd1 . '" value="' . html_attr($combo_Class->SelectedData) . '">';
	$combo_Class->MatchText = '<span id="Class-container-readonly' . $rnd1 . '"></span><input type="hidden" name="Class" id="Class' . $rnd1 . '" value="' . html_attr($combo_Class->SelectedData) . '">';
	$combo_Stream->HTML = '<span id="Stream-container' . $rnd1 . '"></span><input type="hidden" name="Stream" id="Stream' . $rnd1 . '" value="' . html_attr($combo_Stream->SelectedData) . '">';
	$combo_Stream->MatchText = '<span id="Stream-container-readonly' . $rnd1 . '"></span><input type="hidden" name="Stream" id="Stream' . $rnd1 . '" value="' . html_attr($combo_Stream->SelectedData) . '">';

	ob_start();
	?>

	<script>
		// initial lookup values
		AppGini.current_Class__RAND__ = { text: "", value: "<?php echo addslashes($selected_id ? $urow['Class'] : $filterer_Class); ?>"};
		AppGini.current_Stream__RAND__ = { text: "", value: "<?php echo addslashes($selected_id ? $urow['Stream'] : $filterer_Stream); ?>"};

		jQuery(function() {
			setTimeout(function(){
				if(typeof(Class_reload__RAND__) == 'function') Class_reload__RAND__();
				if(typeof(Stream_reload__RAND__) == 'function') Stream_reload__RAND__();
			}, 10); /* we need to slightly delay client-side execution of the above code to allow AppGini.ajaxCache to work */
		});
		function Class_reload__RAND__(){
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint){ ?>

			$j("#Class-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c){
					$j.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: AppGini.current_Class__RAND__.value, t: 'timetable', f: 'Class' },
						success: function(resp){
							c({
								id: resp.results[0].id,
								text: resp.results[0].text
							});
							$j('[name="Class"]').val(resp.results[0].id);
							$j('[id=Class-container-readonly__RAND__]').html('<span id="Class-match-text">' + resp.results[0].text + '</span>');
							if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=classes_view_parent]').hide(); }else{ $j('.btn[id=classes_view_parent]').show(); }


							if(typeof(Class_update_autofills__RAND__) == 'function') Class_update_autofills__RAND__();
						}
					});
				},
				width: '100%',
				formatNoMatches: function(term){ return '<?php echo addslashes($Translation['No matches found!']); ?>'; },
				minimumResultsForSearch: 10,
				loadMorePadding: 200,
				ajax: {
					url: 'ajax_combo.php',
					dataType: 'json',
					cache: true,
					data: function(term, page){ return { s: term, p: page, t: 'timetable', f: 'Class' }; },
					results: function(resp, page){ return resp; }
				},
				escapeMarkup: function(str){ return str; }
			}).on('change', function(e){
				AppGini.current_Class__RAND__.value = e.added.id;
				AppGini.current_Class__RAND__.text = e.added.text;
				$j('[name="Class"]').val(e.added.id);
				if(e.added.id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=classes_view_parent]').hide(); }else{ $j('.btn[id=classes_view_parent]').show(); }


				if(typeof(Class_update_autofills__RAND__) == 'function') Class_update_autofills__RAND__();
			});

			if(!$j("#Class-container__RAND__").length){
				$j.ajax({
					url: 'ajax_combo.php',
					dataType: 'json',
					data: { id: AppGini.current_Class__RAND__.value, t: 'timetable', f: 'Class' },
					success: function(resp){
						$j('[name="Class"]').val(resp.results[0].id);
						$j('[id=Class-container-readonly__RAND__]').html('<span id="Class-match-text">' + resp.results[0].text + '</span>');
						if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=classes_view_parent]').hide(); }else{ $j('.btn[id=classes_view_parent]').show(); }

						if(typeof(Class_update_autofills__RAND__) == 'function') Class_update_autofills__RAND__();
					}
				});
			}

		<?php }else{ ?>

			$j.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: AppGini.current_Class__RAND__.value, t: 'timetable', f: 'Class' },
				success: function(resp){
					$j('[id=Class-container__RAND__], [id=Class-container-readonly__RAND__]').html('<span id="Class-match-text">' + resp.results[0].text + '</span>');
					if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=classes_view_parent]').hide(); }else{ $j('.btn[id=classes_view_parent]').show(); }

					if(typeof(Class_update_autofills__RAND__) == 'function') Class_update_autofills__RAND__();
				}
			});
		<?php } ?>

		}
		function Stream_reload__RAND__(){
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint){ ?>

			$j("#Stream-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c){
					$j.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: AppGini.current_Stream__RAND__.value, t: 'timetable', f: 'Stream' },
						success: function(resp){
							c({
								id: resp.results[0].id,
								text: resp.results[0].text
							});
							$j('[name="Stream"]').val(resp.results[0].id);
							$j('[id=Stream-container-readonly__RAND__]').html('<span id="Stream-match-text">' + resp.results[0].text + '</span>');
							if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=streams_view_parent]').hide(); }else{ $j('.btn[id=streams_view_parent]').show(); }


							if(typeof(Stream_update_autofills__RAND__) == 'function') Stream_update_autofills__RAND__();
						}
					});
				},
				width: '100%',
				formatNoMatches: function(term){ return '<?php echo addslashes($Translation['No matches found!']); ?>'; },
				minimumResultsForSearch: 10,
				loadMorePadding: 200,
				ajax: {
					url: 'ajax_combo.php',
					dataType: 'json',
					cache: true,
					data: function(term, page){ return { s: term, p: page, t: 'timetable', f: 'Stream' }; },
					results: function(resp, page){ return resp; }
				},
				escapeMarkup: function(str){ return str; }
			}).on('change', function(e){
				AppGini.current_Stream__RAND__.value = e.added.id;
				AppGini.current_Stream__RAND__.text = e.added.text;
				$j('[name="Stream"]').val(e.added.id);
				if(e.added.id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=streams_view_parent]').hide(); }else{ $j('.btn[id=streams_view_parent]').show(); }


				if(typeof(Stream_update_autofills__RAND__) == 'function') Stream_update_autofills__RAND__();
			});

			if(!$j("#Stream-container__RAND__").length){
				$j.ajax({
					url: 'ajax_combo.php',
					dataType: 'json',
					data: { id: AppGini.current_Stream__RAND__.value, t: 'timetable', f: 'Stream' },
					success: function(resp){
						$j('[name="Stream"]').val(resp.results[0].id);
						$j('[id=Stream-container-readonly__RAND__]').html('<span id="Stream-match-text">' + resp.results[0].text + '</span>');
						if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=streams_view_parent]').hide(); }else{ $j('.btn[id=streams_view_parent]').show(); }

						if(typeof(Stream_update_autofills__RAND__) == 'function') Stream_update_autofills__RAND__();
					}
				});
			}

		<?php }else{ ?>

			$j.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: AppGini.current_Stream__RAND__.value, t: 'timetable', f: 'Stream' },
				success: function(resp){
					$j('[id=Stream-container__RAND__], [id=Stream-container-readonly__RAND__]').html('<span id="Stream-match-text">' + resp.results[0].text + '</span>');
					if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=streams_view_parent]').hide(); }else{ $j('.btn[id=streams_view_parent]').show(); }

					if(typeof(Stream_update_autofills__RAND__) == 'function') Stream_update_autofills__RAND__();
				}
			});
		<?php } ?>

		}
	</script>
	<?php

	$lookups = str_replace('__RAND__', $rnd1, ob_get_contents());
	ob_end_clean();


	// code for template based detail view forms

	// open the detail view template
	if($dvprint){
		$template_file = is_file("./{$TemplateDVP}") ? "./{$TemplateDVP}" : './templates/timetable_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	}else{
		$template_file = is_file("./{$TemplateDV}") ? "./{$TemplateDV}" : './templates/timetable_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'TimeTable details', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', ($_REQUEST['Embedded'] ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($AllowInsert){
		if(!$selected_id) $templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return timetable_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return timetable_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	}else{
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if($_REQUEST['Embedded']){
		$backAction = 'AppGini.closeParentModal(); return false;';
	}else{
		$backAction = '$j(\'form\').eq(0).attr(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($selected_id){
		if(!$_REQUEST['Embedded']) $templateCode = str_replace('<%%DVPRINT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" onclick="$$(\'form\')[0].writeAttribute(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;" title="' . html_attr($Translation['Print Preview']) . '"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($AllowUpdate){
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return timetable_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		}else{
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		}
		if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '<button type="submit" class="btn btn-danger" id="delete" name="delete_x" value="1" onclick="return confirm(\'' . $Translation['are you sure?'] . '\');" title="' . html_attr($Translation['Delete']) . '"><i class="glyphicon glyphicon-trash"></i> ' . $Translation['Delete'] . '</button>', $templateCode);
		}else{
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		}
		$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>', $templateCode);
	}else{
		$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		$templateCode = str_replace('<%%DESELECT_BUTTON%%>', ($ShowCancel ? '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>' : ''), $templateCode);
	}

	// set records to read only if user can't insert new records and can't edit current record
	if(($selected_id && !$AllowUpdate && !$AllowInsert) || (!$selected_id && !$AllowInsert)){
		$jsReadOnly .= "\tjQuery('#Time_Table').replaceWith('<div class=\"form-control-static\" id=\"Time_Table\">' + (jQuery('#Time_Table').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#Time_Table, #Time_Table-edit-link').hide();\n";
		$jsReadOnly .= "\tjQuery('#Class').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#Class_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\tjQuery('#Stream').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#Stream_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	}elseif($AllowInsert){
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
			$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode = str_replace('<%%COMBO(Class)%%>', $combo_Class->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(Class)%%>', $combo_Class->MatchText, $templateCode);
	$templateCode = str_replace('<%%URLCOMBOTEXT(Class)%%>', urlencode($combo_Class->MatchText), $templateCode);
	$templateCode = str_replace('<%%COMBO(Stream)%%>', $combo_Stream->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(Stream)%%>', $combo_Stream->MatchText, $templateCode);
	$templateCode = str_replace('<%%URLCOMBOTEXT(Stream)%%>', urlencode($combo_Stream->MatchText), $templateCode);

	/* lookup fields array: 'lookup field name' => array('parent table name', 'lookup field caption') */
	$lookup_fields = array(  'Class' => array('classes', 'Class'), 'Stream' => array('streams', 'Stream'));
	foreach($lookup_fields as $luf => $ptfc){
		$pt_perm = getTablePermissions($ptfc[0]);

		// process foreign key links
		if($pt_perm['view'] || $pt_perm['edit']){
			$templateCode = str_replace("<%%PLINK({$luf})%%>", '<button type="button" class="btn btn-default view_parent hspacer-md" id="' . $ptfc[0] . '_view_parent" title="' . html_attr($Translation['View'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-eye-open"></i></button>', $templateCode);
		}

		// if user has insert permission to parent table of a lookup field, put an add new button
		if($pt_perm['insert'] && !$_REQUEST['Embedded']){
			$templateCode = str_replace("<%%ADDNEW({$ptfc[0]})%%>", '<button type="button" class="btn btn-success add_new_parent hspacer-md" id="' . $ptfc[0] . '_add_new" title="' . html_attr($Translation['Add New'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-plus-sign"></i></button>', $templateCode);
		}
	}

	// process images
	$templateCode = str_replace('<%%UPLOADFILE(id)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Time_Table)%%>', ($noUploads ? '' : '<input type=hidden name=MAX_FILE_SIZE value=20480000>'.$Translation['upload image'].' <input type="file" name="Time_Table" id="Time_Table">'), $templateCode);
	if($AllowUpdate && $row['Time_Table'] != ''){
		$templateCode = str_replace('<%%REMOVEFILE(Time_Table)%%>', '<br><input type="checkbox" name="Time_Table_remove" id="Time_Table_remove" value="1"> <label for="Time_Table_remove" style="color: red; font-weight: bold;">'.$Translation['remove image'].'</label>', $templateCode);
	}else{
		$templateCode = str_replace('<%%REMOVEFILE(Time_Table)%%>', '', $templateCode);
	}
	$templateCode = str_replace('<%%UPLOADFILE(Class)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Stream)%%>', '', $templateCode);

	// process values
	if($selected_id){
		if( $dvprint) $templateCode = str_replace('<%%VALUE(id)%%>', safe_html($urow['id']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(id)%%>', html_attr($row['id']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(id)%%>', urlencode($urow['id']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(Time_Table)%%>', safe_html($urow['Time_Table']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(Time_Table)%%>', html_attr($row['Time_Table']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Time_Table)%%>', urlencode($urow['Time_Table']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(Class)%%>', safe_html($urow['Class']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(Class)%%>', html_attr($row['Class']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Class)%%>', urlencode($urow['Class']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(Stream)%%>', safe_html($urow['Stream']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(Stream)%%>', html_attr($row['Stream']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Stream)%%>', urlencode($urow['Stream']), $templateCode);
	}else{
		$templateCode = str_replace('<%%VALUE(id)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(id)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(Time_Table)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Time_Table)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(Class)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Class)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(Stream)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Stream)%%>', urlencode(''), $templateCode);
	}

	// process translations
	foreach($Translation as $symbol=>$trans){
		$templateCode = str_replace("<%%TRANSLATION($symbol)%%>", $trans, $templateCode);
	}

	// clear scrap
	$templateCode = str_replace('<%%', '<!-- ', $templateCode);
	$templateCode = str_replace('%%>', ' -->', $templateCode);

	// hide links to inaccessible tables
	if($_REQUEST['dvprint_x'] == ''){
		$templateCode .= "\n\n<script>\$j(function(){\n";
		$arrTables = getTableList();
		foreach($arrTables as $name => $caption){
			$templateCode .= "\t\$j('#{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\t\$j('#xs_{$name}_link').removeClass('hidden');\n";
		}

		$templateCode .= $jsReadOnly;
		$templateCode .= $jsEditable;

		if(!$selected_id){
			$templateCode.="\n\tif(document.getElementById('Time_TableEdit')){ document.getElementById('Time_TableEdit').style.display='inline'; }";
			$templateCode.="\n\tif(document.getElementById('Time_TableEditLink')){ document.getElementById('Time_TableEditLink').style.display='none'; }";
		}

		$templateCode.="\n});</script>\n";
	}

	// ajaxed auto-fill fields
	$templateCode .= '<script>';
	$templateCode .= '$j(function() {';


	$templateCode.="});";
	$templateCode.="</script>";
	$templateCode .= $lookups;

	// handle enforced parent values for read-only lookup fields

	// don't include blank images in lightbox gallery
	$templateCode = preg_replace('/blank.gif" data-lightbox=".*?"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	/* default field values */
	$rdata = $jdata = get_defaults('timetable');
	if($selected_id){
		$jdata = get_joined_record('timetable', $selected_id);
		if($jdata === false) $jdata = get_defaults('timetable');
		$rdata = $row;
	}
	$cache_data = array(
		'rdata' => array_map('nl2br', array_map('addslashes', $rdata)),
		'jdata' => array_map('nl2br', array_map('addslashes', $jdata))
	);
	$templateCode .= loadView('timetable-ajax-cache', $cache_data);

	// hook: timetable_dv
	if(function_exists('timetable_dv')){
		$args=array();
		timetable_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}
?>
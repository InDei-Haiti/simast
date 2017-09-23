function initEdits(){
	$j(".subj_selects").live("change", function(e){
		var $grp = $j(this).find('option:selected').closest("optgroup"), gtitle = $grp.attr('label');
		$j(this).parent().removeClass("warn");
		editor.setClientType(this, gtitle);
	});
	
}

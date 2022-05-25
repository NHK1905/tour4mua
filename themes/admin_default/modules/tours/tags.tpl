<!-- BEGIN: main -->
<div class="well">
	<form class="navbar-form" action="{NV_BASE_ADMINURL}index.php" method="get" onsubmit="return nv_search_tag();">
		<input class="form-control" id="q" type="text" value="{Q}" maxlength="64" name="q" style="width: 265px" placeholder="{LANG.keywords}" />
		<input class="btn btn-primary" type="submit" value="{LANG.search_submit}" /><br />
	</form>
	<label><em>{LANG.search_note_fix}</em></label>
</div>

<!-- BEGIN: incomplete_link -->
<div class="alert alert-info">
	<a class="text-info" href="{ALL_LINK}">{LANG.tags_all_link}.</a>
</div>
<!-- END: incomplete_link -->
<div id="module_show_list">
	{TAGS_LIST}
</div>
<br />
<!-- BEGIN: error -->
<div class="alert alert-warning">{ERROR}</div>
<!-- END: error -->

<div class="panel panel-default">
	<div class="panel-body">
		<form action="{NV_BASE_ADMINURL}index.php" method="post" class="form-horizontal">
			<input type="hidden" name ="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
			<input type="hidden" name ="{NV_OP_VARIABLE}" value="{OP}" />
			<input type="hidden" name ="tid" value="{tid}" />
			<input name="savecat" type="hidden" value="1" />
			<!-- BEGIN: incomplete --><input name="incomplete" type="hidden" value="1" /><!-- END: incomplete -->
		
			<div class="form-group">
				<label class="col-sm-3 control-label"><strong>{LANG.alias}</strong></label>
				<div class="col-sm-21">
					<input class="form-control" name="alias" id="idalias" type="text" value="{alias}" maxlength="255" /> {GLANG.length_characters}: <span id="aliaslength" class="red">0</span>. {GLANG.title_suggest_max}
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-3 control-label"><strong>{LANG.keywords}</strong></label>
				<div class="col-sm-21">
					<input class="form-control" name="keywords" type="text" value="{keywords}" maxlength="255" />
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-3 control-label"><strong>{LANG.description}</strong></label>
				<div class="col-sm-21">
					<textarea class="form-control" id="description" name="description" cols="100" rows="5">{description}</textarea> {GLANG.length_characters}: <span id="descriptionlength" class="red">0</span>. {GLANG.description_suggest_max} 
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-3 control-label"><strong>{LANG.image}</strong></label>
				<div class="col-sm-21">
					<div class="input-group">
						<input class="form-control" type="text" name="image" value="{ROW.image}" id="image" /> <span class="input-group-btn">
							<button class="btn btn-default selectfile" type="button">
								<em class="fa fa-folder-open-o fa-fix">&nbsp;</em>
							</button>
						</span>
					</div>
				</div>
			</div>
			
			<div class="text-center">
				<input class="btn btn-primary" name="submit1" type="submit" value="{LANG.save}" />
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	$("#aliaslength").html($("#idalias").val().length);
	$("#idalias").bind("keyup paste", function() {
		$("#aliaslength").html($(this).val().length);
	});

	$("#descriptionlength").html($("#description").val().length);
	$("#description").bind("keyup paste", function() {
		$("#descriptionlength").html($(this).val().length);
	});

	$(".selectfile").click(function() {
		var area = "image";
		var path = "{UPLOAD_CURRENT}";
		var currentpath = "{UPLOAD_CURRENT}";
		var type = "image";
		nv_open_browse(script_name + "?" + nv_name_variable + "=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", 850, 420, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
		return false;
	});

	var load_bar = '<p class="text-center"><img src="' + nv_base_siteurl + 'assets/images/load_bar.gif" alt="Waiting..."/></p>';

	function nv_search_tag(tid) {
		$("#module_show_list").html(load_bar).load("index.php?{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=tags&q=" + rawurlencode($("#q").val()) + "&num=" + nv_randomPassword(10))
		return false;
	}

	function nv_del_tags(tid) {
		if (confirm(nv_is_del_confirm[0])) {
			$("#module_show_list").html(load_bar).load("index.php?{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=tags&del_tid=" + tid + "&num=" + nv_randomPassword(10))
		}
		return false;
	}
</script>
<!-- END: main -->
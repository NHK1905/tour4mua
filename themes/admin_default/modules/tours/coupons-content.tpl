<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">

<!-- BEGIN: error -->
<div class="alert alert-warning">{ERROR}</div>
<!-- END: error -->
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post" class="form-horizontal" >
	<input type="hidden" name="id" value="{ROW.id}" />
	
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.title}</strong> <span class="red">(*)</span></label>
				<div class="col-sm-19 col-md-20">
					<input class="form-control" type="text" name="title" value="{ROW.title}" required="required" oninvalid="setCustomValidity( nv_required )" oninput="setCustomValidity('')" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.coupons}</strong> <span class="red">(*)</span></label>
				<div class="col-sm-19 col-md-20">
					<input class="form-control" type="text" name="code" value="{ROW.code}" required="required" oninvalid="setCustomValidity( nv_required )" oninput="setCustomValidity('')" pattern="^\w+$" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.coupons_discount}</strong> <span class="red">(*)</span></label>
				<div class="col-sm-19 col-md-20">
					<div class="row">
						<div class="col-sm-20">
							<input class="form-control" type="number" name="discount" value="{ROW.discount}" required="required" oninvalid="setCustomValidity( nv_required )" oninput="setCustomValidity('')" />
						</div>
						<div class="col-sm-4">
							<select class="form-control" name="type">
								<!-- BEGIN: select_type -->
								<option value="{OPTION.key}" {OPTION.selected}>{OPTION.title}</option>
								<!-- END: select_type -->
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.begin_time}</strong> <span class="red">(*)</span></label>
				<div class="col-sm-19 col-md-20">
					<div class="input-group">
						<input class="form-control" value="{ROW.date_start}" type="text" id="date_start" name="date_start" readonly="readonly" pattern="^[0-9]{2,2}\/[0-9]{2,2}\/[0-9]{1,4}$" />
						<span class="input-group-btn">
							<button class="btn btn-default" type="button" id="date_start-btn">
								<em class="fa fa-calendar fa-fix">&nbsp;</em>
							</button> </span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.end_time}</strong></label>
				<div class="col-sm-19 col-md-20">
					<div class="input-group">
						<input class="form-control" value="{ROW.date_end}" type="text" id="date_end" name="date_end" readonly="readonly" pattern="^[0-9]{2,2}\/[0-9]{2,2}\/[0-9]{1,4}$" />
						<span class="input-group-btn">
							<button class="btn btn-default" type="button" id="date_end-btn">
								<em class="fa fa-calendar fa-fix">&nbsp;</em>
							</button> </span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.coupons_tour}</strong></label>
				<div class="col-sm-19 col-md-20">
					<select name="tourid[]" id="tourid" class="form-control" multiple="multiple">
						<!-- BEGIN: tours -->
						<option value="{TOUR.id}" selected="selected">{TOUR.title}</option>
						<!-- END: tours -->
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.coupons_quantity}</strong></label>
				<div class="col-sm-19 col-md-20">
					<input class="form-control" type="number" name="quantity" value="{ROW.quantity}" />
					<em class="help-block">{LANG.coupons_quantity_note}</em>
				</div>
			</div>
		</div>	
	</div>
	<div style="text-align: center"><input class="btn btn-primary loading" name="submit" type="submit" value="{LANG.save}" /></div>
</form>

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
	$("#date_start,#date_end").datepicker({
		dateFormat : "dd/mm/yy",
		changeMonth : true,
		changeYear : true,
		showOtherMonths : true,
	});
	
	$("#tourid").select2({
		language: "{NV_LANG_DATA}",
		ajax: {
	    url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=coupons-content&get_tour_json=1',
	    	dataType: 'json',
	    	delay: 250,
	    	data: function (params) {
	      		return {
	      			q: params.term, // search term
	      			page: params.page
	      		};
	      	},
	    	processResults: function (data, params) {
	    		params.page = params.page || 1;
	    		return {
	    			results: data,
	    			pagination: {
	    				more: (params.page * 30) < data.total_count
	    			}
	    		};
	    	},
		cache: true
		},
		escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		minimumInputLength: 3,
		templateResult: formatRepo, // omitted for brevity, see the source of this page
		templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
	});
});

function formatRepo (repo) {
	if (repo.loading) return repo.text;

	var markup = '<div class="clearfix">' +
   	'<div class="col-sm-19">' + repo.title + '</div>' +
    '<div clas="col-sm-5"><span class="show text-right">' + repo.code + '</span></div>' +
    '</div>';
	markup += '</div></div>';
	return markup;
}

function formatRepoSelection (repo) {
$('#username').val( repo.title );
	return repo.title || repo.text;
}
//]]>
</script>
<!-- END: main -->
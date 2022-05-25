f<!-- BEGIN: main -->
<div class="tour-content">
	<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
	<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/daterangepicker/daterangepicker.css" rel="stylesheet" />
	<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/bootstrap-select/css/bootstrap-select.min.css" />
	<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/daterangepicker/moment.min.js"></script>

	<!-- BEGIN: error -->
	<div class="alert alert-warning">{ERROR}</div>
	<!-- END: error -->

	<form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
		<input type="hidden" name="id" value="{ROW.id}" />
		<div class="row">
			<div class="col-md-18">
				<div class="panel panel-default">
					<div class="panel-heading">{LANG.tour_info}</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.title}</strong> <span class="red">(*)</span></label>
							<div class="col-sm-19 col-md-20">
								<input class="form-control" type="text" name="title" value="{ROW.title}" required="required" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.alias}</strong></label>
							<div class="col-sm-19 col-md-18">
								<input class="form-control" type="text" name="alias" value="{ROW.alias}" id="id_alias" />
							</div>
							<div class="col-sm-4 col-md-2">
								<i class="fa fa-refresh fa-lg icon-pointer" onclick="nv_get_alias('id_alias');">&nbsp;</i>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.custom_title}</strong></label>
							<div class="col-sm-19 col-md-20">
								<input class="form-control" type="text" name="title_custom" value="{ROW.title_custom}" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.cat}</strong> <span class="red">(*)</span></label></label>
							<div class="col-sm-19 col-md-20">
								<select name="catid" id="catid" class="form-control">
									<option value=0>---{LANG.cat_c}---</option>
									<!-- BEGIN: cat -->
									<option value="{CAT.id}"{CAT.selected}>{CAT.space}{CAT.title}</option>
									<!-- END: cat -->
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.code}</strong> <!-- BEGIN: code_required_star --> <span class="red">(*)</span> <!-- END: code_required_star --></label>
							<div class="col-sm-19 col-md-8">
								<input class="form-control" type="text" name="code" value="{ROW.code}"
								<!-- BEGIN: code_required_attr -->
								required="required"
								<!-- END: code_required_attr -->
								/>
							</div>
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.num_day}</strong> <span class="red">(*)</span></label>
							<div class="col-sm-19 col-md-8">
								<input class="form-control" type="number" name="num_day" value="{ROW.num_day}" pattern="^[0-9]*$" required="required" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.date_start}</strong> <span class="red">(*)</span></label>
							<div class="col-sm-19 col-md-8">
								<div class="row">
									<div class="col-md-10">
										<select name="date_start_method" class="form-control" id="start_date_method">
											<!-- BEGIN: date_start_method -->
											<option value="{DATE_START_METHOD.index}" {DATE_START_METHOD.selected}>{DATE_START_METHOD.value}</option>
											<!-- END: date_start_method -->								
										</select>
									</div>
									<div class="col-md-14">
										<div class="date_start_method" id="date_start_method_0" {DATE_START_METHOD_DISPLAY_0}>
											<div class="input-group">
												<input class="form-control" type="text" name="date_start" readonly="readonly" value="{ROW.date_start}" id="date_start" pattern="^[0-9]{2,2}\/[0-9]{2,2}\/[0-9]{1,4}$" required="required" /> <span class="input-group-btn">
													<button class="btn btn-default" type="button" id="date_start-btn">
														<em class="fa fa-calendar fa-fix"> </em>
													</button>
												</span>
											</div>
										</div>
										<div class="date_start_method" id="date_start_method_1" {DATE_START_METHOD_DISPLAY_1}>
											<select class="selectpicker" multiple="multiple" name="date_start_config[1][]">
												<!-- BEGIN: day_week -->
												<option value="{DAY_WEEK.index}" {DAY_WEEK.selected}>{DAY_WEEK.value}</option>
												<!-- END: day_week -->
											</select>
										</div>
										<div class="date_start_method" id="date_start_method_2" {DATE_START_METHOD_DISPLAY_2}>
											<select class="selectpicker" multiple="multiple" name="date_start_config[2][]">
												<!-- BEGIN: day_month -->
												<option value="{DAY_MONTH.index}" {DAY_MONTH.selected}>{DAY_MONTH.index}</option>
												<!-- END: day_month -->
											</select>
										</div>
										<div class="date_start_method" id="date_start_method_3" {DATE_START_METHOD_DISPLAY_3}>
											<span class="text-middle">{LANG.allday}</span>
										</div>
										<div class="date_start_method" id="date_start_method_4" {DATE_START_METHOD_DISPLAY_4}>
											<span class="text-middle">{LANG.contact}</span>
										</div>
									</div>
								</div>
							</div>
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.num_seat}</strong> <span class="red">(*)</span></label>
							<div class="col-sm-19 col-md-8">
								<input type="number" name="num_seat" class="form-control" value="{ROW.num_seat}" pattern="^[0-9]*$" oninvalid="setCustomValidity( nv_digits )" oninput="setCustomValidity('')" required="required" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.place_start}</strong> <span class="red">(*)</span></label>
							<div class="col-sm-19 col-md-20">{PLACE_START}</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.place_end}</strong> <span class="red">(*)</span></label>
							<div class="col-sm-19 col-md-20">{PLACE_END}</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">{LANG.tour_price}</div>
					<div class="panel-body">
						<div id="loadprice">
							<p class="text-center">
								<strong class="text-success">{LANG.tour_price_note}</strong>
							</p>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">{LANG.tour_description}</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.homeimgfile}</strong></label>
							<div class="col-sm-19 col-md-20">
								<div class="input-group">
									<input class="form-control" type="text" name="homeimgfile" value="{ROW.homeimgfile}" id="id_homeimgfile" /> <span class="input-group-btn">
										<button class="btn btn-default selectfile" type="button">
											<em class="fa fa-folder-open-o fa-fix">&nbsp;</em>
										</button>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.homeimgalt}</strong></label>
							<div class="col-sm-19 col-md-20">
								<input class="form-control" type="text" name="homeimgalt" value="{ROW.homeimgalt}" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.description}</strong> <span class="red">(*)</span></label>
							<div class="col-sm-19 col-md-20">
								<textarea class="form-control" name="description" rows="4">{ROW.description}</textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.description_html}</strong></label>
							<div class="col-sm-19 col-md-20">{ROW.description_html}</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.tour_plan}</strong></label>
							<div class="col-sm-19 col-md-20">{ROW.plan}</div>
						</div>

					</div>
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading">
						<a href="javascript:void(0)" data-toggle="collapse" data-target="#panel-note-content">{LANG.note_content}</a>
					</div>
					<div class="panel-body collapse <!-- BEGIN: note_collapse_in -->in<!-- END: note_collapse_in -->" id="panel-note-content">
						{ROW.note_editor}
					</div>
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading">
						<a href="javascript:void(0)" data-toggle="collapse" data-target="#panel-services">{LANG.services}</a>
					</div>
					<!-- BEGIN: services -->
					<div class="panel-body collapse <!-- BEGIN: services_collapse_in -->in<!-- END: services_collapse_in -->" id="panel-services">
						<div class="row">
							<!-- BEGIN: loop -->
							<div class="col-xs-24 col-sm-12 col-md-8">
								<label><input type="checkbox" name="services[]" value="{SERVICES.id}" {SERVICES.checked} />{SERVICES.title}</label>
							</div>
							<!-- END: loop -->
						</div>
					</div>
					<!-- END: services -->
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading">
						<a href="javascript:void(0)" data-toggle="collapse" data-target="#panel-vehicle">{LANG.vehicle}</a>
					</div>
					<!-- BEGIN: vehicle -->
					<div class="panel-body collapse <!-- BEGIN: vehicle_collapse_in -->in<!-- END: vehicle_collapse_in -->" id="panel-vehicle">
						<div class="row">
							<!-- BEGIN: loop -->
							<div class="col-xs-24 col-sm-12 col-md-8">
								<label><input type="radio" name="vehicle" value="{VEHICLE.id}" {VEHICLE.checked} />{VEHICLE.title}</label>
							</div>
							<!-- END: loop -->
						</div>
					</div>
					<!-- END: vehicle -->
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading">
						<a href="javascript:void(0)" data-toggle="collapse" data-target="#panel-flying-info">{LANG.flying_info}</a>
					</div>
					<div class="panel-body collapse <!-- BEGIN: flying_collapse_in -->in<!-- END: flying_collapse_in -->" id="panel-flying-info">
						<div class="row m-bottom">
							<div class="col-md-2 text-middle">
								<label style="display: block; margin-top: 24px"><strong>{LANG.flying_begin}</strong></label>
							</div>
							<div class="col-md-10">
								<label>{LANG.flying}</label> <select class="form-control" name="flying_begin[id]">
									<option value="">---{LANG.flying_c}</option>
									<!-- BEGIN: flying_begin -->
									<option value="{FLYING.id}"{FLYING.selected_begin}>{FLYING.title}</option>
									<!-- END: flying_begin -->
								</select>
							</div>
							<div class="col-md-6">
								<label>{LANG.flying_time}</label>
								<div class="input-group">
									<input class="form-control flying_time" type="text" name="flying_begin[time]" readonly="readonly" value="{ROW.flying_begin.time}" required="required"  /> <span class="input-group-btn">
										<button class="btn btn-default" type="button" id="date_start-btn">
											<em class="fa fa-calendar fa-fix"> </em>
										</button>
									</span>
								</div>
							</div>
							<div class="col-md-6">
								<label>{LANG.flying_code}</label> <input type="text" value="{ROW.flying_begin.code}" class="form-control" name="flying_begin[code]" />
							</div>
						</div>
						<div class="row">
							<div class="col-md-2 text-middle">
								<label style="display: block; margin-top: 24px"><strong>{LANG.flying_end}</strong></label>
							</div>
							<div class="col-md-10">
								<label>{LANG.flying}</label> <select class="form-control" name="flying_end[id]">
									<option value="">---{LANG.flying_c}</option>
									<!-- BEGIN: flying_end -->
									<option value="{FLYING.id}"{FLYING.selected_end}>{FLYING.title}</option>
									<!-- END: flying_end -->
								</select>
							</div>
							<div class="col-md-6">
								<label>{LANG.flying_time}</label>
								<div class="input-group">
									<input class="form-control flying_time" type="text" name="flying_end[time]" readonly="readonly" value="{ROW.flying_end.time}" required="required"  /> <span class="input-group-btn">
										<button class="btn btn-default" type="button" id="date_start-btn">
											<em class="fa fa-calendar fa-fix"> </em>
										</button>
									</span>
								</div>
							</div>
							<div class="col-md-6">
								<label>{LANG.flying_code}</label> <input type="text" value="{ROW.flying_end.code}" class="form-control" name="flying_end[code]" />
							</div>
						</div>
					</div>
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading">
						<a href="javascript:void(0)" data-toggle="collapse" data-target="#panel-hotels-info">{LANG.hotels_info}</a>
					</div>
					<div class="panel-body collapse <!-- BEGIN: hotels_collapse_in -->in<!-- END: hotels_collapse_in -->" id="panel-hotels-info">
						<div id="hotelslist" class="m-bottom" data-numhotels="{NUMHOTELS}">
							<!-- BEGIN: hotels_info -->
							<div class="row m-bottom">
								<div class="col-md-14">
									<label>{LANG.hotels}</label> <select class="form-control" name="hotels[{HOTELS_INFO.index}][id]">
										<option value="">---{LANG.hotels_c}---</option>
										<!-- BEGIN: hotels -->
										<option value="{HOTELS.id}"{HOTELS.selected}>{HOTELS.title}</option>
										<!-- END: hotels -->
									</select>
								</div>
								<div class="col-md-8">
									<label>{LANG.hotels_time}</label> <input type="text" value="{HOTELS_INFO.time}" class="form-control hotels_time" name="hotels[{HOTELS_INFO.index}][time]" />
								</div>
								<div class="col-md-2">
									<label>&nbsp;</label>
									<button class="btn btn-default show" onclick="if(confirm(nv_is_del_confirm[0])) $(this).parent().parent().remove(); return false;">
										<em class="fa fa-trash-o fa-lg">&nbsp;</em>
									</button>
								</div>
							</div>
							<!-- END: hotels_info -->
						</div>
						<button id="addhotels" class="btn btn-primary btn-xs">{LANG.hotels_add}</button>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<a href="javascript:void(0)" data-toggle="collapse" data-target="#panel-guides">{LANG.guides}</a>
					</div>
					<!-- BEGIN: guides -->
					<div class="panel-body collapse <!-- BEGIN: guides_collapse_in -->in<!-- END: guides_collapse_in -->" id="panel-guides">
						<div class="table_guides" style="height: 300px; overflow: scroll; border: solid 1px #ddd; padding: 5px;">
							<table class="table table-striped table-bordered table-hover">
								<colgroup>
									<col class="w50" />
									<col width="150" />
									<col width="80" />
									<col class="w100" />
									<col />
									<col class="w100" />
									<col class="w100" />
								</colgroup>
								<thead>
									<tr class="filters">
										<th></th>
										<th><input type="text" class="form-control input-sm" placeholder="{LANG.last_name}"></th>
										<th><input type="text" class="form-control input-sm" placeholder="{LANG.first_name}"></th>
										<th class="text-center">{LANG.birthday}</th>
										<th class="text-center">{LANG.address}</th>
										<th class="text-center">{LANG.gender}</th>
										<th class="text-center">{LANG.phone}</th>
									</tr>
								</thead>
								<tbody>
									<!-- BEGIN: loop -->
									<tr class="pointer <!-- BEGIN: success -->success<!-- END: success -->">
										<td class="text-center"><input type="checkbox" name="guides[]" value="{GUIDES.id}" {GUIDES.checked} /></td>
										<td>{GUIDES.last_name}</td>
										<td>{GUIDES.first_name}</td>
										<td>{GUIDES.birthday}</td>
										<td>{GUIDES.address}</td>
										<td class="text-center">{GUIDES.gender}</td>
										<td>{GUIDES.phone}</td>
									</tr>
									<!-- END: loop -->
								</tbody>
							</table>
						</div>
					</div>
					<!-- END: guides -->
				</div>
			</div>
			<div class="col-md-6">
				<!-- BEGIN: block_cat -->
				<div class="panel panel-default">
					<div class="panel-heading">{LANG.groups}</div>
					<div class="panel-body" style="height: 200px; overflow: scroll;">
						<!-- BEGIN: loop -->
						<div class="row">
							<label><input type="checkbox" value="{BLOCKS.bid}" name="bids[]"{BLOCKS.checked}>{BLOCKS.title}</label>
						</div>
						<!-- END: loop -->
					</div>
				</div>
				<!-- END: block_cat -->
				
				<div class="panel panel-default">
					<div class="panel-heading">{LANG.keywords}</div>
					<div class="panel-body">
						<div class="message_body" style="overflow: auto">
							<div class="clearfix uiTokenizer uiInlineTokenizer">
								<div id="keywords" class="tokenarea">
									<!-- BEGIN: keywords -->
									<span class="uiToken removable" title="{KEYWORDS}"> {KEYWORDS} <input type="hidden" autocomplete="off" name="keywords[]" value="{KEYWORDS}" /> <a onclick="$(this).parent().remove();" class="remove uiCloseButton uiCloseButtonSmall" href="javascript:void(0);"></a> </span>
									<!-- END: keywords -->
								</div>
								<div class="uiTypeahead">
									<div class="wrap">
										<input type="hidden" class="hiddenInput" autocomplete="off" value="" />
										<div class="innerWrap">
											<input id="keywords-search" type="text" placeholder="{LANG.input_keyword_tags}" class="form-control textInput" style="width: 100%;" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading">{LANG.groups_view}</div>
					<div class="panel-body" style="height: 200px; overflow: scroll;">
						<!-- BEGIN: groups_view -->
						<label class="show"><input type="checkbox" name="groups_view[]" value="{GROUPS_VIEW.value}" {GROUPS_VIEW.checked} />{GROUPS_VIEW.title}</label>
						<!-- END: groups_view -->
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">{LANG.groups_comment}</div>
					<div class="panel-body" style="height: 200px; overflow: scroll;">
						<!-- BEGIN: groups_comment -->
						<label class="show"><input type="checkbox" name="groups_comment[]" value="{GROUPS_COMMENT.value}" {GROUPS_COMMENT.checked} />{GROUPS_COMMENT.title}</label>
						<!-- END: groups_comment -->
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">{LANG.extend}</div>
					<div class="panel-body">
						<label class="show"><input type="checkbox" name="allowed_rating" value="1" {ROW.allowed_rating_ck} />{LANG.allowed_rating}</label> <label class="show"><input type="checkbox" name="show_price" value="1" {ROW.show_price_ck} />{LANG.show_price}</label>
					</div>
				</div>
			</div>
		</div>
		<div class="text-center">
			<input class="btn btn-primary loading" name="submit" type="submit" id="submit" value="{LANG.save}" />
		</div>
	</form>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery/jquery.validate.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.validator-{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/tours_autoNumeric-1.9.41.js"></script>
<script src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/bootstrap-select/js/bootstrap-select.min.js"></script>
<script src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/bootstrap-select/js/i18n/defaults-vi_VN.min.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function() {
		$('form').validate();
		
		$('.flying_time').daterangepicker({
			timePicker : true,
			timePicker24Hour : true,
			drops : "up",
			singleDatePicker : true,
			autoUpdateInput: false,
			locale : {
				applyLabel : '{LANG.apply}',
				cancelLabel : '{LANG.delete}'
			}
		});
		
		$('.flying_time').on('apply.daterangepicker', function(ev, picker) {
		    $(this).val(picker.startDate.format('DD/MM/YYYY H:mm'));
		});
		
		$('.flying_time').on('cancel.daterangepicker', function(ev, picker) {
		    $(this).val('');
		});
		
		nv_apply_hotels_time();
		
		$('#catid').change(function(){
			nv_loadprice($(this).val(), {ROW.id});
		});
		
		$('.table_guides .pointer').click(function(){
			var checkbox = $(this).find('input');
			if(checkbox.is(':checked')){
				checkbox.prop('checked', false);
				$(this).removeClass('success');
			}
			else{
				checkbox.prop('checked', true);
				$(this).addClass('success');				
			}
		});
	});

	function nv_get_alias(id) {
		var title = strip_tags($("[name='title']").val());
		if (title != '') {
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name
					+ '&' + nv_fc_variable + '=content&nocache='
					+ new Date().getTime(), 'get_alias_title='
					+ encodeURIComponent(title), function(res) {
				$("#" + id).val(strip_tags(res));
			});
		}
		return false;
	}
	
	function nv_apply_hotels_time(){
		$('.hotels_time').daterangepicker({
			timePicker : true,
			timePicker24Hour : true,
			drops : "up",
			autoUpdateInput: false,
			locale : {
				applyLabel : '{LANG.apply}',
				cancelLabel : '{LANG.delete}'
			}
		});
		
		
		$('.hotels_time').on('apply.daterangepicker', function(ev, picker) {
		    $(this).val(picker.startDate.format('DD/MM/YYYY H:mm') + ' - ' + picker.endDate.format('DD/MM/YYYY H:mm'));
		});
		
		$('.hotels_time').on('cancel.daterangepicker', function(ev, picker) {
		    $(this).val('');
		});
	}
	
	function nv_loadprice(catid, row_id){
		$('#loadprice').html('<p class="text-center"><img src="' + nv_base_siteurl + 'assets/images/load_bar.gif" alt="Waiting..."/></p>').load( script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=loadprice&catid=' + catid + '&row_id=' + row_id );
	}
	
	$("#date_start").datepicker({
		dateFormat : "dd/mm/yy",
		changeMonth : true,
		changeYear : true,
		showOtherMonths : true,
	});

	$(".selectfile").click(function() {
		var area = "id_homeimgfile";
		var path = "{NV_UPLOADS_DIR}/{MODULE_UPLOAD}";
		var currentpath = "{CURRENTPATH}";
		var type = "image";
		nv_open_browse(script_name + "?" + nv_name_variable
				+ "=upload&popup=1&area=" + area + "&path="
				+ path + "&type=" + type + "&currentpath="
				+ currentpath, "NVImg", 850, 420,
				"resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
		return false;
	});

	//]]>
</script>

<!-- BEGIN: loadprice -->
<script>
	nv_loadprice($('#catid').val(), {ROW.id});
</script>
<!-- END: loadprice -->

<!-- BEGIN: auto_get_alias -->
<script>
	//<![CDATA[
	$("[name='title']").change(function() {
		nv_get_alias('id_alias');
	});
	//]]>
</script>
<!-- END: auto_get_alias -->

<script>
$(document).ready(function() {
	var numhotels = $('#hotelslist').data('numhotels');
	$('#addhotels').click(function(e) {
		e.preventDefault();
		var html = '';
		html += '<div class="row m-bottom">';
		html += '<div class="col-md-14">';
		html += '	<select class="form-control"';
		html += '		name="hotels['+numhotels+'][id]">';
		html += '		<option value="">---{LANG.hotels_c}---</option>';
		<!-- BEGIN: hotels_js -->
		html += '		<option value="{HOTELS.id}">{HOTELS.title}</option>';
		<!-- END: hotels_js -->
		html += '	</select>';
		html += '</div>';
		html += '<div class="col-md-8">';
		html += '	<input type="text"';
		html += '		value="" class="form-control hotels_time" name="hotels['+numhotels+'][time]" />';
		html += '</div>';
		html += '<div class="col-md-2">';
		html += '	<button class="btn btn-default" onclick="if(confirm(nv_is_del_confirm[0])) $(this).parent().parent().remove(); return false;">';
		html += '		<em class="fa fa-trash-o fa-lg">&nbsp;</em>';
		html += '	</button>';
		html += '</div>';
		html += '</div>';
		numhotels += 1;
		$('#hotelslist').append( html );
		nv_apply_hotels_time();
	});
});
</script>

<script>
	$(document).ready(function() {
		$('.selectpicker').selectpicker({
			iconBase: 'fa',
			tickIcon: 'fa-check'
		});

		$('.table_guides .btn-filter').click(function() {
			var $panel = $(this).parents('.table_guides'),
			    $filters = $panel.find('.filters input'),
			    $tbody = $panel.find('.table tbody');
			if ($filters.prop('disabled') == true) {
				$filters.prop('disabled', false);
				$filters.first().focus();
			} else {
				$filters.val('').prop('disabled', true);
				$tbody.find('.no-result').remove();
				$tbody.find('tr').show();
			}
		});

		$('.table_guides .filters input').keyup(function(e) {
			/* Ignore tab key */
			var code = e.keyCode || e.which;
			if (code == '9')
				return;
			/* Useful DOM data and selectors */
			var $input = $(this),
			    inputContent = $input.val().toLowerCase(),
			    $panel = $input.parents('.table_guides'),
			    column = $panel.find('.filters th').index($input.parents('th')),
			    $table = $panel.find('.table'),
			    $rows = $table.find('tbody tr');
			/* Dirtiest filter function ever ;) */
			var $filteredRows = $rows.filter(function() {
				var value = $(this).find('td').eq(column).text().toLowerCase();
				return value.indexOf(inputContent) === -1;
			});
			/* Clean previous no-result if exist */
			$table.find('tbody .no-result').remove();
			/* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
			$rows.show();
			$filteredRows.hide();
			/* Prepend no-result row if all rows are filtered */
			if ($filteredRows.length === $rows.length) {
				$table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="' + $table.find('.filters th').length + '">{LANG.error_no_result_found}</td></tr>'));
			}
		});
	});
</script>
<!-- END: main -->
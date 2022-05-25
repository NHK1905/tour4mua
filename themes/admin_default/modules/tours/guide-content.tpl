<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />

<!-- BEGIN: error -->
<div class="alert alert-warning">{ERROR}</div>
<!-- END: error -->
<div class="panel panel-default">
	<div class="panel-body">
		<form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
			<input type="hidden" name="id" value="{ROW.id}" />
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.fullname}</strong> <span class="red">(*)</span></label>
				<div class="col-sm-19 col-md-20">
					<div class="row">
						<div class="col-sm-12 col-md-12">
							<input class="form-control" type="text" name="last_name" value="{ROW.last_name}" required="required" oninvalid="setCustomValidity( nv_required )" oninput="setCustomValidity('')" placeholder="{LANG.last_name}" />
						</div>
						<div class="col-sm-12 col-md-12">
							<input class="form-control" type="text" name="first_name" value="{ROW.first_name}" required="required" oninvalid="setCustomValidity( nv_required )" oninput="setCustomValidity('')" placeholder="{LANG.first_name}" />
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.phone}</strong> <span class="red">(*)</span></label>
				<div class="col-sm-19 col-md-20">
					<input class="form-control" type="text" name="phone" value="{ROW.phone}" required="required" oninvalid="setCustomValidity( nv_required )" oninput="setCustomValidity('')" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.birthday}</strong></label>
				<div class="col-sm-19 col-md-20">
					<div class="input-group">
						<input class="form-control" type="text" name="birthday" value="{ROW.birthday}" id="birthday" pattern="^[0-9]{2,2}\/[0-9]{2,2}\/[0-9]{1,4}$" /> <span class="input-group-btn">
							<button class="btn btn-default" type="button" id="birthday-btn">
								<em class="fa fa-calendar fa-fix"> </em>
							</button>
						</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.address}</strong></label>
				<div class="col-sm-19 col-md-20">
					<input class="form-control" type="text" name="address" value="{ROW.address}" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-5 col-md-4 text-right"><strong>{LANG.gender}</strong></label>
				<div class="col-sm-19 col-md-20">
					<!-- BEGIN: gender -->
					<label><input type="radio" name="gender" value="{GENDER.key}" {GENDER.checked} />{GENDER.value}</label>
					<!-- END: gender -->
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.image}</strong></label>
				<div class="col-sm-19 col-md-20">
					<div class="input-group">
						<input class="form-control" type="text" name="image" value="{ROW.image}" id="id_image" /> <span class="input-group-btn">
							<button class="btn btn-default selectfile" type="button">
								<em class="fa fa-folder-open-o fa-fix">&nbsp;</em>
							</button>
						</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.guide_description}</strong></label>
				<div class="col-sm-19 col-md-20">{ROW.description}</div>
			</div>
			<div class="form-group" style="text-align: center">
				<input class="btn btn-primary" name="submit" type="submit" value="{LANG.save}" />
			</div>
		</form>
	</div>
</div>

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
</script>

<script type="text/javascript">
	//<![CDATA[
	$("#birthday").datepicker({
		dateFormat : "dd/mm/yy",
		changeMonth : true,
		changeYear : true,
		showOtherMonths : true,
	});

	$(".selectfile")
			.click(
					function() {
						var area = "id_image";
						var path = "{NV_UPLOADS_DIR}/{MODULE_UPLOAD}/images";
						var currentpath = "{NV_UPLOADS_DIR}/{MODULE_UPLOAD}/images";
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
<!-- END: main -->
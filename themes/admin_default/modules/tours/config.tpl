<!-- BEGIN: main -->
<form action="" method="post" class="form-horizontal tour-config">
	<div class="panel panel-default">
		<div class="panel-heading">{LANG.config_system}</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.config_home_type}</strong></label>
				<div class="col-sm-20">
					<select class="form-control" name="home_type">
						<!-- BEGIN: home_type -->
						<option value="{HOME_TYPE.index}"{HOME_TYPE.selected}>{HOME_TYPE.value}</option>
						<!-- END: home_type -->
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.config_per_page}</strong></label>
				<div class="col-sm-20">
					<input type="text" name="per_page" value="{DATA.per_page}" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.config_home_image_size}</strong></label>
				<div class="col-sm-20">
					<div class="row">
						<div class="col-xs-11">
							<input type="text" name="home_image_w" class="form-control" value="{DATA.home_image_size_w}" />
						</div>
						<div class="col-xs-2 text-center text-middle">X</div>
						<div class="col-xs-11">
							<input type="text" name="home_image_h" class="form-control" value="{DATA.home_image_size_h}" />
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.config_title_lenght}</strong></label>
				<div class="col-sm-20">
					<input type="number" name="title_lenght" value="{DATA.title_lenght}" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.config_money_unit}</strong></label>
				<div class="col-sm-20">
					<select name="money_unit" class="form-control">
						<!-- BEGIN: money_loop -->
						<option value="{DATAMONEY.value}"{DATAMONEY.selected}>{DATAMONEY.title}</option>
						<!-- END: money_loop -->
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.config_structupload}</strong></label>
				<div class="col-sm-20">
					<select class="form-control" name="structure_upload" id="structure_upload">
						<!-- BEGIN: structure_upload -->
						<option value="{STRUCTURE_UPLOAD.key}"{STRUCTURE_UPLOAD.selected}>{STRUCTURE_UPLOAD.title}</option>
						<!-- END: structure_upload -->
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.config_no_image}</strong></label>
				<div class="col-sm-19 col-md-20">
					<div class="input-group">
						<input class="form-control" type="text" name="no_image" value="{DATA.no_image}" id="id_image" /> <span class="input-group-btn">
							<button class="btn btn-default selectfile" type="button">
								<em class="fa fa-folder-open-o fa-fix">&nbsp;</em>
							</button>
						</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 text-right"><strong>{LANG.config_groups_booking_sendmail}</strong></label>
				<div class="col-sm-20" style="height: 200px; overflow: scroll; border: solid 1px #ddd; padding: 10px">
					<!-- BEGIN: booking_groups_sendmail -->
					<label class="show"><input type="checkbox" name="booking_groups_sendmail[]" value="{GROUPS_BOOKING_SENDMAIL.value}" {GROUPS_BOOKING_SENDMAIL.checked} />{GROUPS_BOOKING_SENDMAIL.title}</label>
					<!-- END: booking_groups_sendmail -->
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.default_place_start}</strong></label>
				<div class="col-sm-20">{LOCATION}</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 text-right"><strong>{LANG.config_coupons}</strong></label>
				<div class="col-sm-20">
					<label><input type="checkbox" name="coupons" value="1" {DATA.ck_coupons} />{LANG.config_coupons_note}</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 text-right"><strong>{LANG.tags_alias}</strong></label>
				<div class="col-sm-20">
					<label><input type="checkbox" value="1" name="tags_alias" {DATA.ck_tags_alias}/>{LANG.tags_alias_note}</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 text-right"><strong>{LANG.auto_tags}</strong></label>
				<div class="col-sm-20">
					<label><input type="checkbox" value="1" name="auto_tags" {DATA.ck_auto_tags}/>{LANG.auto_tags_note}</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 text-right"><strong>{LANG.tags_remind}</strong></label>
				<div class="col-sm-20">
					<label><input type="checkbox" value="1" name="tags_remind" {DATA.ck_tags_remind}/>{LANG.tags_remind_note}</label>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">{LANG.tour_info}</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-4 text-right"><strong>{LANG.config_allow_auto_code}</strong></label>
				<div class="col-sm-20">
					<label><input type="checkbox" name="allow_auto_code" value="1" {DATA.ck_allow_auto_code} />{LANG.config_allow_auto_code_note}</label>
				</div>
			</div>
			<div class="form-group" id="format_code"{DATA.style_format_code}>
				<label class="col-sm-4 control-label"><strong>{LANG.config_format_code}</strong></label>
				<div class="col-sm-20">
					<input type="text" name="format_code" value="{DATA.format_code}" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 text-right"><strong>{LANG.config_date_start_method}</strong></label>
				<div class="col-sm-20">
					<!-- BEGIN: date_start_method -->
					<label><input type="radio" name="date_start_method" value="{DATE_START_METHOD.index}" {DATE_START_METHOD.checked} />{DATE_START_METHOD.value}</label>&nbsp;&nbsp;&nbsp;
					<!-- END: date_start_method -->
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.config_contact_info}</strong></label>
				<div class="col-sm-20">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active"><a href="#contact-info" aria-controls="contact-info" role="tab" data-toggle="tab">{LANG.config_contact}</a></li>
						<li role="presentation"><a href="#note-content" aria-controls="note-content" role="tab" data-toggle="tab">{LANG.note_content}</a></li>
					</ul>

					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="contact-info">{DATA.contact_info}</div>
						<div role="tabpanel" class="tab-pane" id="note-content">{DATA.note_content}</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.config_age_config}</strong></label>
				<div class="col-sm-20">
					<table class="table table-striped table-bordered table-hover" id="table-age" data-numage="{NUMAGE}">
						<thead>
							<th>{LANG.config_age_config_name}</th>
							<th>{LANG.config_age_config_from}</th>
							<th>{LANG.config_age_config_to}</th>
							<th class="text-center">{LANG.config_age_config_price_base}</th>
						</thead>
						<tbody>
							<!-- BEGIN: age_config -->
							<tr>
								<td><input type="text" class="form-control" name="age_config[{AGE_CONFIG.index}][name]" value="{AGE_CONFIG.name}" /></td>
								<td><input type="number" class="form-control" name="age_config[{AGE_CONFIG.index}][from]" value="{AGE_CONFIG.from}" /></td>
								<td><input type="number" class="form-control" name="age_config[{AGE_CONFIG.index}][to]" value="{AGE_CONFIG.to}" /></td>
								<td class="text-center"><input type="radio" name="price_base" value="{AGE_CONFIG.index}" {AGE_CONFIG.checked} /></td>
							</tr>
							<!-- END: age_config -->
						</tbody>
						<tfoot>
							<tr>
								<td colspan="4"><button class="btn btn-primary btn-xs" id="btn-addage">{LANG.addage}</button></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">{LANG.config_booking}</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-4 text-right"><strong>{LANG.config_booking_type}</strong></label>
				<div class="col-sm-20">
					<!-- BEGIN: booking_type -->
					<label><input type="radio" value="{BOOKING_TYPE.index}" name="booking_type" {BOOKING_TYPE.checked} />{BOOKING_TYPE.value}</label>&nbsp;&nbsp;&nbsp;
					<!-- END: booking_type -->
				</div>
			</div>
			<div id="booking_groups"{DATA.style_booking_groups}>
				<div class="form-group">
					<label class="col-sm-4 text-right"><strong>{LANG.config_booking_groups}</strong></label>
					<div class="col-sm-20" style="height: 200px; overflow: scroll; border: solid 1px #ddd; padding: 10px">
						<!-- BEGIN: booking_groups -->
						<label class="show"><input type="checkbox" name="booking_groups[]" value="{GROUPS_BOOKING.value}" {GROUPS_BOOKING.checked} />{GROUPS_BOOKING.title}</label>
						<!-- END: booking_groups -->
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><strong>{LANG.config_format_booking_code}</strong></label>
					<div class="col-sm-20">
						<input type="text" name="format_booking_code" value="{DATA.format_booking_code}" class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 text-right"><strong>{LANG.config_booking_sendmail}</strong></label>
					<div class="col-sm-20">
						<label><input type="checkbox" name="booking_sendmail" value="1" class="form-control" {DATA.ck_booking_sendmail} />{LANG.config_booking_sendmail_note}</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 text-right"><strong>{LANG.config_booking_price_method}</strong></label>
					<div class="col-sm-20">
						<!-- BEGIN: price_config -->
						<label><input type="radio" name="booking_price_method" value="{PRICE_METHOD.index}" {PRICE_METHOD.checked} />{PRICE_METHOD.value}</label>&nbsp;&nbsp;&nbsp;
						<!-- END: price_config -->
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 text-right"><strong>{LANG.config_rule_content}</strong></label>
					<div class="col-sm-20">{DATA.rule_content}</div>
				</div>
			</div>
		</div>
	</div>
	<div class="text-center">
		<input type="submit" class="btn btn-primary" value="{LANG.save}" name="savesetting" />
	</div>
</form>
<script type="text/javascript">
	$(".selectfile")
			.click(
					function() {
						var area = "id_image";
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

	$('input[name="allow_auto_code"]').change(function() {
		$('#format_code').toggle();
	});

	$('input[name="booking_type"]').change(function() {
		if ($(this).val() == 2) {
			$('#booking_groups').show();
		} else {
			$('#booking_groups').hide();
		}
	});

	var numage = $('#table-age').data('numage');
	$('#btn-addage')
			.click(
					function(e) {
						e.preventDefault();
						var html = '';
						html += '<tr>';
						html += '	<td><input type="text" class="form-control" name="age_config[' + numage + '][name]" value="" /></td>';
						html += '	<td><input type="number" class="form-control" name="age_config[' + numage + '][from]" value="" /></td>';
						html += '	<td><input type="number" class="form-control" name="age_config[' + numage + '][to]" value="" /></td>';
						html += '	<td class="text-center"><input type="radio" name="price_base" value="' + numage + '" /></td>';
						html += '</tr>';
						$('table#table-age tr:last').before(html);
						numage += 1;
					});
</script>
<!-- BEGIN: main -->
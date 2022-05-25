<!-- BEGIN: main -->

<div class="well">
	<form class="form-inline" action="{NV_BASE_ADMINURL}index.php" method="get">
		<input type="hidden" name="{NV_LANG_VARIABLE}"  value="{NV_LANG_DATA}" />
		<input type="hidden" name="{NV_NAME_VARIABLE}"  value="{MODULE_NAME}" />
		<input type="hidden" name="{NV_OP_VARIABLE}"  value="{OP}" />
		<input class="form-control" type="text" value="{Q}" name="q" maxlength="255" placeholder="{LANG.keywords}" />&nbsp;
		<input class="btn btn-primary" type="submit" value="{LANG.search_submit}" />
		<a class="btn btn-success" href="{COUPONS_ADD}">{LANG.coupons_add}</a>
	</form>
</div>

<form class="form-inline" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
			<colgroup>
				<col class="w50" />
				<col />
				<col width="130" />
				<col width="120" />
				<col span="3" width="130" />
				<col class="w100" />
				<col class="w150" />
			</colgroup>
			<thead>
				<tr>
					<th class="text-center"><input name="check_all[]" type="checkbox" value="yes" onclick="nv_checkAll(this.form, 'idcheck[]', 'check_all[]',this.checked);"></th>
					<th>{LANG.title}</th>
					<th>{LANG.coupons}</th>
					<th>{LANG.coupons_discount}</th>
					<th>{LANG.begin_time}</th>
					<th>{LANG.end_time}</th>
					<th class="text-center">{LANG.status}</th>
					<th class="text-center">{LANG.active}</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<!-- BEGIN: generate_page -->
			<tfoot>
				<tr>
					<td colspan="8">{NV_GENERATE_PAGE}</td>
				</tr>
			</tfoot>
			<!-- END: generate_page -->
			<tbody>
				<!-- BEGIN: loop -->
				<tr>
					<td class="text-center"><input type="checkbox" onclick="nv_UncheckAll(this.form, 'idcheck[]', 'check_all[]', this.checked);" value="{VIEW.id}" name="idcheck[]"></td>
					<td> <a href="{VIEW.link_view}" title="{VIEW.title}">{VIEW.title}</a> </td>
					<td> {VIEW.code} </td>
					<td> {VIEW.discount}{VIEW.discount_text} </td>
					<td> {VIEW.date_start} </td>
					<td> {VIEW.date_end} </td>
					<td class="text-center"> {VIEW.status} </td>
					<td class="text-center"> <input type="checkbox" name="status" id="change_status_{VIEW.id}" value="{VIEW.id}" {VIEW.status_ck} onclick="nv_change_status({VIEW.id});" /> </td>
					<td class="text-center"><i class="fa fa-edit fa-lg">&nbsp;</i> <a href="{VIEW.link_edit}">{LANG.edit}</a> - <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="{VIEW.link_delete}" onclick="return confirm(nv_is_del_confirm[0]);">{LANG.delete}</a></td>
				</tr>
				<!-- END: loop -->
			</tbody>
		</table>
	</div>
</form>

<script>
function nv_change_status(id) {
	var new_status = $('#change_status_' + id).is(':checked') ? true : false;
	if (confirm(nv_is_change_act_confirm[0])) {
		var nv_timer = nv_settimeout_disable('change_status_' + id, 5000);
		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=coupons&nocache=' + new Date().getTime(), 'change_status=1&id=' + id, function(res) {
			var r_split = res.split('_');
			if (r_split[0] != 'OK') {
				alert(nv_is_change_act_confirm[2]);
			}
		});
	}
	else{
		$('#change_status_' + id).prop('checked', new_status ? false : true );
	}
	return;
}
</script>
<!-- END: main -->
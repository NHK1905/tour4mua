<!-- BEGIN: main -->
<!-- BEGIN: data -->
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th width="10px" class="text-center">&nbsp;</th>
			<th>{LANG.money_name}</th>
			<th>{LANG.currency}</th>
			<th>{LANG.exchange}</th>
			<th>{LANG.round}</th>
			<th width="120px" class="text-center">{LANG.function}</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="6"><i class="fa fa-check-square-o">&nbsp;</i><a href="#" id="checkall">{LANG.prounit_select}</a>&nbsp;&nbsp;<i class="fa fa-square-o">&nbsp;</i><a href="#" id="uncheckall">{LANG.prounit_unselect}</a>&nbsp;&nbsp;<i class="fa fa-trash-o">&nbsp;</i><a href="#" id="delall">{LANG.prounit_del_select}</a></td>
		</tr>
	</tfoot>
	<tbody>
		<!-- BEGIN: row -->
		<tr>
			<td><input type="checkbox" class="ck" value="{ROW.id}" /></td>
			<td>{ROW.code}</td>
			<td>{ROW.currency}</td>
			<td>1 {ROW.code} = {ROW.exchange} {MONEY_UNIT}</td>
			<td>{ROW.round}</td>
			<td class="text-center"><i class="fa fa-edit">&nbsp;</i><a href="{ROW.link_edit}" title="">{LANG.edit}</a>&nbsp; <i class="fa fa-trash-o">&nbsp;</i><a href="{ROW.link_del}" class="delete" title="">{LANG.delete}</a></td>
		</tr>
		<!-- END: row -->
	</tbody>
</table>
<script type='text/javascript'>
	$(function() {
		$('#checkall').click(function() {
			$('input:checkbox').each(function() {
				$(this).attr('checked', 'checked');
			});
		});
		$('#uncheckall').click(function() {
			$('input:checkbox').each(function() {
				$(this).removeAttr('checked');
			});
		});
		$('#delall').click(function() {
			if (confirm("{LANG.prounit_del_confirm}")) {
				var listall = [];
				$('input.ck:checked').each(function() {
					listall.push($(this).val());
				});
				if (listall.length < 1) {
					alert("{LANG.prounit_del_no_items}");
					return false;
				}
				$.ajax({
					type : 'POST',
					url : '{URL_DEL}',
					data : 'listall=' + listall,
					success : function(data) {
						window.location = '{URL_DEL_BACK}';
					}
				});
			}
		});
		$('a.delete').click(function(event) {
			event.preventDefault();
			if (confirm("{LANG.prounit_del_confirm}")) {
				var href = $(this).attr('href');
				$.ajax({
					type : 'POST',
					url : href,
					data : 'delete=1',
					success : function(data) {
						window.location = '{URL_DEL_BACK}';
					}
				});
			}
		});
	});
</script>
<!-- END: data -->

<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->

<form class="form-horizontal" action="" method="post">
	<div class="panel panel-default">
		<div class="panel-body">
			<input name="savecat" type="hidden" value="1" /> <input type="hidden" value="{DATA.id}" name="id" />
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.money_name}</strong> <span class="red">*</span></label>
				<div class="col-sm-20">
					<select class="form-control" name="code">
						<!-- BEGIN: money -->
						<option value="{DATAMONEY.value}"{DATAMONEY.selected}>{DATAMONEY.title}</option>
						<!-- END: money -->
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.currency}</strong> <span class="red">*</span></label>
				<div class="col-sm-20">
					<input class="form-control" name="currency" type="text" value="{DATA.currency}" maxlength="255" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.exchange}</strong> <span class="red">*</span></label>
				<div class="col-sm-20">
					<input class="form-control" name="exchange" type="text" value="{DATA.exchange}" maxlength="255" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.round}</strong> <span class="red">*</span></label>
				<div class="col-sm-20">
					<select class="form-control" name="round">
						<!-- BEGIN: round -->
						<option value="{ROUND.round1}"{ROUND.selected}>{ROUND.round2}</option>
						<!-- END: round -->
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><strong>{LANG.money_number_format}</strong> <span class="red">*</span></label>
				<div class="col-sm-20">
					<div class="row">
						<div class="col-sm-4 text-middle">{LANG.money_number_format_dec_point}</div>
						<div class="col-sm-8">
							<input class="form-control" name="dec_point" type="text" value="{DATA.dec_point}" maxlength="1" />
						</div>
						<div class="col-sm-4 text-middle">{LANG.money_number_format_thousands_sep}</div>
						<div class="col-sm-8">
							<input class="form-control" name="thousands_sep" type="text" value="{DATA.thousands_sep}" maxlength="1" />
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<div class="text-center">
		<input class="btn btn-primary" name="submit" type="submit" value="{LANG.save}" />
	</div>
</form>

<!-- END: main -->
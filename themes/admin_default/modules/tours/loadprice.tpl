<!-- BEGIN: main -->
<input type="hidden" id="row_id" value="{ROW_ID}" />
<input type="hidden" id="cat_id" value="{CAT_ID}" />

<div id="loadprice">
	<!-- BEGIN: price_method_0 -->
	<div class="row">
		<div class="form-group">
			<label class="col-sm-5 col-md-5 control-label"><strong>{LANG.tour_price}</strong> <span class="red">(*)</span></label>
			<div class="col-sm-19 col-md-19">
				<input type="text" name="price_config[price][0]" value="{PRICE}" class="form-control price" <!-- BEGIN: required -->required="required"<!-- END: required --> />
			</div>
		</div>
		<!-- BEGIN: subprice -->
		<!-- BEGIN: loop -->
		<div class="form-group">
			<label class="col-sm-5 col-md-5 control-label pointer"><strong data-toggle="tooltip" data-placement="top" data-original-title="{SUBPRICE.title}">{SUBPRICE.title_clean}</strong></label>
			<div class="col-sm-19 col-md-19">
				<input type="text" name="price_config[subprice][{SUBPRICE.id}]" value="{SUBPRICE.price}" class="form-control price" />
			</div>
		</div>
		<!-- END: loop -->
		<!-- END: subprice -->
	</div>
	<!-- END: price_method_0 -->
	
	<!-- BEGIN: price_method_1 -->
	<table class="table">
		<thead>
			<th>&nbsp;</th>
			<!-- BEGIN: title -->
			<th class="text-center">{TITLE.name}</th>
			<!-- END: title -->
		</thead>
		<tbody>
			<tr>
				<td><strong>{LANG.tour_price}</strong> <span class="red">(*)</span></td>
				<!-- BEGIN: price -->
				<td><input type="text" name="price_config[price][{KEY}]" value="{PRICE}" class="form-control price input_{KEY} <!-- BEGIN: price_base -->price_base<!-- END: price_base -->" <!-- BEGIN: required -->required="required"<!-- END: required --> /></td>
				<!-- END: price -->
			</tr>
			<!-- BEGIN: subprice -->
			<tr>
				<td>{SUBPRICE.title}</td>
				<!-- BEGIN: loop -->
				<td><input type="text" name="price_config[subprice][{SUBPRICE.id}][{SUBPRICE.key}]" value="{SUBPRICE.price}" class="form-control price" /></td>
				<!-- END: loop -->
			</tr>
			<!-- END: subprice -->
		</tbody>
	</table>
	<!-- END: price_method_1 -->
	
	<!-- BEGIN: price_method_2 -->
	<table class="table">
		<thead>
			<th></th>
			<th>{LANG.tour_vietnam}</th>
			<th>{LANG.tour_vietkieu}</th>
			<th>{LANG.tour_nuocngoai}</th>
		</thead>
		<tbody>
			<!-- BEGIN: price -->
			<tr>
				<td>{TITLE.name}</td>
				<!-- BEGIN: loop -->
				<td><input type="text" name="price_config[price][{KEY.j}][{KEY.i}]" data-col="{KEY.i}" value="{PRICE}" class="form-control price input_{KEY.j}_{KEY.i} <!-- BEGIN: price_base -->price_base<!-- END: price_base -->" /></td>
				<!-- END: loop -->
			</tr>
			<!-- END: price -->
	
			<!-- BEGIN: subprice -->
			<tr>
				<td><label class="pointer" data-toggle="tooltip" data-placement="top" data-original-title="{SUBPRICE.title}">{SUBPRICE.title_clean}</label></td>
				<!-- BEGIN: loop -->
				<td><input type="text" name="price_config[subprice][{SUBPRICE.id}][{SUBPRICE.key}]" value="{SUBPRICE.price}" class="form-control price" /></td>
				<!-- END: loop -->
			</tr>
			<!-- END: subprice -->
		</tbody>
	</table>
	<!-- END: price_method_2 -->
</div>

<div class="row">
	<div class="col-md-2 text-middle">
		<strong>{LANG.discounts}</strong>
	</div>
	<div class="col-md-14">
		<select class="form-control" name="discounts_id">
			<option value="0">---{LANG.discounts_c}---</option>
			<!-- BEGIN: discounts -->
			<option value="{DISCOUNTS.did}"{DISCOUNTS.selected}>{DISCOUNTS.title}</option>
			<!-- END: discounts -->
		</select>
	</div>
	<div class="col-md-3 text-middle text-right">
		<strong>{LANG.money}</strong>
	</div>
	<div class="col-md-5">
		<select class="form-control" name="money_unit">
			<!-- BEGIN: money_unit -->
			<option value="{MONEY.code}"{MONEY.selected}>{MONEY.currency} ({MONEY.code})</option>
			<!-- END: money_unit -->
		</select>
	</div>
</div>

<script>
	$(document).ready(function() {
		var Options = {
			aSep : '{NUMBER_FORMAT.aSep}',
			aDec : '{NUMBER_FORMAT.aDec}',
			vMin : '0',
			vMax : '999999999'
		};
		$('.price').autoNumeric('init', Options);
		$('.price').bind('blur focusout keypress keyup', function() {
			$(this).autoNumeric('get', Options);
		});
		
		$('.price_base').blur(function(){
			var base_price = $(this).val();
			var col = $(this).data('col');
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=loadprice&nocache=' + new Date().getTime(),
				data: 'base_get_price=1&base_price=' + base_price + '&row_id=' + $('#row_id').val() + '&catid=' + $('#catid').val() + '&col=' + col,
				success: function(json){
					$.each( json, function( key, val ) {
						$('.input_' + key).val(val);
					});
					$('.price').autoNumeric('update', Options);
				}
			});
		});
	});

	$('[data-toggle="tooltip"]').tooltip();
</script>

<!-- END: main -->
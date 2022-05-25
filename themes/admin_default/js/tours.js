/**
 * @Project NUKEVIET 4.x
 * @Author hongoctrien (contact@mynukeviet.net)
 * @Copyright (C) 2016 hongoctrien. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sun, 08 May 2016 07:42:57 GMT
 */

$(document).ready(function() {
	$('.loading').click(function() {
		if($.validator){
			var valid = $(this).closest('form').valid();
			if(valid){
				$('body').append('<div class="ajax-load-qa"></div>');
			}
		}else{
			var valid = $(this).closest('form').find('input:invalid').length;
			if(valid == 0){
				$('body').append('<div class="ajax-load-qa"></div>');
			}
		}
	});
	
	$('#change_payment_status').click(function(){
		if (confirm(CFG.booking_payment_confirm)) {
			var status = $(this).data('status');
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&'
					+ nv_fc_variable + '=booking-detail&nocache=' + new Date().getTime(),
					'change_payment_status=1&booking_id=' + CFG.booking_id + '&status=' + status, function(res) {
						var r_split = res.split('_');
						if (r_split[0] != 'OK') {
							$('.ajax-load-qa').remove();
							alert(nv_is_change_act_confirm[2]);
						}else{
							window.location.href = CFG.selfurl;
						}
						return;
					});
		}else{
			$('.ajax-load-qa').remove();
		}
	});
	
	if($('#keywords').length > 0){
		$("#keywords-search").bind("keydown", function(event) {
			if (event.keyCode === $.ui.keyCode.TAB && $(this).data("ui-autocomplete").menu.active) {
				event.preventDefault();
			}

			if (event.keyCode == 13) {
				var keywords_add = $("#keywords-search").val();
				keywords_add = trim(keywords_add);
				if (keywords_add != '') {
					nv_add_element('keywords', keywords_add, keywords_add);
					$(this).val('');
				}
				return false;
			}

		}).autocomplete({
			source : function(request, response) {
				$.getJSON(script_name + "?" + nv_name_variable + "=" + nv_module_name + "&" + nv_fc_variable + "=tagsajax", {
					term : extractLast(request.term)
				}, response);
			},
			search : function() {
				// custom minLength
				var term = extractLast(this.value);
				if (term.length < 2) {
					return false;
				}
			},
			focus : function() {
				//no action
			},
			select : function(event, ui) {
				// add placeholder to get the comma-and-space at the end
				if (event.keyCode != 13) {
					nv_add_element('keywords', ui.item.value, ui.item.value);
					$(this).val('');
				}
				return false;
			}
		});

		$("#keywords-search").blur(function() {
			// add placeholder to get the comma-and-space at the end
			var keywords_add = $("#keywords-search").val();
			keywords_add = trim(keywords_add);
			if (keywords_add != '') {
				nv_add_element('keywords', keywords_add, keywords_add);
				$(this).val('');
			}
			return false;
		});
		$("#keywords-search").bind("keyup", function(event) {
			var keywords_add = $("#keywords-search").val();
			if (keywords_add.search(',') > 0) {
				keywords_add = keywords_add.split(",");
				for ( i = 0; i < keywords_add.length; i++) {
					var str_keyword = trim(keywords_add[i]);
					if (str_keyword != '') {
						nv_add_element('keywords', str_keyword, str_keyword);
					}
				}
				$(this).val('');
			}
			return false;
		});	
	}
	
	$('#start_date_method').change(function(){
		$('.date_start_method').hide();
		$('#date_start_method_' + $(this).val()).show();
	});
	
	$('input[name="show_price"]').change(function(){
		if($(this).is(':checked')){
			$('#loadprice').find('input.price').each(function(){
				$(this).rules('add', 'required');
			});
		}else{
			$('#loadprice').find('input.price').each(function(){
				$(this).rules('remove', 'required');
				$(this).prop('required', false);
			});
		}
	});
});

function split(val) {
	return val.split(/,\s*/);
}

function extractLast(term) {
	return split(term).pop();
}

function nv_add_element(idElment, key, value) {
	var html = "<span title=\"" + value + "\" class=\"uiToken removable\">" + value + "<input type=\"hidden\" value=\"" + key + "\" name=\"" + idElment + "[]\" autocomplete=\"off\"><a onclick=\"$(this).parent().remove();\" href=\"javascript:void(0);\" class=\"remove uiCloseButton uiCloseButtonSmall\"></a></span>";
	$("#" + idElment).append(html);
	return false;
}

function nv_chang_cat(catid, mod) {
	var nv_timer = nv_settimeout_disable('id_' + mod + '_' + catid, 5000);
	var new_vid = $('#id_' + mod + '_' + catid).val();
	$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&'
			+ nv_fc_variable + '=change_cat&nocache=' + new Date().getTime(),
			'catid=' + catid + '&mod=' + mod + '&new_vid=' + new_vid, function(
					res) {
				var r_split = res.split('_');
				if (r_split[0] != 'OK') {
					alert(nv_is_change_act_confirm[2]);
				}
				clearTimeout(nv_timer);
				return;
			});
	return;
}

function nv_guide_action(action, url_action, del_confirm_no_post) {
	var listall = [];
	$('input.post:checked').each(function() {
		listall.push($(this).val());
	});

	if (listall.length < 1) {
		alert(del_confirm_no_post);
		return false;
	}

	if (action == 'delete') {
		if (confirm(nv_is_del_confirm[0])) {
			$.ajax({
				type : 'POST',
				url : url_action,
				data : 'delete_list=1&listall=' + listall,
				success : function(data) {
					var r_split = data.split('_');
					if (r_split[0] == 'OK') {
						window.location.href = window.location.href;
					} else {
						alert(nv_is_del_confirm[2]);
					}
				}
			});
		}
	}
	return false;
}

function nv_delete_other_images( i ){
	if (confirm(nv_is_del_confirm[0])) {
		$('#other-image-div-' + i).slideUp().promise().done(function() {
		    $(this).remove();
		});
	}
}

function nv_delete_other_images_tmp( path, thumb, i ){
	if (confirm(nv_is_del_confirm[0])) {
		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=images&nocache=' + new Date().getTime(), 'delete_other_images_tmp=1&path=' + path + '&thumb=' + thumb, function(res) {
			if (res != 'OK') {
				alert(nv_is_del_confirm[2]);
			}
			else{
				$('#other-image-div-' + i).slideUp();
			}
		});
	}
}

function nv_del_block_cat(bid) {
	if (confirm(nv_is_del_confirm[0])) {
		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=groups&nocache=' + new Date().getTime(), 'del_block_cat=1&bid=' + bid, function(res) {
			var r_split = res.split('_');
			if (r_split[0] == 'OK') {
				nv_show_list_block_cat();
			} else if (r_split[0] == 'ERR') {
				alert(r_split[1]);
			} else {
				alert(nv_is_del_confirm[2]);
			}
		});
	}
	return false;
}

function nv_chang_block_cat(bid, mod) {
	var nv_timer = nv_settimeout_disable('id_' + mod + '_' + bid, 5000);
	var new_vid = $('#id_' + mod + '_' + bid).val();
	$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=chang_block_cat&nocache=' + new Date().getTime(), 'bid=' + bid + '&mod=' + mod + '&new_vid=' + new_vid, function(res) {
		var r_split = res.split('_');
		if (r_split[0] != 'OK') {
			alert(nv_is_change_act_confirm[2]);
		}
		clearTimeout(nv_timer);
		nv_show_list_block_cat();
	});
	return;
}

function nv_show_list_block_cat() {
	if (document.getElementById('module_show_list')) {
		$('#module_show_list').load(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=list_block_cat&nocache=' + new Date().getTime());
	}
	return;
}

function nv_chang_block(bid, id, mod) {
	if (mod == 'delete' && !confirm(nv_is_del_confirm[0])) {
		return false;
	}
	var nv_timer = nv_settimeout_disable('id_weight_' + id, 5000);
	var new_vid = $('#id_weight_' + id).val();
	$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=change_block&nocache=' + new Date().getTime(), 'id=' + id + '&bid=' + bid + '&mod=' + mod + '&new_vid=' + new_vid, function(res) {
		nv_chang_block_result(res);
	});
	return;
}

function nv_chang_block_result(res) {
	var r_split = res.split('_');
	if (r_split[0] != 'OK') {
		alert(nv_is_change_act_confirm[2]);
	}
	var bid = parseInt(r_split[1]);
	nv_show_list_block(bid);
	return;
}

function nv_show_list_block(bid) {
	if (document.getElementById('module_show_list')) {
		$('#module_show_list').load(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=list_block&bid=' + bid + '&nocache=' + new Date().getTime());
	}
	return;
}

function nv_del_block_list(oForm, bid, del_confirm_no_post) {
	var del_list = '';
	var fa = oForm['idcheck[]'];
	if (fa.length) {
		for (var i = 0; i < fa.length; i++) {
			if (fa[i].checked) {
				del_list = del_list + ',' + fa[i].value;
			}
		}
	} else {
		if (fa.checked) {
			del_list = del_list + ',' + fa.value;
		}
	}

	if (del_list != '') {
		if (confirm(nv_is_del_confirm[0])) {
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=change_block&nocache=' + new Date().getTime(), 'del_list=' + del_list + '&bid=' + bid, function(res) {
				nv_chang_block_result(res);
			});
		}
	}
	else{
		alert(del_confirm_no_post);
	}
}

function nv_chang_cat(catid, mod) {
	var nv_timer = nv_settimeout_disable('id_' + mod + '_' + catid, 5000);
	var new_vid = $('#id_' + mod + '_' + catid).val();
	$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=change_cat&nocache=' + new Date().getTime(), 'catid=' + catid + '&mod=' + mod + '&new_vid=' + new_vid, function(res) {
		var r_split = res.split('_');
		if (r_split[0] != 'OK') {
			alert(nv_is_change_act_confirm[2]);
		}
		clearTimeout(nv_timer);
		return;
	});
	return;
}

function nv_booking_action(action, url_action, del_confirm_no_post) {
	var listall = [];
	$('input.post:checked').each(function() {
		listall.push($(this).val());
	});

	if (listall.length < 1) {
		alert(del_confirm_no_post);
		return false;
	}

	if (action == 'delete') {
		if (confirm(nv_is_del_confirm[0])) {
			$.ajax({
				type : 'POST',
				url : url_action,
				data : 'delete_list=1&listall=' + listall,
				success : function(data) {
					var r_split = data.split('_');
					if (r_split[0] == 'OK') {
						window.location.href = window.location.href;
					} else {
						alert(nv_is_del_confirm[2]);
					}
				}
			});
		}
	}
	return false;
}

function nv_chang_pays(payid, object, url_change, url_back) {
	var value = $(object).val();
	$.ajax({
		type : 'POST',
		url : url_change,
		data : 'oid=' + payid + '&w=' + value,
		success : function(data) {
			window.location = url_back;
		}
	});
	return;
}

function ChangeActive(idobject, url_active) {
	var id = $(idobject).attr('id');
	$.ajax({
		type : 'POST',
		url : url_active,
		data : 'id=' + id,
		success : function(data) {
			alert(data);
		}
	});
}

function nv_tour_action( action, url_action, del_confirm_no_post )
{
	var listall = [];
	$('input.post:checked').each(function() {
		listall.push($(this).val());
	});

	if (listall.length < 1) {
		alert( del_confirm_no_post );
		return false;
	}

	if( action == 'delete_list_id' )
	{
		if (confirm(nv_is_del_confirm[0])) {
			$.ajax({
				type : 'POST',
				url : url_action,
				data : 'delete_list=1&listall=' + listall,
				success : function(data) {
					var r_split = data.split('_');
					if( r_split[0] == 'OK' ){
						window.location.href = window.location.href;
					}
					else{
						alert( nv_is_del_confirm[2] );
					}
				}
			});
		}
	}

	return false;
}
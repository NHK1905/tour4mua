/**
 * @Project NUKEVIET 4.x
 * @Author hongoctrien (contact@mynukeviet.net)
 * @Copyright (C) 2016 hongoctrien. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sun, 08 May 2016 07:42:57 GMT
 */

$(document).ready(function() {
	$('#submit').click(function() {
		var valid = $(this).closest('form').find('input:invalid').length;
		if(valid == 0 ){
			$('body').append('<div class="ajax-load-qa"></div>');
		}
	});
	
	$('#frm-booking').submit(function() {
		if ($('#agree').length && !$('#agree').is(':checked')) {
			alert($('#agree').data('error'));
			return false;
		}
	    
		// chuyen sang buoc 2
	    $('.bs-wizard > div:eq(1)').removeClass('disabled');
	    $('.bs-wizard > div a[href="#step-2"]').trigger('click');
	    $('html, body').animate({
	        scrollTop: $(".bs-wizard").offset().top
	    }, 1000);
	    
		$.ajax({
			type : $(this).prop("method"),
			cache : !1,
			url : $(this).prop("action"),
			data : $(this).serialize(),
			dataType : "json",
			success : function(b) {
				if (b.status == "error") {
					// active step1
				    $('.bs-wizard > div a[href="#step-1"]').trigger('click');
				    
					alert(b.mess);
					
					if(b.input){						
						// lock all input in step 1
						$("[name=" + b.input + "]", $('#frm-booking')).focus();
					}
				} else {				    
				    // disable all input step1
				    $('#frm-booking').find(':input:not(:disabled)').prop('disabled',true);
				    
				    // parse step3 content
					$.post(
							nv_base_siteurl + 'index.php?' + nv_lang_variable + '='
									+ nv_lang_data + '&' + nv_name_variable + '='
									+ nv_module_name + '&' + nv_fc_variable
									+ '=payment&nocache=' + new Date().getTime(),
							'ajax=1&code=' + b.booking_code + '&checksum=' + b.checksum, function(res) {
								$('#step-3-content').html(res);
							});
				    
					// chuyen sang buoc 3
					var step3 = function(){
						$('#step-2-content').html(LANG.booking_step_2_content_success);
					    $('.bs-wizard > div:eq(1)').removeClass('disabled');
					    $('.bs-wizard > div:eq(2)').removeClass('disabled');
					    $('.bs-wizard > div a[href="#step-3"]').trigger('click');
					};
					setTimeout(step3, 2000);
				}
			}
		});

		return false;
	});
	
	$('#coupons_action').click(function(){
		if(!$('#coupons_code').hasClass('checked')){
			var tour_id = $(this).data('tour-id');
			var coupons_code = $('#coupons_code').val();
			var current_price = $('#total_price').text();
			$('#coupons_code').prop('readonly', true);
			$.ajax({
				type : 'post',
				cache : !1,
				url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=booking&nocache=' + new Date().getTime(),
				data : 'coupons_check=1&tour_id=' + tour_id + '&coupons_code=' + coupons_code + '&current_price=' + current_price,
				dataType : "json",
				success : function(b) {
					if (b.status == "error") {
						alert(b.mess);
						$('#coupons_code').prop('readonly', false);
						$('#coupons_code').focus();
					} else {
						$('#coupons_action').html('<em class="fa fa-trash-o">&nbsp;</em>');
						$('#coupons_action').attr('data-original-title', LANG.coupons_remove);
						$('#coupons_action').attr('data-action', 'remove');
						$('#coupons_code').addClass('checked');
						$('#total_price').html(b.price);
						alert(b.mess);
					}
				}
			});
		}else{
			if(confirm(LANG.coupons_remove_confirm)){
				$('#coupons_action').html('<em class="fa fa-sign-in">&nbsp;</em>');
				$('#coupons_action').attr('data-original-title', LANG.coupons_check);
				$('#coupons_action').attr('data-action', 'check');
				$('#coupons_code').removeClass('checked');
				$('#coupons_code').prop('readonly', false);
				$('#coupons_code').val('');
				$('#total_price').html(nv_tour_get_total_price());
			}
		}
		return false;
	});
});

function nv_tour_delete(id) {
	if (confirm(nv_is_del_confirm[0])) {
		$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '='
				+ nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name
				+ '&' + nv_fc_variable + '=detail&nocache='
				+ new Date().getTime(), 'delete=1&id=' + id, function(res) {
			if (res == 'OK') {
				window.location.href = nv_base_siteurl + "index.php?"
						+ nv_lang_variable + "=" + nv_lang_data + "&"
						+ nv_name_variable + "=" + nv_module_name;
			} else {
				alert(nv_is_del_confirm[2]);
			}
		});
	}
}

function nv_tour_delete_customer(index) {
	$('#customer_' + index).slideUp().remove();
	nv_tour_get_total_price();
	nv_tour_limit_customer();
	return false;
}

function nv_tour_delete_customerprice(index) {
	$('#customerprice_' + index).slideUp().remove();
	nv_tour_get_total_price();
	return false;
}

function nv_tour_limit_customer(){
	var rowCount = $('#table_customer_list >tbody >tr').length - 1;
	if(rowCount >= $('#table_customer_list').data('limit')){
		$('#addcustomer').hide();
	}else{
		$('#addcustomer').show();
	}
}

function nv_tour_get_total_price() {
	var total_price = 0;
	$("input[data-price]").each(function(){
		total_price += parseFloat($(this).attr('data-price'));
	});

	$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=booking&nocache=' + new Date().getTime(), 'formatprice=1&price=' + total_price, function(res) {
		$('#total_price').html(res);
	});
}

function fix_news_image(){
	var news = $('#plan, #description_html'), newsW, w, h;
	if( news.length ){
		var newsW = news.innerWidth();
		$.each($('img', news), function(){
			if( typeof $(this).data('width') == "undefined" ){
				w = $(this).innerWidth();
				h = $(this).innerHeight();
				$(this).data('width', w);
				$(this).data('height', h);
			}else{
				w = $(this).data('width');
				h = $(this).data('height');
			}
			
			if( w > newsW ){
				$(this).prop('width', newsW);
				$(this).prop('height', h * newsW / w);
			}
		});
	}
}

$(window).on('load', function(){
	fix_news_image();
});

$(window).on("resize", function() {
	fix_news_image();
});
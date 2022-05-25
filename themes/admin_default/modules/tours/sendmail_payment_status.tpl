<!-- BEGIN: main -->
{LANG.sendmail_payment_welcome}
<br /><br />
{LANG.sendmail_payment_thank}
<br />
<br />
<div style="background-color: #ddd; padding: 5px;">{LANG.customer_list}</div>
<table style="width: 100%; max-width: 100%; margin-bottom: 18px; border: 1px solid #ddd;">
	<thead>
		<tr>
			<th width="50" style="text-align: left;">{LANG.number}</th>
			<th style="text-align: left;">{LANG.fullname}</th>
			<th width="150" style="text-align: left;">{LANG.birthday}</th>
			<th style="text-align: left;">{LANG.address}</th>
			<th width="100" style="text-align: left;">{LANG.gender}</th>
			<!-- BEGIN: customer_type_thead -->
			<th style="text-align: left;">{LANG.customer_type}</th>
			<!-- END: customer_type_thead -->
			<!-- BEGIN: age_thead -->
			<th width="120" style="text-align: left;">{LANG.age}</th>
			<!-- END: age_thead -->
			<!-- BEGIN: optional_thead -->
			<th width="100" style="text-align: left;">{TITLE}</th>
			<!-- END: optional_thead -->
			<th width="120" style="text-align: left;">{LANG.price} ({MONEY.code})</th>
		</tr>
	</thead>
	<tbody>
		<!-- BEGIN: customer -->
		<tr>
			<td>{CUSTOMER.number}</td>
			<td>{CUSTOMER.fullname}</td>
			<td>{CUSTOMER.birthday}</td>
			<td>{CUSTOMER.address}</td>
			<td>{CUSTOMER.gender}</td>
			<!-- BEGIN: customer_type_tbody -->
			<td>{CUSTOMER.customer_type}</td>
			<!-- END: customer_type_tbody -->
			<!-- BEGIN: age_tbody -->
			<td>{CUSTOMER.age}</td>
			<!-- END: age_tbody -->
			<!-- BEGIN: optional_tbody -->
			<td>{OPTIONAL}</td>
			<!-- END: optional_tbody -->
			<td>{CUSTOMER.price}</td>
		</tr>
		<!-- END: customer -->
	</tbody>
</table>
<p style="text-align: right"><strong>{LANG.total}: </strong><span style="color: #CC3300; font-weight: bold;">{TOTAL} {MONEY.code}</span></p>

<div style="background-color: #ddd; padding: 5px;">{LANG.booking_payment_info}</div>
<ul style="padding: 0; margin: 0; list-style: none">
	<li style="line-height: 25px">{LANG.booking_code}: <strong>{BOOKING_INFO.booking_code}</strong></li>
	<li style="line-height: 25px">{LANG.payment_status}: <strong>{LANG.payment_status_1}</strong></li>
	<li style="line-height: 25px">{LANG.booking_payment_time}: <strong>{BOOKING_INFO.payment_time}</strong></li>
	<li>{LANG.booking_url}: <a href="{BOOKING_INFO.url_payment}">{BOOKING_INFO.url_payment}</a></li>
</ul>
<!-- END: main -->

<div class="booking-detail">
	<div class="control m-bottom text-right">
		<button class="btn btn-primary btn-xs loading" id="change_payment_status" data-status="{BOOKING_INFO.transaction_status}"><em class="fa fa-recycle">&nbsp;</em>{LANG.booking_payment_success}</button>
		<a class="btn btn-danger btn-xs" href="{URL_DELETE}" onclick="return confirm(nv_is_del_confirm[0]);"><em class="fa fa-trash-o">&nbsp;</em>{LANG.delete}</a>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">{LANG.tour_info}</div>
		<div class="panel-body">
			<h2 class="m-bottom" style="margin-top: 0; font-weight: bold">
				<a href="{TOUR_INFO.link}" title="{TOUR_INFO.title}" target="_blank">{TOUR_INFO.title}</a>
			</h2>
			<div class="row">
				<div class="col-xs-24 col-sm-12 col-md-12">
					<ul>
						<li>{LANG.code}: <strong>{TOUR_INFO.code}</strong></li>
						<li>{LANG.num_day}: <strong>{TOUR_INFO.num_day} {LANG.date}</strong></li>
						<li>{LANG.tour_price}: <strong class="money">{TOUR_INFO.price.sale_format} {TOUR_INFO.price.unit}</strong></li>
					</ul>
				</div>
				<div class="col-xs-24 col-sm-12 col-md-12">
					<ul>
						<li>{LANG.begin_time}: <strong>{TOUR_INFO.date_start}</strong></li>
						<li>{LANG.place_start}: <strong>{TOUR_INFO.province.title}</strong></li>
						<li>{LANG.rest}: <strong>{TOUR_INFO.rest}</strong></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">{LANG.booking_info}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-10">
					<ul>
						<li>{LANG.fullname}: <strong>{BOOKING_INFO.contact_fullname}</strong></li>
						<li>{LANG.address}: <strong>{BOOKING_INFO.contact_address}</strong></li>
						<li>{LANG.phone}: <strong>{BOOKING_INFO.contact_phone}</strong></li>
					</ul>
				</div>
				<div class="col-md-10">
					<ul>
						<li>Email: <strong><a href="mailto:{BOOKING_INFO.contact_email}">{BOOKING_INFO.contact_email}</a></strong></li>
						<li>{LANG.payment_method}: <strong>{BOOKING_INFO.payment_method}</strong></li>
						<li>{LANG.booking_time}: <strong>{BOOKING_INFO.booking_time}</strong></li>
					</ul>
				</div>
				<div class="col-md-4 text-center">
					<div class="code-box">
						<span class="money"><strong>{BOOKING_INFO.booking_code}</strong></span>
						<span>{LANG.payment_status_str}</span>
					</div>
				</div>
			</div>
			<!-- BEGIN: contact_note -->
			{LANG.note}: <strong>{BOOKING_INFO.contact_note}</strong>
			<!-- END: contact_note -->
		</div>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">{LANG.customer_list}</div>
		<table class="table table-bordered" id="table_customer_list" data-numcustomer="{NUMCUSTOMER}">
			<thead>
				<tr>
					<th width="50" class="text-center">{LANG.number}</th>
					<th>{LANG.fullname}</th>
					<th width="150">{LANG.birthday}</th>
					<th>{LANG.address}</th>
					<th width="100">{LANG.gender}</th>
					<!-- BEGIN: customer_type_thead -->
					<th>{LANG.customer_type}</th>
					<!-- END: customer_type_thead -->
					<!-- BEGIN: age_thead -->
					<th width="120">{LANG.age}</th>
					<!-- END: age_thead -->
					<!-- BEGIN: optional_thead -->
					<th width="100">{TITLE}</th>
					<!-- END: optional_thead -->
					<th width="120">{LANG.price} ({MONEY.code})</th>
				</tr>
			</thead>
			<tbody>
				<!-- BEGIN: customer -->
				<tr>
					<td class="text-center">{CUSTOMER.number}</td>
					<td>{CUSTOMER.fullname}</td>
					<td>{CUSTOMER.birthday}</td>
					<td>{CUSTOMER.address}</td>
					<td>{CUSTOMER.gender}</td>
					<!-- BEGIN: customer_type_tbody -->
					<td>{CUSTOMER.customer_type}</td>
					<!-- END: customer_type_tbody -->
					<!-- BEGIN: age_tbody -->
					<td>{CUSTOMER.age}</td>
					<!-- END: age_tbody -->
					<!-- BEGIN: optional_tbody -->
					<td>{OPTIONAL}</td>
					<!-- END: optional_tbody -->
					<td>{CUSTOMER.price}</td>
				</tr>
				<!-- END: customer -->
			</tbody>
		</table>
		<div class="panel-body text-right">
			<strong>{LANG.total}: </strong><span class="money">{TOTAL} {MONEY.code}</span>
		</div>
	</div>
</div>
<script>
	var CFG = [];
	CFG.booking_payment_confirm = '{LANG.booking_payment_confirm}';
	CFG.selfurl = '{SELFURL}';
	CFG.booking_id = '{BOOKING_INFO.booking_id};'
</script>
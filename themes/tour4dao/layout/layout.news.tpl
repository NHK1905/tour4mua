<!-- BEGIN: main -->
{FILE "header_only.tpl"}
{FILE "header_extended.tpl"}
<main id="main">
	<div class="blog-wrapper blog-archive page-wrapper">
		<div class="row row-large row-divided ">
			<div class="large-9 col">
				<h1>
					<span style="font-size: 90%; color: #000080;">
						<strong>CẨM NANG DU LỊCH ĐÀ NẴNG 2020</strong>
					</span>
				</h1>
				<p>[NEWS_HTML]</p>
				<hr>
				{MODULE_CONTENT}
			</div>
			<div class="post-sidebar large-3 col">
				<div>
					<aside class="widget woocommerce widget_products">
						[BLOCK_GRP_NEWS]
					</aside>
				</div>
			</div>
		</div>
	</div>
</main>
{FILE "footer_extended.tpl"}
{FILE "footer_only.tpl"}
<!-- END: main -->
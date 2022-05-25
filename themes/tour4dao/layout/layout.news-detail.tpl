<!-- BEGIN: main -->
{FILE "header_only.tpl"}
{FILE "header_extended.tpl"}
<main id="main">
	<div class="blog-wrapper blog-archive page-wrapper">
		<div class="row row-large row-divided ">
			<div class="large-9 col">
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
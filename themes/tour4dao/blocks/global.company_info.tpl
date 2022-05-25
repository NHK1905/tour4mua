<!-- BEGIN: main -->
<style>
    .footer-logo {
        width: 200px;
        margin-bottom: 20px;
    }
</style>
<div class="textwidget" itemscope itemtype="http://schema.org/LocalBusiness">
    <li class="hide hidden">
        <span itemprop="image">{SITE_LOGO}</span>
        <span itemprop="priceRange">N/A</span>
    </li>
    <p>
        <img width="433" height="147" src="{SITE_LOGO}" class="footer-logo">
    <br>
    {DATA.company_name}</p>
    <div class="textwidget">
        <!-- BEGIN: company_address -->
        <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
            <i class="fa fa-map-marker"></i> 
            <strong >{LANG.company_address}:</strong> {DATA.company_address}
        </div>
        <!-- END: company_address -->
        <!-- BEGIN: company_phone -->
        <div itemprop="telephone">
            <i class="fa fa-phone"></i>
            <strong>
                Đặt tour : 
            </strong>
            <!-- BEGIN: item -->
            {PHONE.number}<br/>
            <!-- END: item -->
        </div>
        <!-- END: company_phone -->
        <!-- BEGIN: company_email -->
        <div itemprop="email">
            <i class="fa fa-envelope"></i><strong>{LANG.email}:</strong>&nbsp;{EMAIL}
        </div>
        <!-- END: company_email -->
        <!-- BEGIN: company_website -->
        <div>
            <i class="fa fa-qrcode"></i> <strong>{LANG.company_website}:</strong> {WEBSITE}
        </div>
        <!-- END: company_website -->
    </div>
</div>
<!-- END: main -->

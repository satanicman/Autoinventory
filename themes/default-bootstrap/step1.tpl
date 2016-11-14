<div class="billing-info-wrap clearfix">
    <form action="/" method="POST" id="billing-info-form" name="">
        <div class="alert alert-danger" id="create_billing_error" style="display:none"></div>
        <div class="registration-content-top clearfix">
            <h3 class="myac-title num col-lg-5 active"><span
                        class="num-number"><span>1</span></span><span>{l s="Billing Info"}</span></h3>
            <h3 class="myac-title num col-lg-7"><span
                        class="num-number"><span>2</span></span><span>{l s="Account Details"}</span></h3>
        </div>
        <div class="col-lg-5 billing-info-col left">
            <h6 class="myac-title num"><span class="num-number"><span>1</span></span><span>{l s="Billing Adress"}</span>
            </h6>
            <div class="form-group">
                <label for="business_name" class="label">{l s="Business Name"}</label>
                <input type="text" class="form-control" id="business_name" name="business_name" value="John Doe">
            </div>
            <div class="form-group">
                <label for="adress1" class="label">{l s="Adress 1"}</label>
                <input type="text" class="form-control" id="adress_1" name="adress[]" value="631 Inventory ave">
            </div>
            <div class="form-group">
                <label for="adress2" class="label">{l s="Adress 2"}</label>
                <input type="text" class="form-control" id="adress_2" name="adress[]">
            </div>
            <div class="form-group">
                <label for="city" class="label">{l s="City"}</label>
                <input type="text" class="form-control" id="city" name="city" value="Auto city">
            </div>
            <div class="form-group parts">
                <div class="part large">
                    <label for="state" class="label">{l s="State"}</label>
                    <select name="id_state" id="state" class="form-control not_uniform">
                        <option value="">-</option>
                        {foreach from=$states item=state}
                            <option value="{$state.id_state}">{$state.name}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="part medium">
                    <label for="zip" class="label">{l s="ZipCode"}</label>
                    <input type="text" class="form-control" id="zip_code" name="zip" value="10305">
                </div>
            </div>
        </div>
        <div class="col-lg-7 billing-info-col right">
            <h6 class="myac-title num"><span
                        class="num-number"><span>2</span></span><span>{l s="Credit Card Info"}</span></h6>
            <div class="form-group">
                <label for="card_name" class="label">{l s="Name On Card"}</label>
                <input type="text" class="form-control" name="card_name" id="card_name" value="John Doe">
            </div>
            <div class="form-group">
                <label for="card_number" class="label payment-wrap">
                    <span>{l s="Card Number"}</span>
                    <span class="payment-logos">
                                    <span class="payment-logo visa"></span>
                                    <span class="payment-logo mc"></span>
                                    <span class="payment-logo ae"></span>
                                    <span class="payment-logo discovery"></span>
                                </span>
                </label>
                <input type="text" class="form-control" name="card_number" id="card_number" value="xxxx-xxx-xxx xxxx">
            </div>
            <div class="form-group parts large">
                <div class="part medium">
                    <label for="cvv" class="label">{l s="CVV Number"}</label>
                    <div class="help_container">
                        <input type="text" class="form-control" name="cvv" id="cvv" value="123">
                        <span class="help-wrap">
                                        <span class="help-icon"></span>
                                        <span class="help-text">{l s="some help text"}</span>
                                    </span>
                    </div>
                </div>
                <div class="part medium">
                    <label for="month" class="label">{l s="Exp. Month"}</label>
                    <input type="text" class="form-control" placeholder="{l s="Month"}" name="month" id="month">
                </div>
                <div class="part medium">
                    <label for="year" class="label">{l s="Exp. Year"}</label>
                    <input type="text" class="form-control" placeholder="{l s="Year"}" name="year" id="year">
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <input type="hidden" name="email" id="email" value="{if isset($email) && $email}{$email}{/if}">
        <div class="registration-content-footer">
            <button class="btn btn-default" name="submitBilling">{l s="Continue"}</button>
            <p class="registration-content-footer-text">{l s="After your free 60 day trial, you’ll be charged $499/month. You can cancel anytime on your setting page
    By clicking “Continue you start free free trail & agreeing to autorize this recurring charge"}</p>
        </div>
    </form>
</div>
<form id="searchForm" class="searchForm" data-type="{$type}" action="/" onsubmit="return false;">
    <input type="hidden" id="id_category_layered" value="{$layered_category_url}">
    <div class="top-line">
        <div class="form-group half odd make">
            <label for="make" class="label">{l s="Make"}</label>
            <select name="{$id_feature_make}" id="make_select_{$type}" data-url="{$make_feature_url}"  class="form-control not_uniform make_select">
                {foreach from=$makes item=model}
                    <option data-id_feature_value="{$model.id_feature_value}" value="{$model.url_name}{*$model.id_feature_value*}">{$model.value}</option>
                {/foreach}
            </select>
        </div>
        <div class="form-group half even model">
            <label for="model" class="label">{l s="Model"}</label>
            <select name="{$id_feature_model}" id="model_select_{$type}" data-url="{$model_feature_url}" class="form-control not_uniform">
                {foreach from=$models item=model}
                    <option value="{$model.url_name}{*$model.id_feature_value*}">{$model.value}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="bottom-line">
        {if $type === 'buy'}
            <div class="form-group medium-block payment">
                <p class="filter-header label">
                    {l s="Price"}
                    <span class="filter-header-value">
                        <span id="price_slider_range">{convertPrice price=$prices.min|floatval} - {convertPrice price=$prices.max|floatval}</span>
                    </span>
                </p>
                <div class="price_slider_container">
                    <div class="price_slider" id="price_slider" data-max="{$prices.max}" data-min="{$prices.min}"></div>
                </div>
            </div>
        {else}
        <div class="form-group small payment">
            <label for="payment" class="label">{l s="Monthly Payment"}</label>
            <select name="{$id_feature_MonthlyPayment}" data-url="{$MonthlyPayment_url}" id="payment_select_{$type}" class="form-control not_uniform">
                {foreach from=$MonthlyPayment item=model}
                    <option value="{$model.url_name}">{$model.value}</option>
                {/foreach}
            </select>
        </div>
        <div class="form-group small remaining">
            <label for="remaining" class="label">{l s="Months Remaining"}</label>
            <select name="{$id_feature_MonthsRemaining}" data-url="{$MonthsRemaining_url}" id="remaining_select_{$type}" class="form-control not_uniform">
                {foreach from=$MonthsRemaining item=model}
                    <option value="{$model.url_name}">{$model.value}</option>
                {/foreach}
            </select>
        </div>
        {/if}
        <div class="form-group small Zip">
            <label for="zip" class="label">{l s="Zip"}</label>
            <input type="text" name="zip" id="zip_{$type}" data-url="{$zip_url}" class="form-control" value="10305">
        </div>
        <div class="form-group small">
            <div class="label bottom">{l s="within"}</div>
        </div>
        <div class="form-group small distance">
            <label for="distance" class="label">{l s="Distance"}</label>
            <select name="{$id_feature_distance}" id="distance_select_{$type}" data-url="{$distance_url}" class="form-control not_uniform" data-max="{$distance_max}">
                {foreach from=$distance item=model}
                    <option value="{$model}">{$model}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <button class="btn btn-default" id="find_my_car">{l s="Find my car"}</button>
</form>
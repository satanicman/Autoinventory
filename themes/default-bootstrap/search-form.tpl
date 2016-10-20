<form id="searchForm" action="/" onsubmit="return false;">
    <input type="hidden" id="id_category_layered" value="{$layered_category_url}">
    <div class="top-line">
        <div class="form-group half odd make">
            <label for="make" class="label">{l s="Make"}</label>
            <select name="{$id_feature_make}" id="make_select" data-url="{$make_feature_url}"  class="form-control not_uniform">
                {foreach from=$makes item=model}
                    <option value="{$model.url_name}{*$model.id_feature_value*}">{$model.value}</option>
                {/foreach}
            </select>
        </div>
        <div class="form-group half even model">
            <label for="model" class="label">{l s="Model"}</label>
            <select name="{$id_feature_model}" id="model_select" data-url="{$model_feature_url}" class="form-control not_uniform">
                {foreach from=$models item=model}
                    <option value="{$model.url_name}{*$model.id_feature_value*}">{$model.value}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="bottom-line">
        <div class="form-group small payment">
            <label for="payment" class="label">{l s="Monthly Payment"}</label>
            <select name="{$id_feature_MonthlyPayment}" data-url="{$MonthlyPayment_url}" id="payment_select" class="form-control not_uniform">
                {foreach from=$MonthlyPayment item=model}
                    <option value="{$model.url_name}">{$model.value}</option>
                {/foreach}
            </select>
        </div>
        <div class="form-group small remaining">
            <label for="remaining" class="label">{l s="Months Remaining"}</label>
            <select name="{$id_feature_MonthsRemaining}" data-url="{$MonthsRemaining_url}" id="remaining_select" class="form-control not_uniform">
                {foreach from=$MonthsRemaining item=model}
                    <option value="{$model.url_name}">{$model.value}</option>
                {/foreach}
            </select>
        </div>
        <div class="form-group small Zip">
            <label for="zip" class="label">{l s="Zip"}</label>
            <input type="text" name="zip" id="zip" class="form-control" value="10305">
        </div>
        <div class="form-group small">
            <div class="label bottom">{l s="within"}</div>
        </div>
        <div class="form-group small distance">
            <label for="distance" class="label">{l s="Distance"}</label>
            <select name="{$id_feature_distance}" id="distance_select" class="form-control not_uniform" data-max="{$distance_max}">
                {foreach from=$distance item=model}
                    <option value="{$model}">{$model}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <button class="btn btn-default" id="find_my_car">{l s="Find my car"}</button>
</form>
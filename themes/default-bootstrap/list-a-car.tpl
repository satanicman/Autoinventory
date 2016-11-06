{if isset($message) && $message}
    <div class="bootstrap">
        <div class="module_confirmation conf confirm alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {$message.text}
        </div>
    </div>
{/if}
<form class="add-product" method="POST" enctype="multipart/form-data">
    {if isset($product->id) && $product->id}
        <input type="hidden" name="product_id" id="product_id" value="{$product->id}">
    {/if}
    {if !isset($add)}
    <div id="information">
        <h2 class="myac-title">{l s="Information"}</h2>
        {if isset($categories) && $categories}
            <div class="form-group required">
                <label for="categories" class="label">{l s="Category"}<span>*</span></label>

                <select name="categories" id="categories" class="form-control not_uniform">
                    {foreach from=$categories item=category name=category}
                        <option value="{$category.id_feature_value}" data-tab="#category-{$category.id_feature_value}"
                                {if isset($product->features[33]['id_feature_value'])
                                && $product->features[33]['id_feature_value'] == $category.id_feature_value}
                                    selected="selected"
                                {/if}
                        >{$category.value}</option>
                    {/foreach}
                </select>
            </div>
        {/if}
        <input type="hidden" name="type" id="type" value="">
        <div class="form-group vin">
            <input type="text" id="vin-value" name="features[11][value]" class="form-control"
                   placeholder="{l s="VIN"}"{if isset($product->features[11])
            && $product->features[11]}
                value="{$product->features[11]['value']}"
                    {/if}>
            <input type="hidden" name="features[11][required]" value="0">
            <button class="vin-submit btn btn-default" id="vin-submit">{l s="FIND"}</button>
        </div>
        <div class="form-group required half odd">
            <label for="make" class="label">{l s="Make"}<span>*</span></label>
            <input type="hidden" id="make_post"{if isset($product->features[19])
            && $product->features[19]}
                value="{$product->features[19]['value']}"
                    {/if}>
            <input type="hidden" name="features[19][required]" value="{l s="Make"}">
            <select name="features[19][value]" id="make" class="api_fields form-control not_uniform"></select>
        </div>
        <div class="form-group required half even">
            <label for="model" class="label">{l s="Model"}<span>*</span></label>
            <input type="hidden" id="model_post" {if isset($product->features[20])
            && $product->features[20]}
                value="{$product->features[20]['value']}"
                    {/if}>
            <select id="model" class="api_fields form-control not_uniform" name="features[20][value]"></select>
            <input type="hidden" name="features[20][required]" value="{l s="Model"}">
        </div>
        <div class="form-group required half odd">
            <label for="year" class="label">{l s="Year"}<span>*</span></label>
            <input type="hidden" id="year_post"{if isset($product->year)
            && $product->year}
                value="{$product->year}"
                    {/if}>
            <select name="year" id="year" class="api_fields form-control not_uniform"></select>

        </div>
        <div class="form-group required half even">
            <label for="trim" class="label">{l s="Trim"}<span>*</span></label>
            <input type="hidden" id="trim_post"{if isset($product->features[21])
            && $product->features[21]}
                value="{$product->features[21]['value']}"
                    {/if}>
            <select id="trim" class="api_fields form-control not_uniform"
                    name="features[21][value]"></select>
            <input type="hidden" name="features[21][required]" value="{l s="Trim"}">
        </div>
        <div class="tab-content">
            <div id="category-918" class="tab-pane fade">
                <div class="form-group required half odd">
                    <label for="engine" class="label">{l s="Engine"}<span>*</span></label>
                    <input type="hidden" id="engine_post"{if isset($product->features[13])
                    && $product->features[13]}
                        value="{$product->features[13]['value']}"
                            {/if}>
                    <select class="api_fields form-control not_uniform" name="features[13][value]" id="engines"></select>
                    <input type="hidden" name="features[13][required]" value="{l s="Engine"}">
                </div>
                <div class="form-group required half even">
                    <label for="transmission" class="label">{l s="Transmission"}<span>*</span></label>
                    <input type="hidden" id="transmission_post"{if isset($product->features[10])
                    && $product->features[10]}
                        value="{$product->features[10]['value']}"
                            {/if}>
                    <select class="api_fields form-control not_uniform" name="features[10][value]" id="transmission"></select>
                    <input type="hidden" name="features[10][required]" value="{l s="Transmission"}">
                </div>
                <div class="form-group required half odd">
                    <label for="body_type" class="label">{l s="Body type"}<span>*</span></label>
                    <input type="hidden" id="body_type_post"{if isset($product->features[24])
                    && $product->features[24]}
                        value="{$product->features[24]['value']}"
                            {/if}>
                    <select  id="body_type" class="api_fields form-control not_uniform" name="features[24][value]">
                        <option value="0" selected="selected">Please select the variant</option>
                        <option value="Convertible">Convertible</option>
                        <option value="Coupe">Coupe</option>
                        <option value="Crossover">Crossover</option>
                        <option value="Diesel">Diesel</option>
                        <option value="Electric">Electric</option>
                        <option value="Hatchback">Hatchback</option>
                        <option value="Hybrid">Hybrid</option>
                        <option value="Luxury">Luxury</option>
                        <option value="Minivan">Minivan</option>
                        <option value="Natural Gas">Natural Gas</option>
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="Truck - Crew Cab">Truck - Crew Cab</option>
                        <option value="Truck - Extended Cab">Truck - Extended Cab</option>
                        <option value="Truck - Regular Cab">Truck - Regular Cab</option>
                        <option value="Van">Van</option>
                        <option value="Wagon">Wagon</option>
                    </select>
                    <input type="hidden" name="features[24][required]" value="{l s="Body type"}">
                </div>
                <div class="form-group required half even">
                    <label for="doors" class="label">{l s="Doors"}<span>*</span></label>
                    <input type="hidden" id="doors_post"{if isset($product->features[25])
                    && $product->features[25]}
                        value="{$product->features[25]['value']}"
                            {/if}>
                    <select id="doors" class="api_fields form-control not_uniform" name="features[25][value]">
                        <option value="0" selected="selected">Please select the variant</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <input type="hidden" name="features[25][required]" value="{l s="Doors"}">
                </div>
                <div class="form-group required half odd">
                    <label for="drive_type" class="label">{l s="Drive type"}<span>*</span></label>
                    <input type="hidden" id="drive_type_post"{if isset($product->features[14])
                    && $product->features[14]}
                        value="{$product->features[14]['value']}"
                            {/if}>
                    <select id="drive_type" class="api_fields form-control not_uniform" name="features[14][value]">
                        <option value="0" selected="selected">Please select the variant</option>
                        <option value="4WD">4WD</option>
                        <option value="FWD">FWD</option>
                        <option value="RWD">RWD</option>
                    </select>
                    <input type="hidden" name="features[14][required]" value="{l s="Drive type"}">
                </div>
                <div class="form-group required half even">
                    <label for="price" class="label">{l s="Price"}<span>*</span></label>
                    <input type="text" id="price" class="form-control" name="price"
                           value="{if isset($product->price) && $product->price}{$product->price}{else}0{/if}">
                </div>
                <div class="form-group required half odd">
                    <label for="exterior_color" class="label">{l s="Exterior color"}<span>*</span></label>
                    <input type="hidden" id="exterior_color_post"{if isset($product->features[8])
                    && $product->features[8]}
                        value="{$product->features[8]['value']}"
                            {/if}>
                    <select id="exterior_color" name="features[8][value]" class="form-control not_uniform">
                        <option value="0" selected="selected">Please select the variant</option>
                        <option value="black">black</option>
                        <option value="blue">blue</option>
                        <option value="green">green</option>
                        <option value="grey">grey</option>
                        <option value="orange">orange</option>
                        <option value="purple">purple</option>
                        <option value="red">red</option>
                        <option value="silver">silver</option>
                        <option value="white">white</option>
                        <option value="yellow">yellow</option>
                        <option value="custom">custom</option>
                    </select>
                    <input type="hidden" name="features[8][required]" value="{l s="Exterior color"}">
                </div>
                <div class="form-group required half even">
                    <label for="interior_color" class="label">{l s="Interior color"}<span>*</span></label>
                    <input type="hidden" id="interior_color_post"{if isset($product->features[9])
                    && $product->features[9]}
                        value="{$product->features[9]['value']}"
                            {/if}>
                    <select id="interior_color" name="features[9][value]" class="form-control not_uniform">
                        <option value="0" selected="selected">Please select the variant</option>
                        <option value="black">black</option>
                        <option value="white">white</option>
                        <option value="beige">beige</option>
                        <option value="red">red</option>
                        <option value="grey">grey</option>
                        <option value="other">other</option>
                    </select>
                    <input type="hidden" name="features[9][required]" value="{l s="Interior color"}">
                </div>
                <div class="form-group required half odd">
                    <label for="mileage" class="label">{l s="Mileage"}<span>*</span></label>
                    <input type="text" id="mileage" name="miles" class="form-control"{if isset($product->miles)
                    && $product->miles}
                        value="{$product->miles}"
                            {/if}>
                </div>
                <div class="form-group half even">
                    <label for="stock" class="label">{l s="Stock #"}</label>
                    <input type="text" id="stock" name="features[12][value]"
                           class="form-control"{if isset($product->features[12])
                    && $product->features[12]}
                        value="{$product->features[12]['value']}"
                            {/if}>
                    <input type="hidden" name="features[12][required]" value="0">
                </div>
                <div class="clearfix"></div>
                <div class="form-group">
                    <label for="description" class="label">{l s="Description"}</label>
                    <textarea name="description" id="description">
                    {if isset($product->description) && $product->description}
                        {$product->description}
                    {/if}
                </textarea>
                </div>
            </div>
            <div id="category-919" class="tab-pane fade">
            </div>
        </div>
    </div>
    <div id="features" class="clearfix">
        <h2 class="myac-title">{l s="Features"}</h2>
        {foreach from=$available_features item=feature name=feature}
            <div class="form-group col-lg-4 {if $smarty.foreach.feature.index%2}even{else}odd{/if}">
                <input type="checkbox" id="feature_{$feature.id_feature}"
                       name="features[features][{$feature.id_feature}]"
                       class="form-control not_uniform" value="1"{if isset($product->features[$feature.id_feature])
                && $product->features[$feature.id_feature]}
                    checked="checked"
                        {/if}>
                <label for="feature_{$feature.id_feature}" class="label"><span
                            class="checkbox"></span><span>{$feature.name}</span></label>
            </div>
        {/foreach}
    </div>
    {/if}
    {if (isset($product->id) && $product->id) || (isset($add) && $add)}
        <div id="images">
            <h2 class="myac-title">{l s="Images"}</h2>
            {*<input type="file" name="image_product[]" class="filer not_uniform" data-parsley-id="27" multiple="multiple">*}
            <input type="file" name="image_product[]" id="filer_input" class="filer not_uniform" data-parsley-id="27"
                   multiple="multiple">
        </div>
    {/if}
    {if !isset($add)}
    <button class="vin-submit btn btn-default" name="myAccountAddProduct"
            id="myAccountAddProduct">{if isset($product->id) && $product->id}{l s="Update product"}{else}{l s="Add product"}{/if}</button>
    {else}
        <input type="hidden" name="add" id="add" value="1">
    {/if}
    <div id="preloader_list">
        <img src="{$img_dir}/preloader.gif" alt="preloader">
    </div>
</form>
<div class="hidden" id="buy-fields"></div>
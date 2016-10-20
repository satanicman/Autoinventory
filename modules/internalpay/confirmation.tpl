<div id="dlist" class="block">
<h2 class="block_header corners">{l s='List of paid products' mod='internalpay'}</h2>
<ul>
{if isset($products)}
{foreach from=$products item=product}
	<li>    {if $product.download_hash}
                    <a href="{if isset($bIs15) && $bIs15 && isset($product.download)}{$product.download->getTextLink(false, $product.download_hash)}{else}{$base_dir}get-file.php?key={$product.filename|escape:'htmlall':'UTF-8'}-{$product.download_hash|escape:'htmlall':'UTF-8'}{/if}" title="{l s='download this product' mod='internalpay'}">
                            <img src="{$img_dir|escape:'htmlall':'UTF-8'}icon/download_product.gif" class="icon" alt="{l s='Download product' mod='internalpay'}" />
                    </a>
                    <a href="{if isset($bIs15) && $bIs15 && isset($product.download)}{$product.download->getTextLink(false, $product.download_hash)}{else}{$base_dir}get-file.php?key={$product.filename|escape:'htmlall':'UTF-8'}-{$product.download_hash|escape:'htmlall':'UTF-8'}{/if}" title="{l s='download this product' mod='internalpay'}">
                            {l s='Download' mod='internalpay'} {$product.product_name|escape:'htmlall':'UTF-8'}
                    </a>
		{else}
                    {$product.product_name|escape:'htmlall':'UTF-8'}
		{/if}
	</li>
{/foreach}
{/if}
</ul>
</div>
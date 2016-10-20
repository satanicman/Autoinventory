<p class="payment_module">
	<a href="{$this_path_ssl|escape:'htmlall':'UTF-8'}validation.php" title="{l s='Payment with internal account' mod='internalpay'}">
		<img src="{$this_path|escape:'htmlall':'UTF-8'}internalpay.png" alt="{l s='Payment with internal account' mod='internalpay'}" style="float:left;" />
		<br />{l s='Payment with internal account' mod='internalpay'}
		<br />{l s='After you pay your balance will remain:' mod='internalpay'} {convertPrice price=$new_ballance}
		<br style="clear:both;" />
	</a>
</p>
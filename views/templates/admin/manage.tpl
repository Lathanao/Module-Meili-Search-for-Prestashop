{**
*          2020 Lathanao - Module for Prestashop
*          Add a great module and modules on your great shop.
*
*          @author         Lathanao <welcome@lathanao.com>
*          @copyright      2020 Lathanao
*          @license        MIT (see LICENCE file)
 *}
<div class="panel">
	<h3><i class="icon-cogs"></i> Indexes server Meili</h3>
	<div class="row">
		<p>
			<a class="ajaxcall-recurcive btn btn-default" href="{$products_indexer}" target='_blank'>{l s='Index all products' d='Modules.MeiliSearch.Admin'}</a>
			<a class="ajaxcall-recurcive btn btn-default" href="{$categories_indexer}" target='_blank'>{l s='Index all categories' d='Modules.MeiliSearch.Admin'}</a>
			<a class="ajaxcall btn btn-default" href="{$drop_indexes}" target='_blank'>{l s='Drop all indexes' d='Modules.MeiliSearch.Admin'}</a>
		</p>
	</div>
	<div class="row">
		<div class="alert alert-info">
        {l s='Cron job your index product:' d='Modules.MeiliSearch.Admin'}
			<br>
			<strong><a href="{$products_indexer}" target='_blank'>{$products_indexer}</a></strong>
			<br>
			<br>
        {l s='Cron job your index categories' d='Modules.MeiliSearch.Admin'}
			<br>
			<strong><a href="{$categories_indexer}" target='_blank'>{$categories_indexer}</a></strong>
		</div>
	</div>
	<div class="row">
		<div class="alert alert-info">{l s='A nightly rebuild is recommended.' d='Modules.MeiliSearch.Admin'}</div>
	</div>
</div>
<div class="panel">
	<h3><i class="icon-cogs"></i> Indexes integrity</h3>
	<div class="row">
		<div class="alert alert-info">
        {l s='Product index ' d='Modules.MeiliSearch.Admin'}
			<br>
			<strong><a href="{$products_indexer_info}" target='_blank'>{$products_indexer_info}</a></strong>
			<br>
			<strong><a href="{$products_indexer_documents}" target='_blank'>{$products_indexer_documents}</a></strong>
			<br>
			<strong><a href="{$products_indexer_stats}" target='_blank'>{$products_indexer_stats}</a></strong>
			<br>
			<br>
        {l s='cCategory index integrity' d='Modules.MeiliSearch.Admin'}
			<br>
			<strong><a href="{$categories_indexer_info}" target='_blank'>{$categories_indexer_info}</a></strong>
			<br>
			<strong><a href="{$categories_indexer_documents}" target='_blank'>{$categories_indexer_documents}</a></strong>
			<br>
			<strong><a href="{$categories_indexer_stats}" target='_blank'>{$categories_indexer_stats}</a></strong>
		</div>
	</div>
</div>
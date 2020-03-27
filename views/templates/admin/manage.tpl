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
      {if isset($isMeilliDetected)  && !$isMeilliDetected}
				<div class="alert alert-danger">
            <h4>{l s='The Meili search engine is not detected. It\'s maybe or wrong configuration' d='Modules.MeiliSearch.Admin'}</h4>
				</div>
      {elseif $isMeilliDetected == 404}
				<div class="alert alert-success">
            <h4>{l s='The Meili search engine is well detected, but doesn\'t look to work properly' d='Modules.MeiliSearch.Admin'}</h4>
				</div>
      {else}
				<div class="alert alert-success">
            <h4>{l s='The Meili search engine is well detected' d='Modules.MeiliSearch.Admin'}</h4>
				</div>
      {/if}
      {if !$isCurlDetected}
				<div class="alert alert-danger">
		        <h4>{l s='The Curl extension is not installed' d='Modules.MeiliSearch.Admin'}</h4>
				</div>
      {/if}
	</div>

	<div class="row">
    {if isset($isUidProductMatchWithUidInDb) && $isUidProductMatchWithUidInDb}
			<div class="alert alert-success">
				<h4>{l s='Product index in used :' d='Modules.MeiliSearch.Admin'} {$product_UID}</h4>
				<a href="{$product_indexer_info}" target='_blank'>Index information</a>
				<br>
				<a href="{$product_indexer_documents}" target='_blank'>Index documents</a>
				<br>
				<a href="{$product_indexer_stats}" target='_blank'>Index statistic</a>
			</div>
    {else}
			<div class="alert alert-danger">
				<h4>{l s='Product index' d='Modules.MeiliSearch.Admin'}</h4>
          {if $product_UID}
              {l s='Uid category doesn\'t match with Uid in database' d='Modules.MeiliSearch.Admin'}}
              {l s='You should reindex the search engine datadase.' d='Modules.MeiliSearch.Admin'}}
          {else}
              {l s='Uid category is missing.' d='Modules.MeiliSearch.Admin'}
              {l s='You should reindex the search engine datadase.' d='Modules.MeiliSearch.Admin'}
          {/if}
			</div>
    {/if}

    {if isset($isUidCategoryMatchWithUidInDb) && $isUidCategoryMatchWithUidInDb}
			<div class="alert alert-success">
				<h4>{l s='Category index in used :' d='Modules.MeiliSearch.Admin'} {$category_UID}</h4>
				<a href="{$category_indexer_info}" target='_blank'>Index information</a>
				<br>
				<a href="{$category_indexer_documents}" target='_blank'>Index documents</a>
				<br>
				<a href="{$category_indexer_stats}" target='_blank'>Index statistic</a>
			</div>
    {else}
			<div class="alert alert-danger">
				<h4>{l s='Category index' d='Modules.MeiliSearch.Admin'}</h4>
          {if $category_UID}
              {l s='Uid category doesn\'t match with Uid in database.' d='Modules.MeiliSearch.Admin'}
              {l s='You should reindex the search engine datadase.' d='Modules.MeiliSearch.Admin'}
          {else}
              {l s='Uid category is missing.' d='Modules.MeiliSearch.Admin'}
              {l s='You should reindex the search engine datadase.' d='Modules.MeiliSearch.Admin'}
          {/if}
			</div>
    {/if}
	</div>
</div>

<div class="panel">
	<h3><i class="icon-cogs"></i> Cron job</h3>
	<div class="row">

		<div class="alert alert-info">
			<h4>{l s='Cron job' d='Modules.MeiliSearch.Admin'}</h4>
        {l s='Cron job your index product:' d='Modules.MeiliSearch.Admin'}
			<br>
			<strong><a href="{$products_indexer}" target='_blank'>{$products_indexer}</a></strong>
			<br>
			<br>
        {l s='Cron job your index categories:' d='Modules.MeiliSearch.Admin'}
			<br>
			<strong><a href="{$categories_indexer}" target='_blank'>{$categories_indexer}</a></strong>
			<br>
			<br>
			<p>{l s='A nightly rebuild is recommended.' d='Modules.MeiliSearch.Admin'}</p>
		</div>
	</div>
</div>

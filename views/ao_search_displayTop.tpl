/*******************************************************************
*          2020 Lathanao - Module for Prestashop
*          Add a great module and modules on your great shop.
*
*          @author         Lathanao <welcome@lathanao.com>
*          @copyright      2020 Lathanao
*          @license        MIT (see LICENCE file)
********************************************************************/
<div id="search-box-meili" class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
	<form method="get" action="{$search_controller_url}">
		<div class="action input">
			<input type="text" autocomplete="off" id="search-query-meili" name="search-query-meili" class="search_query"
			       data-index="1" placeholder="{$setup.SEARCH_INPUT_MSG}">
			<button class="btn btn-labeled btn-primary submit-search">
				<i class="material-icons"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">search</font></font></i>
			</button>
			<table id="table-result-search-meili"></table>
		</div>
	</form>
</div>



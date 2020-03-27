{**
*          2020 Lathanao - Module for Prestashop
*          Add a great module and modules on your great shop.
*
*          @author         Lathanao <welcome@lathanao.com>
*          @copyright      2020 Lathanao
*          @license        MIT (see LICENCE file)
 *}
<div class="panel">
	<h3><i class="icon-cogs"></i> DOCUMENTATION AND PREREQUISITES</h3>
	<div class="row">
		<h4>To make this module working properly, you must install:</h4>
		<ul>
			<li><a href="https://www.meilisearch.com/" target='_blank'>Meili Search</a></li>
		</ul>
		<h4>You should install:</h4>
		<ul>
			<li><a href="https://www.docker.com/" target='_blank'>Docker</a></li>
		</ul>
		<h4>If you are using Google Chrome, maybe this extension might help you to get a better view on your
			datas (Firefox get a similar functionnality already by default, no need extension)</h4>
		<ul>
			<li><a href="https://chrome.google.com/webstore/detail/json-formatter/bcjindcccaagfpapjjmafapmmgkkhgoa" target='_blank'>json-formatter</a></li>
		</ul>

		<h4>Then, you need to proxied the ajax search request by adding these 3 lines in your nginx config (e.g.: before your PHP FPM setup)</h4>
		<code> location /instantsearch/ { <br>
			&nbsp&nbsp&nbspproxy_pass http://127.0.0.1:7700/;<br>
			}</code>
		<br>
		<br>
		<a href="{$link_readme}">Download README.md</a>

	</div>
</div>

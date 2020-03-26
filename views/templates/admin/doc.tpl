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
		<h4>To make this module working properly, you should install:</h4>
		<ul>
			<li><a href="https://www.docker.com/" target='_blank'>Docker</a></li>
			<li><a href="https://www.meilisearch.com/" target='_blank'>Meili search via docker</a></li>
		</ul>
		<p>Then, you need to proxied the search request by adding these 3 line in your nginx config (e.g.: before your PHP FPM setup)</p>
		<code> location /search/ { <br>
			&nbsp&nbsp&nbspproxy_pass http://127.0.0.1:7700/;<br>
			}</code>
	</div>
</div>

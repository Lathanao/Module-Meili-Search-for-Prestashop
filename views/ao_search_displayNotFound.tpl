{********************************************************************
*          Module for Prestashop
*          Add a great module and modules on your great shop.
*
*          @author         Lathanao <welcome@lathanao.com>
*          @copyright      2019 Lathanao
*          @version        1.0
*          @license        Commercial license see README.md
********************************************************************}
<div class="dropdown-toggle">
  <div class="ui segment gradient-shadow">
    <form method="get" action="{$search_controller_url}">
      <div class="ui action fluid input">
        <input type="text" autocomplete="off" id="search_query_2" name="search_query" class="search_query"
               data-index="2" placeholder="{$setup.SEARCH_INPUT_MSG}">
        <button class="ui primary right labeled icon button">
          Search<i id="changeIcon" class="search icon"></i>
        </button>
      </div>
    </form>
    <table id="result_search_2" class="resultSearch ui very basic collapsing celled table">
      <tbody>
      </tbody>
    </table>
  </div>
</div>

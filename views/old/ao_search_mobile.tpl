{********************************************************************
*          Visio Theme for Prestashop
*          Add a beautiful theme and modules on your shop.
*
*          @author         Lathanao <welcome@lathanao.com>
*          @copyright      2017 Lathanao
*          @version        1.0
*          @license        Commercial license see README.md
********************************************************************}

<div class="dropdown-toggle">
  <div class="ui segment gradient-shadow">

    <form id="#cleanImputSearch" method="get" action="{$search_controller_url}">
      <div class="ui fluid input mobile">
        <input type="text" autocomplete="off" id="search_query_3" name="search_query" data-index="3"
               placeholder="{$setup.SEARCH_INPUT_MSG}">
      </div>

    </form>

    <table id="result_search_3" class="resultSearch ui very basic collapsing unstackable celled table"
           style="display: none;">
      <tbody></tbody>
    </table>

  </div>
</div>

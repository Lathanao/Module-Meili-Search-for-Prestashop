{********************************************************************
*          Visio Theme for Prestashop
*          Add a beautiful theme and modules on your shop.
*
*          @author         Lathanao <welcome@lathanao.com>
*          @copyright      2017 Lathanao
*          @version        1.0
*          @license        Commercial license see README.md
********************************************************************}

<li class="parent dropdown search">
  <a class="dropdown-menu"><i
            class="{if isset($setup.menu_circular_icon) && $setup.menu_circular_icon}circular{/if} search icon"></i></a>
  <div class="dropdown-toggle">
    <div class="ui segment gradient-shadow">
      <form method="get" action="{$search_controller_url}">
        <div class="ui action input">
          <input type="text" autocomplete="off" id="search_query" name="search_query" class="search_query"
                 data-index="1" placeholder="{$setup.SEARCH_INPUT_MSG}">
          <button class="ui primary right labeled icon button">
            Search
            <i id="changeIcon" class="search icon"></i>
          </button>
        </div>
      </form>
      <div id="result_search_1">
      </div>
    </div>
  </div>
</li>

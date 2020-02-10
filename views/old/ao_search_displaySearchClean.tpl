{*******************************************************************************
*          Visio Theme for Prestashop
*          Add a beautiful theme and modules on your shop.
*
*          @author         Lathanao <welcome@lathanao.com>
*          @copyright      2017 Lathanao
*          @version        1.0
*          @license        Commercial license see README.md
*********************************************************************₢⦿͡㍕⦿͡ꀣ***}

<section class="search dropdown">
  <form id="cleanImputSearch" method="get" action="{$search_controller_url}">
    <div class="ui action input">
      <input type="text" autocomplete="off" id="search_query" name="search_query" class="search_query" data-index="1"
             placeholder="{$setup.SEARCH_INPUT_MSG}">
      <button class="ui icon button">
        <i id="changeIcon" class="search icon"></i>
      </button>
    </div>
  </form>

  <div id="result_search" class="dropdown-toggle">
  </div>
</section>

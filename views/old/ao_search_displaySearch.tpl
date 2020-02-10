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

  <svg role="menu" viewBox="0 0 20 20" aria-label="search">
    <use xlink:href="#search"/>
  </svg>

  <div class="dropdown-toggle">
    <div class="shadow">
      <form method="get" action="{$search_controller_url}">
        <div class="action input">
          <input type="text" autocomplete="off" id="search_query" name="search_query" class="search_query"
                 data-index="1" placeholder="{$setup.SEARCH_INPUT_MSG}">
          <button class="btn btn-labeled btn-primary add-to-cart">
            Search
            <svg viewBox="0 0 20 20" aria-label="search">
              <use xlink:href="#search"/>
            </svg>
          </button>
        </div>
      </form>
      <div id="result_search_1">
      </div>
    </div>
  </div>
</li>

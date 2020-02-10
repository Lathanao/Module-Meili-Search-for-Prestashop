{********************************************************************
*          Module for Prestashop
*          Add a great module and modules on your great shop.
*
*          @author         Lathanao <welcome@lathanao.com>
*          @copyright      2019 Lathanao
*          @version        1.0
*          @license        Commercial license see README.md
********************************************************************}
{if !empty($products)}
    <table id="table-result-search">
      <tbody>
      {foreach from=$products item=item key=key name=products}
        <tr class="row">
          <td class="col-lg-9 col-xs-9">
            <h5 class="header">
              <img src="{$item.cover.bySize.small_default.url}" height="{$item.cover.bySize.small_default.height}"
                   width="{$item.cover.bySize.small_default.width}" class="rounded image">
              <div class="content">
                <a href="{$item.url}">{$item.name|strip_tags:'UTF-8'}</a>
              </div>
            </h5>
          </td>
          {if $item.show_price  && $item.available_for_order && $SEARCH_SHOW_PRICE}
            <td class="col-lg-3 col-xs-3">
              {if $item.has_discount}
                <div class="center aligned">
                  <del class="regular-price">{$item.regular_price}</del>
                </div>
                <div class="center aligned">
                  {if $item.discount_type === 'percentage'}
                    <span class="discount">{$item.discount_percentage}</span>
                  {else}
                    <span class="discount">-{$item.discount_to_display}</span>
                  {/if}
                </div>
              {/if}
              <div class="center aligned">
                <span class="price">{$item.price}</span>
              </div>
            </td>
          {/if}
        </tr>
      {/foreach}
      </tbody>
    </table>
{else}
{/if}

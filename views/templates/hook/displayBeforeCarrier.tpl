{*
* 2007-2025 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2025 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($showPostcodeRestrictionMessage) && $showPostcodeRestrictionMessage}
    <div class="carrierpostcoderestriction-notice">
        <div class="texts">
            <h4>{l s='Unfortunately, we do not deliver to your address yet!' d='Modules.Carrierpostcoderestriction.Shop'}
            </h4>
            <p>{l s='Your area (%s) is not yet included in our current delivery route! However, you can always come and collect your basket at one of our collection points.' sprintf=[$postcode] d='Modules.Carrierpostcoderestriction.Shop'}
            </p>
        </div>
        {if $deliveryLinkUrl && $deliveryLinkUrl != '#'}
            <div class="carrierpostcoderestriction-links">
                <p>{l s='To see the areas we currently deliver to' d='Modules.Carrierpostcoderestriction.Shop'}</p>
                <a href="{$deliveryLinkUrl|escape:'html':'UTF-8'}" target="_blank"
                    class="link-delivery-zones">{$deliveryLinkText|escape:'html':'UTF-8'}</a>
            </div>
        {/if}
        {if $contactLinkUrl && $contactLinkUrl != '#'}
            <div class="carrierpostcoderestriction-links">
                <p>{l s='Do you think this is an error?' d='Modules.Carrierpostcoderestriction.Shop'}</p>
                <a href="{$contactLinkUrl|escape:'html':'UTF-8'}" target="_blank"
                    class="link-contact">{$contactLinkText|escape:'html':'UTF-8'}</a>
            </div>
        {/if}
    </div>
{/if}
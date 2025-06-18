<div class="panel">
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{l s='ID' d='Modules.Carrierpostcoderestriction.Admin'}</th>
                    <th>{l s='Carrier' d='Modules.Carrierpostcoderestriction.Admin'}</th>
                    <th>{l s='Description' d='Modules.Carrierpostcoderestriction.Admin'}</th>
                    <th>{l s='Status' d='Modules.Carrierpostcoderestriction.Admin'}</th>
                    <th class="text-center">{l s='Bypass Restriction' d='Modules.Carrierpostcoderestriction.Admin'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$carriers item=carrier}
                    <tr>
                        <td>{$carrier.id_carrier|intval}</td>
                        <td>{$carrier.name|escape:'html':'UTF-8'}</td>
                        <td>{$carrier.delay|escape:'html':'UTF-8'}</td>
                        <td>
                            {if $carrier.active}
                                <span class="badge badge-success">{l s='Active' d='Modules.Carrierpostcoderestriction.Admin'}</span>
                            {else}
                                <span class="badge badge-danger">{l s='Inactive' d='Modules.Carrierpostcoderestriction.Admin'}</span>
                            {/if}
                        </td>
                        <td class="text-center">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="CARRIER_BYPASS_{$carrier.id_carrier|intval}" 
                                       id="CARRIER_BYPASS_{$carrier.id_carrier|intval}_on" value="1" 
                                       {if isset($bypass_values[$carrier.id_carrier]) && $bypass_values[$carrier.id_carrier]}checked="checked"{/if}>
                                <label for="CARRIER_BYPASS_{$carrier.id_carrier|intval}_on">{l s='Yes' d='Modules.Carrierpostcoderestriction.Admin'}</label>
                                <input type="radio" name="CARRIER_BYPASS_{$carrier.id_carrier|intval}" 
                                       id="CARRIER_BYPASS_{$carrier.id_carrier|intval}_off" value="0" 
                                       {if !isset($bypass_values[$carrier.id_carrier]) || !$bypass_values[$carrier.id_carrier]}checked="checked"{/if}>
                                <label for="CARRIER_BYPASS_{$carrier.id_carrier|intval}_off">{l s='No' d='Modules.Carrierpostcoderestriction.Admin'}</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>

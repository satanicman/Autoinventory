<tr class="dealer-table-row">
    <td class="dealer-table-col">{if isset($day) && $day}{$day}{/if}</td>
    <td class="dealer-table-col">
        <select name="time[{if isset($type) && $type}{$type|lower}{/if}][{if isset($day) && $day}{$day|lower}{/if}][from]" id="time" class="form-control">
            {assign var=hour value=0}
            {for $step=0 to 47}
                {assign var=time value=''}
                {if $step % 2}
                    {assign var=time value=$hour|cat:':30'}
                    {if $hour < 12}
                        {assign var=time value=$time|cat:' am'}
                    {else}
                        {assign var=time value=$time|cat:' pm'}
                    {/if}
                    {assign var=hour value=$hour+1}
                {else}
                    {assign var=time value=$hour|cat:':00'}
                    {if $hour < 12}
                        {assign var=time value=$time|cat:' am'}
                    {else}
                        {assign var=time value=$time|cat:' pm'}
                    {/if}
                {/if}
                <option value="{$time}"{if isset($selected) && $selected && $selected.from == $time} selected="selected"{/if}>{$time}</option>
            {/for}
        </select>
    </td>
    <td class="dealer-table-col">
        <select name="time[{if isset($type) && $type}{$type|lower}{/if}][{if isset($day) && $day}{$day|lower}{/if}][to]" id="time" class="form-control">
            {assign var=hour value=0}
            {for $step=0 to 47}
                {assign var=time value=''}
                {if $step % 2}
                    {assign var=time value=$hour|cat:':30'}
                    {if $hour < 12}
                        {assign var=time value=$time|cat:' am'}
                    {else}
                        {assign var=time value=$time|cat:' pm'}
                    {/if}
                    {assign var=hour value=$hour+1}
                {else}
                    {assign var=time value=$hour|cat:':00'}
                    {if $hour < 12}
                        {assign var=time value=$time|cat:' am'}
                    {else}
                        {assign var=time value=$time|cat:' pm'}
                    {/if}
                {/if}
                <option value="{$time}"{if isset($selected) && $selected && $selected.to == $time} selected="selected"{/if}>{$time}</option>
            {/for}
        </select>
    </td>
    <td class="dealer-table-col">
        <label class="label">
            <input type="checkbox" name="time[{if isset($type) && $type}{$type|lower}{/if}][{if isset($day) && $day}{$day|lower}{/if}][closed]"
                   id="closed"{if isset($selected) && isset($selected.closed) && $selected.closed} checked="checked"{/if}>
            <span>{l s="Closed"}</span>
        </label>
    </td>
</tr>
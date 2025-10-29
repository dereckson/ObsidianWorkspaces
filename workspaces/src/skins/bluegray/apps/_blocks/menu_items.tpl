<div class="row">
    {if $items}
		<ul id="documents">
            {foreach from=$items item=item}
				<li class="document"><a href="{$item.url}">{$item.name}</a></li>
            {/foreach}
		</ul>
    {else}
		<p>No element currently available.</p>
    {/if}
</div>

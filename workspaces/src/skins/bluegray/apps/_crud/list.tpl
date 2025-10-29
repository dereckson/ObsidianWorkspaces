<div class="row">
    {if $items}
	<table class="table">
		<thead>
			<tr>
				{foreach from=$keys item=key}
				<th scope="col">{$key}</th>
				{/foreach}
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$items item=item}
		<tr>
            {foreach from=$keys item=key name=loop}
			{if $smarty.foreach.loop.index == 0}
				<td><a href="{$item.url}">{$item[$key]}</a></td>
			{else}
				<td>{$item[$key]}</td>
			{/if}
            {/foreach}
			<td><a href="{$item.url}">🔎</a></td>
		</tr>
		{/foreach}
		</tbody>
	</table>
    {else}
		<p>No item.</p>
    {/if}
</div>

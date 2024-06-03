{if $modules[$index].module eq "refresh"}
	{include file="block_begin.tpl"}
{/if}
{$modules[$index].message}
<br>
<br>
<div align="center">
{$lang.refresh_message}<br>
<br>
<a href="{$refresh_url}">{$lang.refresh_message_click}</a>
</div>
<br>
{if $modules[$index].module eq "refresh"}
	{include file="block_end.tpl"}
{/if}

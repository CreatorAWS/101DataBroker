{if $modules[$index].error_type eq "custom"}
	{include file="block_begin.tpl"}
	<p>{$modules[$index].error_message}</p>
	{include file="block_end.tpl"}
{else}
	{include file="block_begin.tpl" panel_title=$lang.error404.title}
	<p>{$lang.error404.text}</p>
	{include file="block_end.tpl"}
{/if}
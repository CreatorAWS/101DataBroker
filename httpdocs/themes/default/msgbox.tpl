{if $modules[$index].mode eq "yesno"}
{include file="block_begin.tpl"}
<center>
<strong>{$modules[$index].message}?</strong><br>
<br>
<a href="{$modules[$index].url_yes}">{$lang.yes}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$modules[$index].url_no}">{$lang.no}</a><br>
</center>
<br>
{include file="block_end.tpl"}
{/if}

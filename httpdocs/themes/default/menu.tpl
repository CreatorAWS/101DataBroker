{if $modules[$index].mode eq 'view'}
{include file="block_begin.tpl"}
{section name=i loop=$modules[$index].menu}
{if $smarty.section.i.index eq 0}{else}<br>{/if}
{section name=j loop=$modules[$index].menu[i].level start=1} - {/section}
<a href="{$modules[$index].menu[i].url}" class="{if $modules[$index].menu[i].active eq "1"}menuSelected{else}menuLine{/if}"{if $modules[$index].menu[i].alt neq ""} alt="{$modules[$index].menu[i].alt}"{/if}{if $modules[$index].menu[i].newpage eq "1"} target="_blank"{/if}{$modules[$index].menu[i].attr}>{$modules[$index].menu[i].caption}</a>
{/section}
{include file="block_end.tpl"}
{/if}

{include file="menu_adminpart.tpl"}
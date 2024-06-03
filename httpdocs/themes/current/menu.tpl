{if $modules[$index].mode eq 'view'}
{include file="block_begin.tpl"}
<ul class="nav nav-pills nav-stacked">
{section name=i loop=$modules[$index].menu}
	<li{if $modules[$index].menu[i].active eq "1"} class="active"{/if}>
		<a href="{$modules[$index].menu[i].url}"{if $modules[$index].menu[i].alt neq ""} alt="{$modules[$index].menu[i].alt}"{/if}{if $modules[$index].menu[i].newpage eq "1"} target="_blank"{/if}{$modules[$index].menu[i].attr}>
			{section name=j loop=$modules[$index].menu[i].level start=1} - {/section}
			{$modules[$index].menu[i].caption}
		</a>
	</li>
{/section}
</ul>
{include file="block_end.tpl"}
{/if}

{include file="menu_adminpart.tpl"}
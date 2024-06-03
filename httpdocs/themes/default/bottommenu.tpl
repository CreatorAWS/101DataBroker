<div class="bottommenu">
<table width="100%"><tr>
{section name=i loop=$special.bottommenu}
{if $special.bottommenu[i].level eq 1}
<td><ul id="bottommenusection{$special.bottommenu[i].id}" class="bottommenusection">
<li class="bottommenusectionfirst">{if $special.bottommenu[i].url neq ""}<a href="{$special.bottommenu[i].url}"  class="{if $special.bottommenu[i].active eq "1"}bottomMenuSelected{else}bottomMenuLine{/if}"{$special.bottommenu[i].attr}>{$special.bottommenu[i].caption}</a>{else}<strong>{$special.bottommenu[i].caption}</strong>{/if}</li>
{if $special.bottommenu[i].is_submenu eq 1}
   {section name=j loop=$special.bottommenu start=i}
    {if $special.bottommenu[j].level gt "1" and $special.bottommenu[j].submenu_from eq $special.bottommenu[i].id}
     <li><a href="{$special.bottommenu[j].url}"{if $special.bottommenu[j].alt neq ""} alt="{$special.bottommenu[j].alt}"{/if}{if $special.bottommenu[j].newpage eq "1"} target="_blank"{/if} class="{if $special.bottommenu[j].active eq "1"}bottomMenuSelected{else}bottomMenuLine{/if}"{$special.bottommenu[j].attr}>{$special.bottommenu[j].caption}</a></li>
    {/if}
   {/section}
{/if}
</ul></td>
{/if}
{/section}
</tr></table>
</div>
{* Minimalistic style
<div class="bottommenu">
{section name=i loop=$special.bottommenu}
{if $smarty.section.i.index eq 0}{else} | {/if}
<a href="{$special.bottommenu[i].url}" class="{if $special.bottommenu[i].active eq "1"}bottomMenuSelected{else}bottomMenuLine{/if}"{if $special.bottommenu[i].alt neq ""} alt="{$special.bottommenu[i].alt}"{/if}{if $special.bottommenu[i].newpage eq "1"} target="_blank"{/if}{$special.bottommenu[i].attr}>{$special.bottommenu[i].caption}</a>
{/section}
</div>
*}
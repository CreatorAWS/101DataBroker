<div class="bottommenu pull-right">
{section name=i loop=$special.bottommenu}
{if $smarty.section.i.index eq 0}{else} | {/if}
<a href="{$special.bottommenu[i].url}" class="{if $special.bottommenu[i].active eq "1"}bottomMenuSelected{else}bottomMenuLine{/if}"{if $special.bottommenu[i].alt neq ""} alt="{$special.bottommenu[i].alt}"{/if}{if $special.bottommenu[i].newpage eq "1"} target="_blank"{/if}{$special.bottommenu[i].attr}>{$special.bottommenu[i].caption}</a>
{/section}
</div>

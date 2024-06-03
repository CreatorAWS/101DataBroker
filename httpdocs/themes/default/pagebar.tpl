<div class="pagebar">
{if $modules[$index].pages.pages gt "1"}
{section name="i" loop=5000 max=$modules[$index].pages.pages start="1"}
{if $smarty.section.i.index eq $modules[$index].pages.selected}<strong>{$smarty.section.i.index}</strong> {else}
<a href="{$modules[$index].pages.url}&from={math equation="x*y" x=$modules[$index].pages.interval y=$smarty.section.i.index-1}">{$smarty.section.i.index}</a> {/if}
{/section}
{/if}
</div>
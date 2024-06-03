{if $special.pathcount gt 0}
<div class="path">
{section name=path_index loop=$special.path}
{if $smarty.section.path_index.index neq "0"} :: {/if}
<a href="{$special.path[path_index].url}">{$special.path[path_index].title}</a>
{/section}
</div>
{/if}
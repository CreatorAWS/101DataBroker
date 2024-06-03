{if $special.pathcount gt 0}
<ol class="breadcrumb">
{section name=path_index loop=$special.path}
  <li>
    {if $smarty.section.path_index.index neq 0}
      <svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">
        <path d="M1 9L5 5L1 1" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    {/if}
    <a href="{$special.path[path_index].url}">{$special.path[path_index].title}</a>
  </li>
{/section}
</ol>
{/if}

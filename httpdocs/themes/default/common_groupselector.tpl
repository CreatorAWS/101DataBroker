<select name="{if $var eq ""}p_groups{else}{$var}{/if}[]" size="10" multiple{if $style neq ""} style="{$style}"{/if} class="common-groupselector">
{section name=groups_index loop=$groups}
	<option value="{$groups[groups_index].id}"{section name=selgroups_index loop=$groups}{if $selgroups[selgroups_index].id eq $groups[groups_index].id} selected{/if}{/section}>{$groups[groups_index].title}</option>
{/section}
</select>

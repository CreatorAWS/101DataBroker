{if $modules[$index].borders_off neq "1"}
{if $modules[$index].panel eq "center"}
  {* якщо центральна панель *}
<table  width="100%">
	<tr>
		<td width="100%" class="centerpaneltitle"  id="block{$index}title">
			{if $modules[$index].block_image neq ""}<img src="{$modules[$index].block_image}"> {/if}
			{if $modules[$index].rewrite_title_to neq ""}{$modules[$index].rewrite_title_to}{elseif $panel_title neq ""}{$panel_title}{else}{$modules[$index].title}{/if}
		</td>
	</tr>
	<tr>
		<td width="100%" class="centerpanelbody" id="block{$index}body">
{elseif $modules[$index].panel eq "1"}
<table  width="100%">
	<tr>
		<td width="100%" class="panel{$modules[$index].panel}title" id="block{$index}title">
			{if $modules[$index].block_image neq ""}<img src="{$modules[$index].block_image}"> {/if}
			{if $modules[$index].rewrite_title_to neq ""}{$modules[$index].rewrite_title_to}{elseif $panel_title neq ""}{$panel_title}{else}{$modules[$index].title}{/if}
		</td>
	</tr>
	<tr>
		<td width="100%" class="panel{$modules[$index].panel}body" id="block{$index}body">
{else}
<table  width="100%">
	<tr>
		<td width="100%" class="panel{$modules[$index].panel}title" id="block{$index}title">
			{if $modules[$index].block_image neq ""}<img src="{$modules[$index].block_image}"> {/if}
			{if $modules[$index].rewrite_title_to neq ""}{$modules[$index].rewrite_title_to}{elseif $panel_title neq ""}{$panel_title}{else}{$modules[$index].title}{/if}
		</td>
	</tr>
	<tr>
		<td width="100%" class="panel{$modules[$index].panel}body" id="block{$index}body">
{/if}
{/if}
{if $tmp.block[$index].blockstart neq 1}{$special.document.block[$index].blockstart}{assign var=tmp.block[$index].blockstart value=1}{/if}
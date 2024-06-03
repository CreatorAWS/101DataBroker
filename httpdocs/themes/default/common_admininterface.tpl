{strip}
{if $modules[$index].mode eq "common_admininterface_launcher" and $blocks eq ""}
	{include file="common_admininterface.tpl" blocks=$modules[$index].common_admininterface_output}
{else}
	{section name=admininterfaceblockindex loop=$blocks}
		{if $blocks[admininterfaceblockindex].show_borders eq 1}
			{if $blocks[admininterfaceblockindex].title eq ""}
				{include file="block_begin.tpl"}
			{else}
				{include file="block_begin.tpl" panel_title=$blocks[admininterfaceblockindex].title}
			{/if}
		{/if}
		{section name=admininterfaceitemindex loop=$blocks[admininterfaceblockindex].items}
		{if $blocks[admininterfaceblockindex].items[admininterfaceitemindex].type eq "form"}
			{include file="common_adminform.tpl" form=$blocks[admininterfaceblockindex].items[admininterfaceitemindex].form}
		{elseif $blocks[admininterfaceblockindex].items[admininterfaceitemindex].type eq "table"}
			{include file="common_admintable.tpl" table=$blocks[admininterfaceblockindex].items[admininterfaceitemindex].table}
		{elseif $blocks[admininterfaceblockindex].items[admininterfaceitemindex].type eq "board"}
			{include file="common_boardmessages.tpl" board=$blocks[admininterfaceblockindex].items[admininterfaceitemindex].board}
		{elseif $blocks[admininterfaceblockindex].items[admininterfaceitemindex].type eq "bar"}
			{include file="common_adminbuttons.tpl" bar=$blocks[admininterfaceblockindex].items[admininterfaceitemindex].bar}
		{elseif $blocks[admininterfaceblockindex].items[admininterfaceitemindex].type eq "panel"}
			{include file="common_adminpanel.tpl" panelblocks=$blocks[admininterfaceblockindex].items[admininterfaceitemindex].panel}
		{elseif $blocks[admininterfaceblockindex].items[admininterfaceitemindex].type eq "pagebar"}
			{include file="pagebar.tpl"}
		{elseif $blocks[admininterfaceblockindex].items[admininterfaceitemindex].type eq "html"}
			{$blocks[admininterfaceblockindex].items[admininterfaceitemindex].html}
		{elseif $blocks[admininterfaceblockindex].items[admininterfaceitemindex].tpl neq ""}
			{include file=$blocks[admininterfaceblockindex].items[admininterfaceitemindex].tpl data=$blocks[admininterfaceblockindex].items[admininterfaceitemindex].data action=$blocks[admininterfaceblockindex].items[admininterfaceitemindex].action}
		{/if}
		{/section}
		{if $blocks[admininterfaceblockindex].show_borders eq 1}
			{include file="block_end.tpl"}
		{/if}
	{/section}
{/if}
{/strip}
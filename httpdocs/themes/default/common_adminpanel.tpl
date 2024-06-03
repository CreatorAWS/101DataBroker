{strip}
	{section name=adminpanelblockindex loop=$panelblocks}
		{section name=adminpanelitemindex loop=$panelblocks[adminpanelblockindex].items}
		{if $panelblocks[adminpanelblockindex].items[adminpanelitemindex].type eq "form"}
			{include file="common_adminform.tpl" form=$panelblocks[adminpanelblockindex].items[adminpanelitemindex].form}
		{elseif $panelblocks[adminpanelblockindex].items[adminpanelitemindex].type eq "table"}
			{include file="common_admintable.tpl" table=$panelblocks[adminpanelblockindex].items[adminpanelitemindex].table}
		{elseif $panelblocks[adminpanelblockindex].items[adminpanelitemindex].type eq "board"}
			{include file="common_boardmessages.tpl" board=$panelblocks[adminpanelblockindex].items[adminpanelitemindex].board}
		{elseif $panelblocks[adminpanelblockindex].items[adminpanelitemindex].type eq "bar"}
			{include file="common_adminbuttons.tpl" bar=$panelblocks[adminpanelblockindex].items[adminpanelitemindex].bar}
		{elseif $panelblocks[adminpanelblockindex].items[adminpanelitemindex].type eq "pagebar"}
			{include file="pagebar.tpl"}
		{elseif $panelblocks[adminpanelblockindex].items[adminpanelitemindex].type eq "html"}
			{$panelblocks[adminpanelblockindex].items[adminpanelitemindex].html}
		{elseif $panelblocks[adminpanelblockindex].items[adminpanelitemindex].tpl neq ""}
			{include file=$panelblocks[adminpanelblockindex].items[adminpanelitemindex].tpl data=$panelblocks[adminpanelblockindex].items[adminpanelitemindex].data action=$blocks[adminpanelblockindex].items[adminpanelitemindex].action}
		{/if}
		{/section}
	{/section}
{/strip}
{strip}
{if $tabspostfix eq ""}
	{assign var=tabspostfix value=1|mt_rand:2000000}
{/if}
<div role="tabpanel">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
	{section name=admintabsblockindex loop=$data}
		<li role="presentation"{if $data[admintabsblockindex].active} class="active"{/if}><a href="#tab-{$tabspostfix}-{$smarty.section.admintabsblockindex.index}" aria-controls="tab-{$tabspostfix}-{$smarty.section.admintabsblockindex.index}" role="tab" data-toggle="tab">{$data[admintabsblockindex].title}</a></li>
	{/section}
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
	{section name=admintabsblockindex loop=$data}
		<div role="tabpanel" class="tab-pane{if $data[admintabsblockindex].active} active{/if}" id="tab-{$tabspostfix}-{$smarty.section.admintabsblockindex.index}">
		{section name=admintabsitemindex loop=$data[admintabsblockindex].items}
		{if $data[admintabsblockindex].items[admintabsitemindex].type eq "form"}
			{include file="common_adminform.tpl" form=$data[admintabsblockindex].items[admintabsitemindex].form}
		{elseif $data[admintabsblockindex].items[admintabsitemindex].type eq "table"}
			{include file="common_admintable.tpl" table=$data[admintabsblockindex].items[admintabsitemindex].table}
		{elseif $data[admintabsblockindex].items[admintabsitemindex].type eq "board"}
			{include file="common_boardmessages.tpl" board=$data[admintabsblockindex].items[admintabsitemindex].board}
		{elseif $data[admintabsblockindex].items[admintabsitemindex].type eq "bar"}
			{include file="common_adminbuttons.tpl" bar=$data[admintabsblockindex].items[admintabsitemindex].bar}
		{elseif $data[admintabsblockindex].items[admintabsitemindex].type eq "panel"}
			{include file="common_adminpanel.tpl" panelblocks=$data[admintabsblockindex].items[admintabsitemindex].panel}
		{elseif $data[admintabsblockindex].items[admintabsitemindex].type eq "pagebar"}
			{include file="pagebar.tpl"}
		{elseif $data[admintabsblockindex].items[admintabsitemindex].type eq "html"}
			{$data[admintabsblockindex].items[admintabsitemindex].html}
		{elseif $data[admintabsblockindex].items[admintabsitemindex].tpl neq ""}
			{include file=$data[admintabsblockindex].items[admintabsitemindex].tpl data=$data[admintabsblockindex].items[admintabsitemindex].data action=$data[admintabsblockindex].items[admintabsitemindex].action}
		{/if}
		{/section}
		</div>
	{/section}
	</div>
</div>
{/strip}
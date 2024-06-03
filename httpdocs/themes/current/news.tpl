{if $modules[$index].mode eq "view"}
{include file="block_begin.tpl"}
<div class="news_datetime label pull-right">{if $_settings.news_use_time eq "1"}{$modules[$index].news_time} {/if}{$modules[$index].news_date}</div>
{if $modules[$index].news_image neq ""}
<div class="news_image thumbnail">
<img src="{$modules[$index].news_image}" border="0" align="left" class="" />
{if $modules[$index].row->img_copyright_news neq ""}<br />{$modules[$index].row->img_copyright_news}{/if}
</div>
{/if}
{$modules[$index].text}
{include file="common_attachments.tpl" attachments=$modules[$index].attachments}
{if ($modules[$index].can_edit eq "1" or $modules[$index].can_delete eq "1") and $modules[$index].panel eq "center"}<hr>
	{if $modules[$index].can_edit eq "1"}<a href="index.php?m=news&d=edit&nid={$modules[$index].id}">{$lang.edit}</a>{/if}
	{if $modules[$index].can_delete eq "1"}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php?m=news&d=delete&nid={$modules[$index].id}&ctg={$modules[$index].id_category}">{$lang.delete}</a>{/if}
{/if}
{include file="block_end.tpl"}
{if $modules[$index].alike_news_present gt 0}
{include file="block_begin.tpl" panel_title=$lang.common.see_also}
{section name=i loop=$modules[$index].alike_news}
<strong class="label label-default">{$modules[$index].alike_news[i].date}</strong> <a href="{$modules[$index].alike_news[i].fullink}">{$modules[$index].alike_news[i].title}</a><br />
{/section}
{include file="block_end.tpl"}
{/if}		   
{/if}		   

{if $modules[$index].mode eq "listnews"}
{if $modules[$index].short_news eq "1"}{* SHORT LIST NEWS *}
{include file="block_begin.tpl"}
{section name=i loop=$modules[$index].list}
<div>
<span class=" label label-info">{if $_settings.news_use_time eq "1"}{$modules[$index].list[i].time} {/if}{$modules[$index].list[i].date}</span>
<a class="shortnews" href="{$modules[$index].list[i].url}">{if $modules[$index].list[i].title neq ""}{$modules[$index].list[i].title}{else}{$modules[$index].list[i].preview|strip_tags}{/if}</a>
</div>
{/section}
<div class="shortnews_allnews pull-right"><a href="index.php?m=news&d=listnews{if $modules[$index].id_category_n neq ""}&ctg={$modules[$index].id_category_n}{/if}">{$lang.all_news}</a></div>
{include file="block_end.tpl"}
{else}{* FULL LIST NEWS *}
{section name=i loop=$modules[$index].list}
		{if $_settings.news_use_title eq "1" and $modules[$index].list[i].title neq ""}
		<span class="news_datetime_list pull-right label label-info">{if $_settings.news_use_time eq "1"}{$modules[$index].list[i].time} {/if}{$modules[$index].list[i].date}</span> <h3 class="news-title"><a href="{$modules[$index].list[i].url}">{$modules[$index].list[i].title}</a></h3>
		{else}
			<h3 class="news-title"><a href="{$modules[$index].list[i].url}">{if $_settings.news_use_time eq "1"}{$modules[$index].list[i].time} {/if}{$modules[$index].list[i].date}</a></h3>
		{/if}
		{if $modules[$index].list[i].image neq ""}
		<div class="news_image_list thumbnail">
		<a href="{$modules[$index].list[i].url}"><img src="{$modules[$index].list[i].image}" border="0"></a>
		</div>
		{/if}
		{if $_settings.news_full_list_longformat eq 1}
			{$modules[$index].list[i].text}
		{else}
			{$modules[$index].list[i].preview} <a href="{$modules[$index].list[i].url}">&raquo;</a>
		{/if}
		<p></p>
{/section}
{include file="pagebar.tpl"}
{/if}
{/if}

{if $userinfo.level gt 0}
{include file="news_adminpart.tpl"}
{/if}
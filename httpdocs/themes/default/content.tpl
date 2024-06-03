{if $modules[$index].mode eq "view"}
{if $modules[$index].category.id_ctg neq ""}
{include file="block_begin.tpl" panel_title=$modules[$index].category.title_category}
{$modules[$index].category.preview_ctg}
{include file="block_end.tpl"}
{/if}
{section name=i loop=$modules[$index].content}
{include file="block_begin.tpl" panel_title=$modules[$index].content[i].title}
{if $modules[$index].content[i].can_view eq "1"}
{if $modules[$index].content[i].image neq ""}
<div class="content_image">
<img src="{$modules[$index].content[i].image}" border="0" align="left">
</div>
{/if}
{$modules[$index].content[i].text}
{include file="common_attachments.tpl" attachments=$modules[$index].content[i].attachments}
{if $modules[$index].content[i].fullink neq ""}<div align="right"><a href="{$modules[$index].content[i].fullink}">[{$lang.module_content.view_full} >>]</a></div>{/if}
{else}
{$lang.message_access_denied}
{/if}
{if ($modules[$index].content[i].can_edit eq "1" or $modules[$index].content[i].can_delete eq "1" ) and $modules[$index].panel eq "center"}
<hr>
{if $modules[$index].content[i].can_edit eq "1" and $modules[$index].panel eq "center"}
<a href="index.php?m=content&d=edit&cid={$modules[$index].content[i].cid}">{$lang.edit}</a>
{/if}
{if $modules[$index].content[i].can_delete eq "1" and $modules[$index].panel eq "center"}
&nbsp;&nbsp;&nbsp;
<a href="index.php?m=content&d=delete&cid={$modules[$index].content[i].cid}&ctg={$modules[$index].content[i].id_category}">{$lang.delete}</a>
{/if}
{/if}
{include file="block_end.tpl"}
{if $modules[$index].content[i].alike_texts_present gt 0}
{include file="block_begin.tpl" panel_title=$lang.common.see_also}
{section name=j loop=$modules[$index].content[i].alike_texts}
<a href="{$modules[$index].content[i].alike_texts[j].fullink}">{$modules[$index].content[i].alike_texts[j].title}</a><br />
{/section}
{include file="block_end.tpl"}
{/if}
{/section}
{/if}

{if $modules[$index].mode eq "viewctg"}
{include file="block_begin.tpl"}
{if $modules[$index].category.can_view eq "1"}
{if $modules[$index].preview_category neq ""}{$modules[$index].preview_category}<br /><br />{/if}
{section name=i loop=$modules[$index].subcategories}
<div class="ctg-in-ctg">
<a href="{$modules[$index].subcategories[i].filename}">{$modules[$index].subcategories[i].title}</a><br />
{$modules[$index].subcategories[i].preview_category}
</div>
{/section}
{section name=i loop=$modules[$index].category.ctg}
<div class="txt-in-ctg">
<a href="{$modules[$index].category.ctg[i].url}">{$modules[$index].category.ctg[i].title}</a><br />
{if $modules[$index].category.ctg[i].image neq ""}
		<div class="content_image_list">
		<a href="{$modules[$index].category.ctg[i].url}"><img src="{$modules[$index].category.ctg[i].image}" border="0"></a>
		</div>
{/if}
{if $_settings.content_use_preview eq "1"}
{$modules[$index].category.ctg[i].preview}
{/if}
</div>
{/section}
{else}
{$lang.message_access_denied}
{/if}
{include file="block_end.tpl"}
{/if}

{if $userinfo.level gt 0}
{include file="content_adminpart.tpl"}
{/if}
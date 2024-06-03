
{if $modules[$index].mode eq "edit" or $modules[$index].mode eq "add"}
{include file="block_begin.tpl"}
<form action="index.php?m=news&d={if $modules[$index].mode eq "edit"}postedit&nid={$modules[$index].id_news}{else}postadd{/if}" method="post" name="post" enctype="multipart/form-data">
{if $modules[$index].mode eq "edit"}<input type="hidden" name="p_date_prev" value="{$modules[$index].date_news}">{/if}
{if $modules[$index].error_message neq ""}<div class="errormessage">{$modules[$index].error_message}</div>{/if}
<table width="100%" class="edittable">
	<tr>
		<td colspan="2">
			{if $_settings.news_use_time eq "1"}
				{$lang.common.time}: 
					<select name="p_time_hours" size="1" class="admintime">
						{section name=i start=0 loop=24 step=1}
						<option value="{$smarty.section.i.index}"{if $smarty.section.i.index eq $modules[$index].date.hours} SELECTED{/if}>{$smarty.section.i.index}</option>
						{/section}
					</select>
					<select name="p_time_minutes" size="1" class="admintime">
						{section name=i start=0 loop=60 step=1}
						<option value="{$smarty.section.i.index}"{if $smarty.section.i.index eq $modules[$index].date.minutes} SELECTED{/if}>{$smarty.section.i.index}</option>
						{/section}
					</select>
			{/if}
			{$lang.date_news}: 
				<select name="p_date_day" size="1" class="admindate">
					{section name=i start=1 loop=32 step=1}
					<option value="{$smarty.section.i.index}"{if $smarty.section.i.index eq $modules[$index].date.mday} SELECTED{/if}>{$smarty.section.i.index}</option>
					{/section}
				</select>
				<select name="p_date_month" size="1" class="adminmonth">
					<option value="1"{if $modules[$index].date.mon eq "1"} SELECTED{/if}>{$lang.month_1}</option>
					<option value="2"{if $modules[$index].date.mon eq "2"} SELECTED{/if}>{$lang.month_2}</option>
					<option value="3"{if $modules[$index].date.mon eq "3"} SELECTED{/if}>{$lang.month_3}</option>
					<option value="4"{if $modules[$index].date.mon eq "4"} SELECTED{/if}>{$lang.month_4}</option>
					<option value="5"{if $modules[$index].date.mon eq "5"} SELECTED{/if}>{$lang.month_5}</option>
					<option value="6"{if $modules[$index].date.mon eq "6"} SELECTED{/if}>{$lang.month_6}</option>
					<option value="7"{if $modules[$index].date.mon eq "7"} SELECTED{/if}>{$lang.month_7}</option>
					<option value="8"{if $modules[$index].date.mon eq "8"} SELECTED{/if}>{$lang.month_8}</option>
					<option value="9"{if $modules[$index].date.mon eq "9"} SELECTED{/if}>{$lang.month_9}</option>
					<option value="10"{if $modules[$index].date.mon eq "10"} SELECTED{/if}>{$lang.month_10}</option>
					<option value="11"{if $modules[$index].date.mon eq "11"} SELECTED{/if}>{$lang.month_11}</option>
					<option value="12"{if $modules[$index].date.mon eq "12"} SELECTED{/if}>{$lang.month_12}</option>
				</select>
				<select name="p_date_year" size="1" class="admindate">
					{section name=i start=2006 loop=2016 step=1}
					<option value="{$smarty.section.i.index}"{if $smarty.section.i.index eq $modules[$index].date.year} SELECTED{/if}>{$smarty.section.i.index}</option>
					{/section}
				</select>
		</td>
	</tr>
	{if $_settings.news_use_title eq "1"}
	<tr>
		<td>
			{$lang.module_news.caption}:
		</td>
		<td>
			<input type="text" name="p_title_news" id="title_news" size="50" value="{$modules[$index].title_news}" />
		</td>
	</tr>
	{/if}
	{if $_settings.news_use_image eq "1"}
	<tr>
		<td>
			{$lang.common.image}
		</td>
		<td>
			{include file="upload_image.tpl" no_use_foto_lang=1}
		</td>
	</tr>
	<tr>
		<td>
			{$lang.common.copyright} ({$lang.common.image}):
		</td>
		<td>
			<input type="text" name="img_copyright_news" size="50" value="{$modules[$index].post.img_copyright_news}">
		</td>
	</tr>
	{/if}
	<tr>
		<td colspan="2">
			{if $special.ext_editor_on eq "1"}
			{if $modules[$index].mode eq "edit"}<br><a href="index.php?m=news&d=edit&nid={$modules[$index].id_news}&exteditor=off">{$lang.ext.editors.switch_to_standard_editor}</a>{else}<br><a href="index.php?m=news&d=add&exteditor=off">{$lang.ext.editors.switch_to_standard_editor}</a>{/if}
			{include file="editors_`$_settings.ext_editor`.tpl" editor_doing="news"}
			{else}
			{if $_settings.ext_editor neq ""}<br>{if $modules[$index].mode eq "edit"}<br><a href="index.php?m=news&d=edit&nid={$modules[$index].id_news}&exteditor=on">{$lang.ext.editors.switch_to_ext_editor}</a>{else}<br><a href="index.php?m=news&d=add&exteditor=on">{$lang.ext.editors.switch_to_ext_editor}</a>{/if}{/if}
				<br />{$lang.text_news}:<br /> <textarea cols="50" rows="20" name="p_text_news" wrap="off" style="width:98%;">{$modules[$index].text_news}</textarea>
				{if $_settings.news_use_preview eq "1"}
					<br />
					<br />{$lang.module_news.preview_news}:<br /> <textarea cols="50" rows="10" name="p_preview_news" wrap="off" style="width:98%;">{$modules[$index].preview_news}</textarea>
				{/if}
			{/if}
		</td>
	</tr>
	<tr>
		<td width="30%">{$lang.common.category}:</td>
		<td width="70%">
			<select name="p_id_category_n">
				{section name=i loop=$modules[$index].ctgid}
					<option value="{$modules[$index].ctgid[i][0]}" {if $modules[$index].ctgidselected eq ""}{if $smarty.section.i.index eq 0}SELECTED{/if}{elseif $modules[$index].ctgid[i][0] eq $modules[$index].ctgidselected}SELECTED{/if}>{$modules[$index].ctgid[i][1]}</option>
				{/section}
			</select>
		</td>
	</tr>
	{if $special.ext_editor_on neq "1"}
	<tr>
		<td>
			{$lang.type_news}:
		</td>
		<td>
			<select name="p_type_news">
				<option value="0"{if $modules[$index].type_news eq "0"} SELECTED{/if}>{$lang.type_news_simple_text}</option>
				<option value="1"{if $modules[$index].type_news eq "1"} SELECTED{/if}>{$lang.type_news_HTML}</option>
			</select>
		</td>
	</tr>
	{/if}
	<tr>
		<td>
			{$lang.common.url}:
		</td>
		<td>
			<input type="text" name="p_filename" value="{$modules[$index].filename_news}" size="50" maxlength="255"> <a href="http://{$_settings.help_resource}/index.php?m=help&q=news_add_edit_url&lang={$_settings.default_language}" target="_blank">[?]</a>
		</td>
	</tr>
	<tr>
		<td>
			{$lang.common.seo_keywords}: 
		</td>
		<td>
			<input type="text" name="keywords_news" value="{$modules[$index].keywords_news}" size="50" maxlength="255">
		</td>
	</tr>
	<tr>
		<td>
			{$lang.common.seo_description}:
		</td>
		<td>
			<textarea cols="30" rows="2" name="description_news">{$modules[$index].description_news}</textarea>
		</td>
	</tr>
	{section name=attch loop=$_settings.news_attachments_count}
	<tr>
		<td>
			{if $modules[$index].attachments[attch].id neq ""}{$lang.common.attachment}{else}{$lang.common.attach}{/if} {$smarty.section.attch.index+1}:
		</td>
		<td>
			{include file="upload_attachment.tpl" attachment_number=$smarty.section.attch.index  attachment=$modules[$index].attachments}
		</td>
	</tr>
	{/section}
	{if $modules[$index].alttpl.news[0].name neq ""}
		<tr>
			<td>
				{$lang.common.template} ({$lang.common.page}):
			</td>
			<td>
				<select name="tplnews" size="1">
					{section name=i loop=$modules[$index].alttpl.news}
						<option value="{$modules[$index].alttpl.news[i].tpl|htmlescape}"{if $modules[$index].alttpl.news[i].tpl eq $sm.p.tplnews} selected{/if}>{$modules[$index].alttpl.news[i].name}</option>
					{/section}
				</select>
			</td>
		</tr>
	{/if}
	{$modules[$index].formadditionalhtml}
	<tr>
		<td colspan="2">
			<div align="center"><input type="submit" value="{$lang.submit}"></div>
		</td>
	</tr>
</table>
</form>
<p align="right"><a href="http://{$_settings.help_resource}/index.php?m=help&q=admin_news_addeditnews&lang={$_settings.default_language}" target="_blank">[? {$lang.help}]</a></p>
{include file="block_end.tpl"}
{/if}

{if $modules[$index].mode eq "list"}
{include file="block_begin.tpl"}
<div align="center"><form action="index.php">
<input type="hidden" name="m" value="news">
<input type="hidden" name="d" value="list">
<select name="ctg" size="1">
	<option value=""{if $modules[$index].ctg_id eq ""} SELECTED{/if}>{$lang.all_categories}</option>
	{section name=i loop=$modules[$index].ctg}
	<option value="{$modules[$index].ctg[i].id}"{if $modules[$index].ctg_id eq $modules[$index].ctg[i].id} SELECTED{/if}>{$modules[$index].ctg[i].title}</option>
	{/section}
</select><input type="submit" value="{$lang.show}">
</form></div>
{include file="common_admintable.tpl" table=$modules[$index].table}
<br>
<a href="index.php?m=news&d=add&ctg={$sm.g.ctg}">{$lang.add_news}</a>
(<a href="index.php?m=news&d=add&ctg={$sm.g.ctg}&exteditor=off">{$lang.common.html}</a>)<br>
<p align="right"><a href="http://{$_settings.help_resource}/index.php?m=help&q=admin_news_lisnews&lang={$_settings.default_language}" target="_blank">[? {$lang.help}]</a></p>
{include file="pagebar.tpl"}
{include file="block_end.tpl"}
{/if}

{if $modules[$index].mode eq "addctg"}
{include file="block_begin.tpl"}
<form action="index.php?m=news&d=postaddctg" method="post" name="post">															
{$lang.caption_category}: <input type="text" name="p_title_category" size="40" value="">
<br />{$lang.common.url}: <input type="text" name="p_filename" value="" size="50" maxlength="255">
<div align="right"><a href="javascript:;" onClick="set_visibility('extended_params')">{$lang.common.extended_parameters}</a></div>
<div style="display:none; width:100%;" id="extended_params">
{$lang.common.groups_can_modify}<br />
{include file="common_groupselector.tpl" groups=$modules[$index].groups_list selgroups=$modules[$index].modify_groups_category var="p_groups_modify"}
{if $_settings.allow_alike_news eq 1}
<div style="width:100%;"><input type="checkbox" name="p_no_alike_news" value="1"> {$lang.module_content.dont_show_alike_news}</div>
{/if}
</div>
{$modules[$index].formadditionalhtml}
<br><div align="center"><input type="submit" value="{$lang.submit}"></div>
</form>
<p align="right"><a href="http://{$_settings.help_resource}/index.php?m=help&q=admin_news_addeditcategory&lang={$_settings.default_language}" target="_blank">[? {$lang.help}]</a></p>
{include file="block_end.tpl"}
{/if}

{if $modules[$index].mode eq "editctg"}
{include file="block_begin.tpl"}
<form action="index.php?m=news&d=posteditctg&ctgid={$modules[$index].id_ctg}" method="post" name="post">															
{$lang.caption_category}: <input type="text" name="p_title_category" size="40" value="{$modules[$index].title_category}">
<br />{$lang.common.url}: <input type="text" name="p_filename" value="{$modules[$index].filename_category}" size="50" maxlength="255"> <a href="http://{$_settings.help_resource}/index.php?m=help&q=content_edit_category_url&lang={$_settings.default_language}" target="_blank">[?]</a>
<div align="right"><a href="javascript:;" onClick="set_visibility('extended_params')">{$lang.common.extended_parameters}</a></div>
<div style="display:none; width:100%;" id="extended_params">
{$lang.common.groups_can_modify}<br />
{include file="common_groupselector.tpl" groups=$modules[$index].groups_list selgroups=$modules[$index].modify_groups_category var="p_groups_modify"}
{if $_settings.allow_alike_news eq 1}
<div style="width:100%;"><input type="checkbox" name="p_no_alike_news" value="1"{if $modules[$index].category_no_alike_news eq "1"} checked{/if}> {$lang.module_content.dont_show_alike_news}</div>
{/if}
</div>
{$modules[$index].formadditionalhtml}
<br /><div align="center"><input type="submit" value="{$lang.submit}"></div>
</form>
<p align="right"><a href="http://{$_settings.help_resource}/index.php?m=help&q=admin_news_addeditcategory&lang={$_settings.default_language}" target="_blank">[? {$lang.help}]</a></p>
{include file="block_end.tpl"}
{/if}

{if $modules[$index].mode eq "admin"}
{include file="block_begin.tpl"}
<a href="index.php?m=news&d=add">{$lang.add_news}</a> (<a href="index.php?m=news&d=add&exteditor=off">{$lang.common.html}</a>)<br>
<br>
<a href="index.php?m=news&d=list">{$lang.list_news}</a><br>
<br>
<a href="index.php?m=news&d=listctg">{$lang.list_news_categories}</a><br>
<br>
<a href="index.php?m=news&d=addctg">{$lang.add_category}</a><br>
<br>
<a href="index.php?m=blocks&d=add&b=news&id=1&c={$lang.short_news_block}">{$lang.set_as_block} "{$lang.short_news_block}"</a>
<br>
<form action="index.php?m=menu&d=addouter" method="post">
<input type="hidden" name="p_url" value="index.php?m=news&d=listnews">
<input type="hidden" name="p_caption" value="{$lang.news}">
<input type="submit" value="{$lang.add_to_menu} - {$lang.news}">
</form>
{include file="block_end.tpl"}
{/if}

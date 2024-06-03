{if $modules[$index].mode eq 'add'}
{include file="block_begin.tpl"}
<form action="index.php?m=blocks&d=postadd" method="post" enctype="multipart/form-data">
<input type="hidden" name="p_block" value="{$modules[$index].block}">
<input type="hidden" name="p_id" value="{$modules[$index].id}">
<input type="hidden" name="p_doing" value="{$modules[$index].doing}">
<table width="100%">
	<tr>
		<td width="30%">{$lang.short_block_name}</td>
		<td width="70%"><input type="text" name="p_caption" value="{$modules[$index].caption_block}"></td>
	</tr>
	<tr>
		<td>{$lang.module_blocks.rewrite_title}</td>
		<td> <input type="text" name="p_rewrite_title" value="{$modules[$index].rewrite_title}"></td>
	</tr>
	<tr>
		<td>{$lang.panel}</td>
		<td>
			 <select name="p_panel">
				{section name=i loop=$_settings.sidepanel_count+1 start=1}
				<option value="{$smarty.section.i.index}"{if $smarty.section.i.index eq 1} SELECTED{/if}>{$lang.panel} {$smarty.section.i.index}</option>
				{/section}
				<option value="c">{$lang.center_panel}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>{$lang.can_view}</td>
		<td>
			<select name="p_level">
				<option value="0" SELECTED>{$lang.all_users}</option>
				<option value="1">{$lang.logged_users}</option>
				<option value="2">{$lang.power_users}</option>
				<option value="3">{$lang.administrators}</option>
				<option value="4">{$lang.common.nobody}</option>
			</select>
			<select name="p_thislevelonly">
				<option value="0" SELECTED>{$lang.common.and_higher}</option>
				<option value="1">{$lang.common.only}</option>
				<option value="-1">{$lang.common.and_lower}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>
			<select name="p_dont_show">
				<option value="0" SELECTED>{$lang.module_blocks.show_on}</option>
				<option value="1">{$lang.module_blocks.dont_show_on}</option>
			</select>
		</td>
		<td>
			 <select name="p_show_on">
				<option value="|0" SELECTED>{$lang.show_on_all_pages}</option>
				<option value="#index#|0">{$lang.module_blocks.show_on_indexpage}</option>
				<option value="account|0|cabinet">{$lang.my_cabinet}</option>
				{section name=i loop=$modules[$index].show_on}
					<option value="{$modules[$index].show_on[i].value}">{$modules[$index].show_on[i].caption}</option>
				{/section}
			</select>
		</td>
	</tr>
	<tr>
		<td>{$lang.common.device} </td>
		<td>
			<select name="show_on_device">
				<option value="" SELECTED>{$lang.common.all}</option>
				<option value="desktop">{$lang.common.desktop_device}</option>
				<option value="mobile">{$lang.common.mobile_device}</option>
				<option value="tablet">{$lang.common.tablet_device}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="checkbox" name="p_no_borders" value="1"> {$lang.module_blocks.no_borders}</td>
	</tr>
	{if $_settings.blocks_use_image eq "1"}
	<tr>
		<td colspan="2">{include file="upload_image.tpl"}</td>
	</tr>
	{/if}
	<tr>
		<td>{$lang.module_blocks.also_show_on_view_ids}</td>
		<td><textarea name="show_on_viewids" cols="15" rows="4">{$modules[$index].show_on_viewids}</textarea></td>
	</tr>
	<tr>
		<td>{$lang.module_blocks.also_can_view_selected_groups}</td>
		<td>{include file="common_groupselector.tpl" groups=$modules[$index].groups_all}
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center;"><input type="submit" value="{$lang.save}"></td>
	</tr>
</table>	
</form>
<p align="right"><a href="http://{$_settings.help_resource}/index.php?m=help&q=blocks_add&lang={$_settings.default_language}" target="_blank">[? {$lang.help}]</a></p>
{include file="block_end.tpl"}
{/if}					

{if $modules[$index].mode eq 'edit'}
{include file="block_begin.tpl"}
<form action="index.php?m=blocks&d=postedit" method="post" enctype="multipart/form-data">
<input type="hidden" name="p_id" value="{$modules[$index].id}">
<input type="hidden" name="p_old_pos" value="{$modules[$index].pos_block}">
<input type="hidden" name="p_old_pnl" value="{$modules[$index].panel_block}">
<table width="100%">
	<tr>
		<td width="30%">{$lang.short_block_name}</td>
		<td width="70%"><input type="text" name="p_caption" value="{$modules[$index].caption_block}"></td>
	</tr>
	<tr>
		<td>{$lang.module_blocks.rewrite_title}</td>
		<td> <input type="text" name="p_rewrite_title" value="{$modules[$index].rewrite_title}"></td>
	</tr>
	<tr>
		<td>{$lang.panel}</td>
		<td>
			 <select name="p_panel">
				{section name=i loop=$_settings.sidepanel_count+1 start=1}
				<option value="{$smarty.section.i.index}"{if $smarty.section.i.index eq $modules[$index].panel_block} SELECTED{/if}>{$lang.panel} {$smarty.section.i.index}</option>
				{/section}
				<option value="c"{if $modules[$index].panel_block eq "c"} SELECTED{/if}>{$lang.center_panel}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>{$lang.can_view}</td>
		<td>
			<select name="p_level">
				<option value="0"{if $modules[$index].level_block eq "0"} SELECTED{/if}>{$lang.all_users}</option>
				<option value="1"{if $modules[$index].level_block eq "1"} SELECTED{/if}>{$lang.logged_users}</option>
				<option value="2"{if $modules[$index].level_block eq "2"} SELECTED{/if}>{$lang.power_users}</option>
				<option value="3"{if $modules[$index].level_block eq "3"} SELECTED{/if}>{$lang.administrators}</option>
				<option value="4"{if $modules[$index].level_block eq "4"} SELECTED{/if}>{$lang.common.nobody}</option>
			</select>
			<select name="p_thislevelonly">
				<option value="0"{if $modules[$index].thislevelonly eq 0} SELECTED{/if}>{$lang.common.and_higher}</option>
				<option value="1"{if $modules[$index].thislevelonly eq 1} SELECTED{/if}>{$lang.common.only}</option>
				<option value="-1"{if $modules[$index].thislevelonly eq -1} SELECTED{/if}>{$lang.common.and_lower}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>
			<select name="p_dont_show">
				<option value="0"{if $modules[$index].dont_show_modif eq "0"} SELECTED{/if}>{$lang.module_blocks.show_on}</option>
				<option value="1"{if $modules[$index].dont_show_modif eq "1"} SELECTED{/if}>{$lang.module_blocks.dont_show_on}</option>
			</select>
		</td>
		<td>
			<select name="p_show_on">
				<option value="|0"{if $modules[$index].show_on_all eq "1"} SELECTED{/if}>{$lang.show_on_all_pages}</option>
				<option value="#index#|0"{if $modules[$index].show_on_module_block eq "#index#"} SELECTED{/if}>{$lang.module_blocks.show_on_indexpage}</option>
				{section name=i loop=$modules[$index].show_on}
					<option value="{$modules[$index].show_on[i].value}"{if $modules[$index].show_on[i].selected eq "1"} SELECTED{/if}>{$modules[$index].show_on[i].caption}</option>
				{/section}
			</select>
		</td>
	</tr>
	<tr>
		<td>{$lang.common.device}</td>
		<td>
			<select name="show_on_device">
				<option value=""{if $modules[$index].show_on_device eq "0"} SELECTED{/if}>{$lang.common.all}</option>
				<option value="desktop"{if $modules[$index].show_on_device eq "desktop"} SELECTED{/if}>{$lang.common.desktop_device}</option>
				<option value="mobile"{if $modules[$index].show_on_device eq "mobile"} SELECTED{/if}>{$lang.common.mobile_device}</option>
				<option value="tablet"{if $modules[$index].show_on_device eq "tablet"} SELECTED{/if}>{$lang.common.tablet_device}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="checkbox" name="p_no_borders" value="1"{if $modules[$index].no_borders eq "1"} checked{/if}> {$lang.module_blocks.no_borders}</td>
	</tr>
	{if $_settings.blocks_use_image eq "1"}
	<tr>
		<td colspan="2">{include file="upload_image.tpl"}</td>
	</tr>
	{/if}
	<tr>
		<td>{$lang.module_blocks.also_show_on_view_ids}</td>
		<td><textarea name="show_on_viewids" cols="15" rows="4">{$modules[$index].show_on_viewids}</textarea></td>
	</tr>
	<tr>
		<td>{$lang.module_blocks.also_can_view_selected_groups}</td>
		<td>{include file="common_groupselector.tpl" groups=$modules[$index].groups_all selgroups=$modules[$index].block_groups_sel}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center;"><input type="submit" value="{$lang.save}"></td>
	</tr>
</table>
</form>
<p align="right"><a href="http://{$_settings.help_resource}/index.php?m=help&q=blocks_edit&lang={$_settings.default_language}" target="_blank">[? {$lang.help}]</a></p>
{include file="block_end.tpl"}
{/if}

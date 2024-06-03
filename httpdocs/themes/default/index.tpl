{include file="page_header.tpl"}
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="area">
<tr>
    <td colspan="3" class="logobar">
	<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="20%">
			<a href="{if $special.page.scheme neq ""}{$special.page.scheme}{else}http{/if}://{$special.resource_url}"><img src="themes/{$special.theme}/images/logo.gif" border="0" alt="{$_settings.logo_text}"></a>
		</td>
		<td width="80%" valign="top" align="right">

			<form action="index.php" id="staticsearch" method="get">
              {$lang.search}:
                <input name="q" value="{$special.search_text}">
				<input type="hidden" name="m" value="search">
				<input type="submit" value="&gt;&gt;">
            </form>

		</td>
	</tr>
	{if $_settings.upper_menu_id neq ""}
	<tr>
		<td colspan="2">
		{include file="uppermenu.tpl"}
		</td>
	</tr>
	{/if}
	</table>
	</td>
</tr>
<tr>
<td valign="top" width="20%">

<!-- Ліва панель -->
{$special.document.panel[1].beforepanel}
{section name=mod_index loop=$modules step=1}
{if $modules[mod_index].panel eq "1"}
{assign var=index value=$smarty.section.mod_index.index}
{assign var=mod_name value=$modules[mod_index].module}
{$special.document.block[mod_index].beforeblock}
{include file="$mod_name.tpl"}
{$special.document.block[mod_index].afterblock}
{/if}
{/section}
{$special.document.panel[1].afterpanel}

	</td>
    <td valign="top" width="60%">

<!-- Середня панель -->
{$special.document.panel[0].beforepanel}
{include file="path.tpl"}
{assign var=loop_center_panel value=1}
{assign var=show_center_panel value=1}
{section name=mod_index loop=$modules step=1 start=1}
{if $_settings.main_block_position lt $loop_center_panel and $show_center_panel eq 1}
{assign var=show_center_panel value=0}
{assign var=index value=0}
{assign var=mod_name value=$modules[0].module}
{$special.document.block[0].beforeblock}
{include file="$mod_name.tpl"}
{$special.document.block[0].afterblock}
{/if}
{if $modules[mod_index].panel eq "center"}
{assign var=index value=$smarty.section.mod_index.index}
{assign var=mod_name value=$modules[mod_index].module}
{$special.document.block[mod_index].beforeblock}
{include file="$mod_name.tpl"}
{$special.document.block[mod_index].afterblock}
{assign var=loop_center_panel value=$loop_center_panel+1}
{/if}
{/section}
{if $show_center_panel eq 1}
{assign var=show_center_panel value=0}
{assign var=index value=0}
{assign var=mod_name value=$modules[0].module}
{$special.document.block[0].beforeblock}
{include file="$mod_name.tpl"}
{$special.document.block[0].afterblock}
{/if}
{$special.document.panel[0].afterpanel}
	
	</td>
    <td valign="top" width="20%">

<!-- Права панель -->
{$special.document.panel[2].beforepanel}
{section name=mod_index loop=$modules step=1}
{if $modules[mod_index].panel eq "2"}
{assign var=index value=$smarty.section.mod_index.index}
{assign var=mod_name value=$modules[mod_index].module}
{$special.document.block[mod_index].beforeblock}
{include file="$mod_name.tpl"}
{$special.document.block[mod_index].afterblock}
{/if}
{/section}
{$special.document.panel[2].afterpanel}

</td>
</tr>
<tr class="bottombar">
    <td class="copyrightbar">{$_settings.copyright_text}</td>
    <td>{if $_settings.bottom_menu_id neq ""}{include file="bottommenu.tpl"}{/if}</td>
    <td><div class="poweredby">Powered by <a href="http://simancms.org/" target="_blank">SiMan CMS</a></div></td>
</tr>
</table>
{include file="page_footer.tpl"}
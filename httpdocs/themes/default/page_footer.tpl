
{if $_settings.footer_static_text neq ""}{$_settings.footer_static_text}{/if}

{if $_settings.show_script_info eq "on"}<br><br>
<center>
<table width="80%" bgcolor="#ffffff">
	<tr>
		<td>
			{$lang.special.page_generation_time}: {$special.time.generation_time}
		</td>
		<td>
			{$lang.special.executed_queries_count}: {$special.sql.count}
		</td>
		<td>
			ID: {$special.page.viewid}
		</td>
	</tr>
</table>
</center>
{/if}

{$special.document.bodyend}</body>
</html>
{if $tmp.block[$index].blockend neq 1}{$special.document.block[$index].blockend}{assign var=tmp.block[$index].blockend value=1}{/if}
{if $modules[$index].borders_off neq "1"}
{if $modules[$index].panel eq "center"}
  {* якщо центральна панель *}
		</td>
	</tr>
</table>
{elseif $modules[$index].panel eq "1"}
  {* якщо ліва панель *}
		</td>
	</tr>
</table>
{else}
  {* якщо права панель *}
		</td>
	</tr>
</table>
{/if}
{/if}
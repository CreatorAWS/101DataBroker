<div class="adminbuttons{if $bar.class neq ""} {$bar.class}{/if}"{if $bar.style neq ""} style="{$bar.style}"{/if}>{if $bar.buttonbar_title neq ""}<span class="buttonbar_title">{$bar.buttonbar_title}</span>{/if}
{foreach name=adminbuttons_index from=$bar.buttons item=button key=button_name}
	<{$button.htmlelement}{foreach name=adminbuttonsattrs_index from=$button.attrs item=abattr key=abattr_name}{if $abattr neq ""} {$abattr_name}="{$abattr}"{/if}{/foreach}>{if $button.image neq ""}<img src="{$button.image}" />{/if}{$button.html}</{$button.htmlelement}>
{/foreach}
</div>
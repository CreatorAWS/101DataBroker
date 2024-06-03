<div class="adash-main{if $data.class neq ""} {$data.class}{/if}">
{foreach name=admindash_index from=$data.items item=item key=item_name}
	<div class="adash-element{if $item.class neq ""} {$bar.class}{/if}"{foreach name=admindashitemattrs_index from=$item.attrs item=attr key=attr_name}{if $attr neq ""} {$attr_name}="{$attr}"{/if}{/foreach}>
		{$item.htmltop}
		{$item.htmlimagestart}{if $item.image neq ""}<img class="adash-image" src="{$item.image}" />{/if}{$item.htmlimageend}
		<div class="adash-title">{$item.htmltitle}</div>
		{$item.htmlbottom}
	</div>
{/foreach}
<div style="clear:both;"></div>
</div>
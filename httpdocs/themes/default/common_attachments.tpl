{section name=attch loop=$attachments}
{if $smarty.section.attch.first}<div class="attachametntsall">{$lang.common.attachments}{/if}
<div class="attachment"><a href="{$attachments[attch].downloadurl}">{$attachments[attch].filename}</a> [{$attachments[attch].filesize}]</div>
{if $smarty.section.attch.last}</div>{/if}
{/section}
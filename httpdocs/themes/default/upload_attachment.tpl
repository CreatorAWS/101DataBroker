{if $attachment[$attachment_number].deleteurl neq ""}
	<a href="{$attachment[$attachment_number].deleteurl}" target="_blank">{$lang.common.delete} {$attachment[$attachment_number].filename}</a>
{else}
	<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="{$_settings.max_upload_filesize}" style="attachmentinput">
	<INPUT NAME="attachment{$attachment_number}" id="attachment{$attachment_number}" TYPE="file">
{/if}
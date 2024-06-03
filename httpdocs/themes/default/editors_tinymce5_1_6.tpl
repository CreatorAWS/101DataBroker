{if $noninit neq "1"}
{$special.editor.exthtml}
<script type="text/javascript" src="ext/editors/{$_settings.ext_editor}/tinymce.min.js"></script>
{/if}

{if $editor_doing eq "content"}
<script type="text/javascript">
{literal}
function siman_editor_insert(img)
{
	tinyMCE.execCommand('mceInsertContent',false,'<img src="files/img/'+img+'">');
}
{/literal}
</script>
<div align="right">
<a href="javascript:;" onclick="document.getElementById('content_images').style.display=(document.getElementById('content_images').style.display)?'':'none';">{$lang.add_image}</a>
</div>
<div style="overflow: auto; width: 100%; height: 100px; display:none;" id="content_images">
{section name=i loop=$modules[$index].images}
<a href="javascript:;" onmousedown="siman_editor_insert('{$modules[$index].images[i]}')">{$modules[$index].images[i]}</a><br />
{/section}
</div>
<br />{$lang.text_content}:<br />
{literal}
<textarea name="p_text_content" id="p_text_content" class="tinymce5_1_6" style="width: 98%; height:400px;">{/literal}{$modules[$index].text_content}{literal}</textarea>
{/literal}
{if $_settings.content_use_preview eq "1"}
<br />{$lang.module_content.preview_content}:<br />
<textarea name="p_preview_content" id="p_preview_content" class="tinymce5_1_6" style="width: 98%; height:200px;">{$modules[$index].preview_content}</textarea>
{/if}
{literal}
<script type="text/javascript">
	tinymce.init({fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt', selector: '.tinymce5_1_6'{/literal}{if $_settings.tinymce5_1_6_customization neq ""}{$_settings.tinymce5_1_6_customization}{else},menubar: false{/if}{literal}});
</script>
{/literal}
<input type="hidden" name="p_type_content" value="1">
{/if}

{if $editor_doing eq "news"}
<script type="text/javascript">
{literal}
function siman_editor_insert(img)
{
	tinyMCE.execCommand('mceInsertContent',false,'<img src="files/img/'+img+'">');
}
{/literal}
</script>
<div align="right">
<a href="javascript:;" onclick="document.getElementById('content_images').style.display=(document.getElementById('content_images').style.display)?'':'none';">{$lang.add_image}</a>
</div>
<div style="overflow: auto; width: 100%; height: 100px; display:none;" id="content_images">
{section name=i loop=$modules[$index].images}
<a href="javascript:;" onmousedown="siman_editor_insert('{$modules[$index].images[i]}')">{$modules[$index].images[i]}</a><br />
{/section}
</div>
<br />{$lang.text_news}:<br />
{literal}
<textarea name="p_text_news" id="p_text_news" class="tinymce5_1_6" style="width: 98%; height:400px;">{/literal}{$modules[$index].text_news}{literal}</textarea>
{/literal}
{if $_settings.news_use_preview eq "1"}
<br />{$lang.module_news.preview_news}:<br />
<textarea name="p_preview_news" id="p_preview_news" class="tinymce5_1_6" style="width: 98%; height:200px;">{$modules[$index].preview_news}</textarea>
{/if}
{literal}
<script type="text/javascript">
	tinymce.init({selector: '.tinymce5_1_6'{/literal}{if $_settings.tinymce5_1_6_customization neq ""}{$_settings.tinymce5_1_6_customization}{else},menubar: false{/if}{literal}});
</script>
{/literal}

<input type="hidden" name="p_type_news" value="1">
{/if}

{if $editor_doing eq "content_ctg"}
<br>
{literal}
<textarea name="p_preview_ctg" id="p_preview_ctg" class="tinymce5_1_6" style="width: 98%; height:400px;">{/literal}{$modules[$index].preview_ctg}{literal}</textarea>
{/literal}
{literal}
<script type="text/javascript">
	tinymce.init({selector: '.tinymce5_1_6'{/literal}{if $_settings.tinymce5_1_6_customization neq ""}{$_settings.tinymce5_1_6_customization}{else},menubar: false{/if}{literal}});
</script>
{/literal}
{/if}

{if $editor_doing eq "sendemail"}
	<textarea name="{$var}" id="{$var}" class="tinymce5_1_6" style="{if $style eq ""}width: 100%; height:400px;{else}{$style}{/if}">{$value}</textarea>
	{literal}
		<script type="text/javascript">

		</script>
	{/literal}
{/if}

{if $editor_doing eq "common"}

<textarea name="{$var}" id="{$var}" class="tinymce5_1_6" style="{if $style eq ""}width: 98%; height:400px;{else}{$style}{/if}">{$value}</textarea>
{literal}
<script type="text/javascript">
	tinymce.init({selector: '.tinymce5_1_6'{/literal}{if $_settings.tinymce5_1_6_customization neq ""}{$_settings.tinymce5_1_6_customization}{else}{$sm.tinymce5_1_6_default_params}{/if}{literal}});
</script>
{/literal}
{/if}

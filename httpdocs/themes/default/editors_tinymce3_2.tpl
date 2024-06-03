{if $noninit neq "1"}
{$special.editor.exthtml}
<script type="text/javascript" src="ext/editors/{$_settings.ext_editor}/tiny_mce.js"></script>
<script type="text/javascript">
{literal}
	tinyMCE.init({
		// General options
		mode : "none",
		theme : "advanced",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons2_add : "fontselect,fontsizeselect",
	
		theme_advanced_buttons1 : "justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,|,visualchars,nonbreaking,template,pagebreak",
		theme_advanced_buttons2 : "bold,italic,underline,strikethrough,sub,sup,|,charmap,iespell,media",
		theme_advanced_buttons3 : "undo,redo,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,hr,removeformat,visualaid",
		theme_advanced_buttons4 : "link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor,|,insertlayer,moveforward,movebackward,absolute,|,styleprops",
		theme_advanced_buttons5 : "tablecontrols,|,charmap,iespell,media,advhr,|,print,|fullscreen",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "none",
		theme_advanced_resizing : true
	});
{/literal}
</script>
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
<br />
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
<textarea name="p_text_content" id="p_text_content" style="width: 98%; height:400px;">{/literal}{$modules[$index].text_content}{literal}</textarea>
{/literal}
{if $_settings.content_use_preview eq "1"}
<br />{$lang.module_content.preview_content}:<br />
<textarea name="p_preview_content" id="p_preview_content" style="width: 98%; height:200px;">{$modules[$index].preview_content}</textarea>
{literal}
<script type="text/javascript">
tinyMCE.execCommand("mceAddControl", true, "p_preview_content");
</script>
{/literal}
{/if}
<script type="text/javascript">
tinyMCE.execCommand("mceAddControl", true, "p_text_content");
</script>
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
<br />
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
<textarea name="p_text_news" id="p_text_news" style="width: 98%; height:400px;">{/literal}{$modules[$index].text_news}{literal}</textarea>
{/literal}
{if $_settings.news_use_preview eq "1"}
<br />{$lang.module_news.preview_news}:<br />
<textarea name="p_preview_news" id="p_preview_news" style="width: 98%; height:200px;">{$modules[$index].preview_news}</textarea>
{literal}
<script type="text/javascript">
tinyMCE.execCommand("mceAddControl", true, "p_preview_news");
</script>
{/literal}
{/if}
<script type="text/javascript">
tinyMCE.execCommand("mceAddControl", true, "p_text_news");
</script>

<input type="hidden" name="p_type_news" value="1">
{/if}

{if $editor_doing eq "content_ctg"}
<br>
{literal}
<textarea name="p_preview_ctg" id="p_preview_ctg" style="width: 98%; height:400px;">{/literal}{$modules[$index].preview_ctg}{literal}</textarea>
{/literal}
<script type="text/javascript">
tinyMCE.execCommand("mceAddControl", true, "p_preview_ctg");
</script>
{/if}

{if $editor_doing eq "common"}
<br>
<textarea name="{$var}" id="{$var}" style="{if $style eq ""}width: 98%; height:400px;{else}{$style}{/if}">{$value}</textarea>
<script type="text/javascript">
tinyMCE.execCommand("mceAddControl", true, "{$var}");
</script>
{/if}
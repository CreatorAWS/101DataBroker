<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>{$special.document.headstart}{$special.document.headdef}
	<link href="themes/{$special.theme}/stylesheets.css" rel="stylesheet">
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script type="text/javascript" src="themes/{$special.theme}/script.js"></script>
{$_settings.meta_header_text}
{$special.document.headend}</head>
{config_load file="main.cfg"}
<body class="allbody"{if $special.body_onload neq ""} onload="{$special.body_onload}"{/if}{$special.document.bodymodifier}>{$special.document.bodystart}
{if $_settings.header_static_text neq ""}{$_settings.header_static_text}{/if}


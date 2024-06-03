<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>{$_settings.resource_title}{if $special.pagetitle neq ""} :: {$special.pagetitle}{/if}</title>
	<META content="text/html; charset={$lang.charset}" http-equiv=Content-Type>
	<META NAME="description" CONTENT="{$special.meta.description}"> 
	<META NAME="keywords" CONTENT="{$special.meta.keywords}">
	<LINK href="themes/{$special.theme}/stylesheetsprint.css" type=text/css rel=stylesheet>
</head>
{config_load file="main.cfg"}
<body bgcolor="#ffffff" text="#000000" onLoad="window.print()">

{assign var=mod_name value=$modules[0].module}
{assign var=index value=0}
{include file="$mod_name.tpl"}

</body>
</html>

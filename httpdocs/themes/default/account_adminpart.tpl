{if $modules[$index].mode eq "successregister"}
{include file="block_begin.tpl"}
{$lang.success_registration}.<br />
<a href="index.php?m=account&d=show">{$lang.you_can_enter}
{include file="block_end.tpl"}
{/if}   

{if $_settings.allow_forgot_password eq "1"}
{if $modules[$index].mode eq "getpasswd"}
{include file="block_begin.tpl"}
<table width="100%" cellspacing="2" cellpadding="2" border="0">
<form action="index.php" method="get">
<input type="hidden" name="m" value="account">
<input type="hidden" name="d" value="getpasswd2">
<tr>
    <td>{$lang.login_str}</td>
    <td><input type="text" name="login"></td>
</tr>
<tr>
    <td colspan="2" align="center"><input type="submit" value="{$lang.get_password}"></td>
</tr>
</form>
</table>
{include file="block_end.tpl"}
{/if}

{if $modules[$index].mode eq "getpasswd2"}
{include file="block_begin.tpl"}
{$modules[$index].secret_question}<br />
<table width="100%" cellspacing="2" cellpadding="2" border="0">
<form action="index.php?m=account&d=getpasswd3&login={$modules[$index].userdata_login}" method="post">
<tr>
    <td>{$lang.secret_answer_question}</td>
    <td><input type="text" name="p_answ"></td>
</tr>
<tr>
    <td>{$lang.new_password}</td>
    <td><input type="text" name="p_newpwd"></td>
</tr>
<tr>
    <td colspan="2" align="center"><input type="submit" value="{$lang.set_password}"></td>
</tr>
</form>
</table>
{include file="block_end.tpl"}
{/if}
{/if}

{if $modules[$index].mode eq "viewprivmsg"}
{include file="block_begin.tpl"}
<a href="index.php?m=account&d=sendprivmsg">{$lang.module_account.send_message}</a> :: {if $modules[$index].privmsg_folder eq "inbox"}<b>{/if}<a href="index.php?m=account&d=viewprivmsg&folder=inbox">{$lang.module_account.inbox}</a>{if $modules[$index].privmsg_folder eq "inbox"}</b>{/if} :: {if $modules[$index].privmsg_folder eq "outbox"}<b>{/if}<a href="index.php?m=account&d=viewprivmsg&folder=outbox">{$lang.module_account.outbox}</a>{if $modules[$index].privmsg_folder eq "outbox"}</b>{/if}<br>
<br>
{include file="common_admintable.tpl" table=$modules[$index].table}
{include file="block_end.tpl"}
{/if}

{if $modules[$index].mode eq "sendprivmsg"}
{include file="block_begin.tpl"}
<b><a href="index.php?m=account&d=sendprivmsg">{$lang.module_account.send_message}</a></b> :: <a href="index.php?m=account&d=viewprivmsg&folder=inbox">{$lang.module_account.inbox}</a> :: <a href="index.php?m=account&d=viewprivmsg&folder=outbox">{$lang.module_account.outbox}</a><br>
<br />
{if $modules[$index].error_message neq ""}
<span style="color:#ff0000;">{$modules[$index].error_message}</span><br />
{/if}
<form action="index.php?m=account&d=postsendprivmsg" method="post">
<table width="100%" cellspacing="2" cellpadding="2" border="0">
<tr>
    <td valign="top" width="50%">{$lang.module_account.recipient}</td>
    <td valign="top" width="50%"><input type="text" name="p_recipient" value="{$modules[$index].data.recipient}" style="width:100%"></td>
</tr>
<tr>
    <td valign="top">{$lang.module_account.message_theme}</td>
    <td valign="top"><input type="text" name="p_theme" value="{$modules[$index].data.theme}" style="width:100%"></td>
</tr>
<tr>
    <td colspan="2" valign="top">{$lang.module_account.message_text}<br />
<textarea cols="50" rows="15" name="p_text" style="width:100%">{$modules[$index].data.text}</textarea>	
	</td>
</tr>
<tr>
    <td colspan="2" valign="top" align="center">
	<input type="submit" value="{$lang.module_account.send_message}">
	</td>
</tr>
</table>
</form>
{include file="block_end.tpl"}
{/if}

{if $modules[$index].mode eq "readprivmsg"}
{include file="block_begin.tpl"}
<a href="index.php?m=account&d=sendprivmsg">{$lang.module_account.send_message}</a> :: <a href="index.php?m=account&d=viewprivmsg&folder=inbox">{$lang.module_account.inbox}</a> :: <a href="index.php?m=account&d=viewprivmsg&folder=outbox">{$lang.module_account.outbox}</a><br>
<br />
<table width="100%" cellspacing="2" cellpadding="2" border="0">
<tr>
    <td valign="top" width="50%">{if $modules[$index].folder_privmsg eq "outbox"}{$lang.module_account.recipient}{else}{$lang.module_account.sender}{/if}</td>
    <td valign="top" width="50%">{$modules[$index].message.user}</td>
</tr>
<tr>
    <td valign="top">{$lang.module_account.message_theme}</td>
    <td valign="top">{$modules[$index].message.theme}</td>
</tr>
<tr>
    <td valign="top">{$lang.common.time}</td>
    <td valign="top">{$modules[$index].message.time}</td>
</tr>
<tr>
    <td colspan="2" valign="top">{$modules[$index].message.body}</td>
</tr>
</table>
<form action="index.php?m=account&d=postsendprivmsg" method="post">
<input type="hidden" name="p_recipient" value="{$modules[$index].message.user}" style="width:100%">
<input type="hidden" name="p_theme" value="Re: {$modules[$index].message.theme}" style="width:100%">
<input type="hidden" name="p_body" value="{$modules[$index].message.rebody}" style="width:100%">
<input type="submit" value="{$lang.module_account.reply}">
</form>
{include file="block_end.tpl"}
{/if}


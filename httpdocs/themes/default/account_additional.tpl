{if $modules[$index].mode eq "register"}
{include file="block_begin.tpl"}
{if $modules[$index].message neq ""}<div class="error-message">{$modules[$index].message}</div>{/if}
<table width="100%" border="0">
<form action="index.php?m=account&d=postregister" method="post" class="registerForm">
<tr>
<td width="50%">{if $_settings.use_email_as_login neq "1"}{$lang.login_str}{else}{$lang.common.email}{/if}</td><td width="50%"><input type="text" name="p_login" value="{$sm.p.p_login|htmlescape}"></td>
</tr>
<tr>
<td>{$lang.password}</td><td><input type="password" name="p_password" value=""></td>
</tr>
<tr>
<td>{$lang.repeat_password}</td><td><input type="password" name="p_password2" value=""></td>
</tr>
{if $_settings.use_email_as_login neq "1"}
	<tr>
	<td>{$lang.email}</td><td><input type="text" name="p_email" value="{$sm.p.p_email}"></td>
	</tr>
{/if}
{if $_settings.account_disable_secret_question neq "1"}
	<tr>
	<td>{$lang.secret_question}</td><td><input type="text" name="p_question" value="{$sm.p.p_question|htmlescape}"></td>
	</tr>
	<tr>
	<td>{$lang.secret_answer_question}</td><td><input type="text" name="p_answer" value="{$sm.p.p_answer|htmlescape}"></td>
	</tr>
{/if}
{include file="additionregister.tpl"}
{if $_settings.use_protect_code eq "1"}
	<tr>
	<td>{$lang.module_account.enter_protect_code}</td><td><img src="ext/antibot/antibot.php?rand={$special.rand}"> <input type="text" name="p_protect_code" size="4"></td>
	</tr>
{/if}
<tr>
<td colspan="2" align="center"><input type="submit" value="{$lang.register}"></td>
</tr>
</form>
</table>
{include file="block_end.tpl"}
{/if}

{if $modules[$index].mode eq "change"}
{include file="block_begin.tpl"}
{if $modules[$index].message neq ""}{$modules[$index].message}{/if}
<form action="index.php?m=account&d=postchange" method="post" class="registerForm">
<table width="100%" cellspacing="0" cellpadding="0">
<tr><td width="50%">{$lang.login_str}</td><td width="50%"><strong>{$modules[$index].user_login}</strong></td></tr>
<tr><td colspan="2"><font size="1">{$lang.set_passwords_if_want_to_change}</font></td></tr>
<tr><td>{$lang.old_password}</td><td><input type="password" name="p_old_password" value=""></td></tr>
<tr><td>{$lang.password}</td><td><input type="password" name="p_password" value=""></td></tr>
<tr><td>{$lang.repeat_password}</td><td><input type="password" name="p_password2" value=""></td></tr>
<tr><td>{$lang.email}</td><td><input type="text" name="p_email" value="{$modules[$index].user_email}"></td></tr>
<tr><td>{$lang.secret_question}</td><td><input type="text" name="p_question" value="{$modules[$index].user_question}"></td></tr>
<tr><td>{$lang.secret_answer_question}</td><td><input type="text" name="p_answer" value="{$modules[$index].user_answer}"></td></tr>
<tr><td>{$lang.module_account.get_mail_from_admin}</td><td><input type="checkbox" name="p_get_mail" value="1"{if $modules[$index].user_get_mail eq 1} checked{/if}></td></tr>
{include file="additionchreginfo.tpl"}
<tr><td colspan="2" align="center"><input type="submit" value="{$lang.change}"></td></tr>
</table>
</form>
{include file="block_end.tpl"}
{/if}


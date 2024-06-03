{if $modules[$index].mode eq "cabinet"}
{include file="block_begin.tpl"}
	{$lang.wellcome}, <strong>{$userinfo.login}</strong>!
	<br />
	{if $_settings.allow_private_messages eq "1"}
	<br />
	<a href="index.php?m=account&d=sendprivmsg">{$lang.module_account.send_message}</a> :: <a href="index.php?m=account&d=viewprivmsg&folder=inbox">{$lang.module_account.inbox}{if $modules[$index].privmsgdata.inbox.unread gt 0} ({$modules[$index].privmsgdata.inbox.unread}/{$modules[$index].privmsgdata.inbox.all}){/if}</a> :: <a href="index.php?m=account&d=viewprivmsg&folder=outbox">{$lang.module_account.outbox}</a><br />
	{/if}
	{if $modules[$index].userlinkcount gt "0"}
	<br />
		{section name=i loop=$modules[$index].userlinks}
		<a href="{$modules[$index].userlinks[i].url}">{$modules[$index].userlinks[i].title}</a><br />
		{/section}
	{/if}
	<br />
	<form action="index.php?m=account&d=savenbook" method="post">
		<strong>{$lang.module_account.notebook}:</strong>
		<div align="center">
			<textarea cols="50" rows="10" name="p_notebook">{$modules[$index].notebook_text}</textarea>
			<br />
			<input type="submit" value="{$lang.save}">
		</div>
	</form>
	<br />
	{if $userinfo.level eq 3}
	<a href="index.php?m=admin">{$lang.control_panel}</a><br />
	{/if}
	<a href="index.php?m=account&d=change">{$lang.module_account.change_personal_info}</a><br />
	<a href="index.php?m=account&d=logout">{$lang.logout}</a>
{include file="block_end.tpl"}
{/if}
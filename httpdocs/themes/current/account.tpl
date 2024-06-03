{if $modules[$index].mode eq "show" or $modules[$index].mode eq "wronglogin"}
{if $modules[$index].panel eq "center"}
  {* main panel *}


        <div class="container-center animated slideInDown">
            <div class="view-header">
                <div class="header-icon">
                    <i class="pe page-header-icon pe-7s-unlock"></i>
                </div>
                <div class="header-title">
                    <h3>Login</h3>
                    <small>
                        Please enter your credentials to login.
                    </small>
                </div>
            </div>

            <div class="panel panel-filled">
                <div class="panel-body">

                    {if $modules[$index].mode eq "show" or $modules[$index].mode eq "wronglogin"}
                    {if $modules[$index].mode eq "wronglogin"}<div align="center" style="color:#ff0000;">{$lang.message_wrong_login}</div>{/if}

                        <form action="index.php?m=account&d=login" id="loginForm" method="post" class="login-form" role="form" novalidate="novalidate">
                            <button type="submit" class="bv-hidden-submit" style="display: none; width: 0px; height: 0px;"></button>
                            <div class="form-group has-feedback">
                                <label class="control-label" for="inputEmail">{if $_settings.use_email_as_login neq "1"}{$lang.login_str}{else}{$lang.common.email}{/if}</label>
                                <input type="email" name="login_d" placeholder="{if $_settings.use_email_as_login neq "1"}{$lang.login_str}{else}{$lang.common.email}{/if}" id="login_d" class="form-control" data-bv-field="email"><i style="display: none;" class="form-control-feedback" data-bv-icon-for="email"></i>
                            </div>


                            <div class="form-group has-feedback">
                                <label class="control-label" for="inputPassword">{$lang.password}</label>
                                <input type="password" name="passwd_d" placeholder="{$lang.password}" id="inputPassword" class="form-control" autocomplete="off" data-bv-field="password"><i style="display: none;" class="form-control-feedback" data-bv-icon-for="password"></i>
                            </div>
                            <div class="form-group">
                                <div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="autologin_d" value="1"> {$lang.common.auto_login}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div>
                                    <button class="btn btn-accent" type="submit">{$lang.login}</button>
                                    {if $_settings.allow_forgot_password eq "1"}
                                        <a href="index.php?m=account&d=getpasswd" class="btn btn-default">{$lang.forgot_password_question}</a>
                                    {/if}
                                </div>
                            </div>
                        </form>
                {if $_settings.allow_register eq "1" or $userinfo.id neq ""}

                <div class="row">
                    <div class="col-xs-12">
                        <div class="change-section">
                            <h3 class="heading">Not Registered?</h3>
                            <a class="btn btn-default btn-block" href="index.php?m=account&d=register">{$lang.register}</a>
                        </div>
                    </div>
                </div>
                {/if}

            </div>
        </div>
    </div>
{/if}
{else}
  {* side panels *}
{include file="block_begin.tpl"}
{if $userinfo.id eq ""}
<form action="index.php?m=account&d=login" method="POST" class="loginForm">

<table width="100%" cellspacing="0">
	<tr>
		<td>
			{if $_settings.use_email_as_login neq "1"}{$lang.login_str}{else}{$lang.common.email}{/if}:
		</td>
		<td><input name="login_d" type="text" size="10" style="width:100%;"></td>
	</tr>
	<tr>
		<td>{$lang.password}:</td>
		<td><input type="password" name="passwd_d" size="10" style="width:100%;"></td>
	<tr>
		<td colspan="2">
			<input type="checkbox" name="autologin_d" value="1"> {$lang.common.auto_login}
		</td>
	</tr>
	<tr>
		<td align="center">
			{if $_settings.allow_register eq "1" or $userinfo.id neq ""}
				<div align="center"><a href="index.php?m=account&d=register">{$lang.register}</a></div>
			{/if}
		</td>
		<td align="center"><input class="loginbutton" type="submit" value="{$lang.login}"></td>
	</tr>
</table>
<input type="hidden" name="p_goto_url" value="{$modules[$index].goto_url}">
</form>
{else}
<strong>{$userinfo.login}</strong>
<br />
{if $_settings.allow_private_messages eq "1"}
<a href="index.php?m=account&d=viewprivmsg&folder=inbox">{$lang.module_account.inbox}</a><br />
{/if}
{if $userinfo.level eq 3}
<a href="index.php?m=admin">{$lang.control_panel}</a><br />
{/if}
<a href="index.php?m=account&d=cabinet">{$lang.my_cabinet}</a><br />
<a href="index.php?m=account&d=logout">{$lang.logout}</a>
{/if}
{include file="block_end.tpl"}
{/if}
{/if}


{include file="account_cabinet.tpl"}
{include file="account_additional.tpl"}
{include file="account_adminpart.tpl"}
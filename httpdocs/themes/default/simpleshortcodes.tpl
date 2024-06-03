{if $modules[$index].mode eq "admin"}
{include file="block_begin.tpl"}
[[time][]] - {$lang.module_simpleshortcodes.time}<br />
[[time][<a href="http://php.net/manual/en/function.strftime.php">FORMAT</a>]] - {$lang.module_simpleshortcodes.time}<br />
[[date][]] - {$lang.module_simpleshortcodes.date}<br />
[[date][<a href="http://php.net/manual/en/function.strftime.php">FORMAT</a>]] - {$lang.module_simpleshortcodes.date}<br />
[[time][][-24..+24]] - {$lang.module_simpleshortcodes.time}<br />
[[time][<a href="http://php.net/manual/en/function.strftime.php">FORMAT</a>][-sec..+sec]] - {$lang.module_simpleshortcodes.time}<br />
[[date][][-24..+24]] - {$lang.module_simpleshortcodes.date}<br />
[[date][<a href="http://php.net/manual/en/function.strftime.php">FORMAT</a>][-sec..+sec]] - {$lang.module_simpleshortcodes.date}<br />
[[userinfo][login]] - {$lang.module_simpleshortcodes.userinfo} ({$lang.module_simpleshortcodes.login})<br />
[[userinfo][email]] - {$lang.module_simpleshortcodes.userinfo} ({$lang.module_simpleshortcodes.email})<br />
[[settings][resource_url]]<br />
[[settings][resource_title]]<br />
[[settings][administrators_email]]<br />
[[uppermenu][ID]]<br />
[[bottommenu][ID]]<br />
[[changetheme][themename]]<br />
[[keywords][keyword1,keyword2]]<br />
[[description][description text]]<br />
<br />
{include file="block_end.tpl"}
{/if}						
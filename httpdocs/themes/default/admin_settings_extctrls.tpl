{section name=tmpextctrls loop=$modules[$index].available_modes}
	{assign var=tmpmode value=$modules[$index].available_modes[tmpextctrls].mode}
	{if $modules[$index].extmodes.$tmpmode.show_settings.$name_settings eq 1}
		<a href="index.php?m=admin&d=remsettings&destmode={$modules[$index].available_modes[tmpextctrls].mode}&name={$name_settings}" title="{$modules[$index].available_modes[tmpextctrls].mode}">[-{$modules[$index].available_modes[tmpextctrls].shortcut}]</a>
	{else}
		<a href="index.php?m=admin&d=copysettings&destmode={$modules[$index].available_modes[tmpextctrls].mode}&name={$name_settings}" title="{$modules[$index].available_modes[tmpextctrls].mode}">[+{$modules[$index].available_modes[tmpextctrls].shortcut}]</a>
	{/if}
{/section}
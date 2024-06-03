{assign var=m value=$modules[$index]}
{literal}<style>#block0title{display:none;}</style>{/literal}
<div class="steps">
    <ul>
        {section name=i loop=$m.wizardsteps}
            {if $smarty.section.i.index neq 0}
                <li class="line {if $smarty.section.i.index eq $m.wizardsprogress.current_step OR $smarty.section.i.index-1 eq $m.wizardsprogress.current_step}active{/if}"></li>
            {/if}
            <li {if $smarty.section.i.index eq $m.wizardsprogress.current_step OR $smarty.section.i.index lt $m.wizardsprogress.current_step}class="active"{/if}>{$smarty.section.i.index+1}</li>
        {/section}
    </ul>
    <div class="wizard_hint">{$m.wizard_hint}</div>
</div>

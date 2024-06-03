{assign var=m value=$modules[$index]}

<div class="steps">
    <ul>
        {section name=i loop=$m.wizardsteps}
            {if $smarty.section.i.index neq 0}
                <li class="line {if $smarty.section.i.index eq $m.active_step OR $smarty.section.i.index-1 eq $m.active_step}active{/if}"></li>
            {/if}
            <li {if $smarty.section.i.index eq $m.active_step OR $smarty.section.i.index lt $m.active_step}class="active"{/if}>{$smarty.section.i.index+1}</li>
        {/section}
    </ul>
    <div class="wizard_hint">{$m.wizard_hint}</div>
</div>

{assign var=m value=$modules[$index]}

<div class="block">
    <div class="block-content-outer">
        {include file="common_adminpanel.tpl" panelblocks=$m.uipanel}

        <div style="clear:both;"></div>
    </div>
</div>
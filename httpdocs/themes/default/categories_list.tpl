{assign var=m value=$modules[$index]}
<div class="categoriest-list-section">
    <div class="wrp">
        <ul class="category">
            {section name = i loop = $data}
                <li class="{if $data[i].selected} active{/if}">
                    <div class="flex">
                        <a href="{$data[i].url}" class="edit">{$data[i].title} <span>{$data[i].count}</span></a>
                    </div>
                </li>
            {/section}
        </ul>
    </div>
</div>
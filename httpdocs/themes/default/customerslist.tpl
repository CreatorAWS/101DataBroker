<style>{literal}
    .block .block-content-outer{
        background: none;
        box-shadow: none!important;
        border:0px solid!important;
    }
    {/literal}
</style>
{if $data.noinfo neq "Nothing Found" AND $data.noinfo neq "Import in Progress"}
<div class="panel panel-profile list-view">
    <div class="panel-heading">
        <div class="customer-wrapper">
            <div class="media">
                <div class="media-left">
                    <div class="vehicle_condition">{$data.initials}</div>
                </div>
                <div class="media-body">
                    {if $data.url neq ""}
                        <h4 class="media-heading"><a href="{$data.url}">{$data.name}</a></h4>
                    {else}
                        <h4 class="media-heading">{$data.name}</h4>
                    {/if}
                    <p class="media-usermeta">
                        {if $data.cellphone neq ""}
                            <span class="cellphone">
                        {if $data.cellphone_url neq ""}
                            {$data.cellphone_url}
                        {else}
                            <i class="fa fa-phone" aria-hidden="true" style="font-size:16px; margin-right: 10px;"></i>Cellphone: {$data.cellphone}
                        {/if}
                        </span>
                        {/if}
                        {if $data.email neq ""}
                            <span class="email"><i class="fa fa-envelope" aria-hidden="true" ></i>Email: {$data.email}</span>
                        {/if}
                        <span class="tags_list">
                            {section name=i loop=$data.tags}
						        <a class="label {if $data.tags[i].class eq ""}label-info{else}{$data.tags[i].class}{/if}" href="{$data.tags[i].url}">{$data.tags[i].title}</a>
					        {/section}
                        <span>
                    </p>
                </div>
            </div><!-- media -->
            <ul class="panel-options">
                {if $data.edit neq ""}
                    <li>
                        <a href="{$data.edit}" class="tooltips">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                        </a>
                    </li>
                {/if}
                {if $data.delete neq ""}
                    <li>
                        <a href="javascript:;" onclick="button_msgbox('{$data.delete}', 'Are you sure?');" class="tooltips">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </a>
                    </li>
                {/if}
                <li>
                    <div class="checkbox">
                        <input type="checkbox" name="ids[]" value="{$data.id}" onclick="checksmsblast()" class="admintable-control-checkbox" />
                    </div>
                </li>
            </ul>
        </div>
        <div class="panel-body people-info">
            <div class="row flex">
                {section name=i loop=$data.boxes}
                    <div class="col-sm-2 counter-{$smarty.section.i.index}">
                        <div class="info-group">
                            {if $data.currenmode neq "contactlist"}
                                <label>{$data.boxes[i].label}: </label>

                                {if $data.boxes[i].url neq ""}
                                    <span><a href="{$data.boxes[i].url}">{$data.boxes[i].value}</a></span>
                                {else}
                                    <span>{$data.boxes[i].value}</span>
                                {/if}
                            {/if}
                        </div>
                    </div>
                {/section}
            </div><!-- row -->
        </div>
    </div><!-- panel-heading -->

</div>
{else}
    <div class="panel panel-profile list-view">
        <div class="panel-heading">
            <div class="media">
                <div class="media-body">
                    <h4 class="media-heading">{$data.noinfo}</h4>
                </div>
            </div><!-- media -->
        </div><!-- panel-heading -->
    </div>
{/if}
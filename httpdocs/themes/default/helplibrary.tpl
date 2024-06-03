<style>{literal}
    .block .block-content-outer{
        background: none;
        box-shadow: none!important;
        border:0px solid!important;
    }
    {/literal}
</style>

<div class="row videodashboard helpvideos">
    {section name=i loop=$data.library}
        <div class="video_item col-md-4">
            <div class="videoscreen-wrapper">
                <div class="videoscreen">
                    <video id="sampleMovie" src="{$data.library[i].video}" controls style="width:100%;"></video>
                </div>
                <div class="videoinfo">
                    <div class="customer-info">
                        {if $data.library[i].title neq ""}
                            <span>{$data.library[i].title}</span>
                        {/if}
                        {if $data.library[i].comment neq ""}
                            <span>{$data.library[i].comment}</span>
                        {/if}
                    </div>
                    <div class="received-info">
                        {if $data.isallowedtomanageassets eq 1}
                            <div class="received-info vide-status">
                                <div class="pull-right" ><a href="{$data.library[i].deletelink}" class="publish_button" style="background: #e30202"><i class="fa fa-trash"></i> Delete</a></div>
                                <div class="pull-right" style="margin-right: 10px;"><a href="{$data.library[i].editlink}" style="background: #666" class="publish_button"><i class="fa fa-edit"></i> Edit</a></div>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    {/section}
</div>



{if $data.noinfo neq "Nothing Found"}
    <div class="col-md-4 col-xs-12">
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
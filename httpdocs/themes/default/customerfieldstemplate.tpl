{assign var=m value=$modules[$index]}
<div class="categorysection">
    {section name = i loop = $data}
        <div class="wrp">
            <div class="title">
                <h3>{$data[i].category.title}</h3>
            </div>
            <div class="managelinks">
                <a href="{$data[i].category.editor_url}" class="edit"><img src="themes/default/images/editvideoico.png"/></a>
                <a href="javascript:;" onclick="button_msgbox('{$data[i].category.delete_url}', 'Are you sure?');" class="edit"><img src="themes/default/images/deletevideoico.png"/></a>
            </div>
            <div class="addbtn btn ab-button"><a href="{$data[i].category.addfieldurl}">Add Tag</a></div>

            <div class="fields template_fields">
                <div class="buttons_wrp">
                    {section name = j loop = $data[i].fields.en}
                        <div class="title">
                            <div class="ab-button">
                                <a href="{$data[i].fields.en[j].url}">{$data[i].fields.en[j].title}</a>
                                <a href="{$data[i].fields.en[j].editor_url}" class="edit"><img src="themes/default/images/editvideoico.png"/></a>
                                <a href="javascript:;" onclick="button_msgbox('{$data[i].fields.en[j].delete_url}', 'Are you sure?');" class="edit"><img src="themes/default/images/deletevideoico.png"/></a>
                            </div>
                        </div>
                    {/section}
                </div>
            </div>

        </div>
    {/section}
</div>
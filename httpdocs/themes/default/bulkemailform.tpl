{assign var=m value=$modules[$index]}
<div>Subject<sup class="adminform-required">*</sup></div>
<input class="form-control" type="text" name="subject" value="{$m.email.subject}" id="subject">

{if $data.var neq ""}
    {assign var = form_name value=$data.var}
{else}
    {assign var = form_name value="text"}
{/if}

<div style="margin-top: 10px;padding-top:10px; display: table;">Message<sup class="adminform-required">*</sup></div>
{include file="editors_`$_settings.ext_editor`.tpl" editor_doing="sendemail" var="$form_name" value=$m.email.text}

<script>
    $(function() {ldelim}

        var tags_list;
        var images_list;

        function getTags()
        {ldelim}
            $.ajax({ldelim}
                type: 'GET',
                url: 'index.php?m=settings&d=getavailabletags&theonepage=1',
                async: false,
                success: function(data){ldelim}
                    tags_list = JSON.parse(decodeURIComponent(data));
                    {rdelim},
                {rdelim});
            {rdelim}

        function getImages()
        {ldelim}
            $.ajax({ldelim}
                type: 'GET',
                url: '{$data.getimageslisturl}',
                async: false,
                success: function(data){ldelim}
                    images_list = JSON.parse(decodeURIComponent(data));
                    {rdelim},
                {rdelim});
            {rdelim}

        getTags();
        getImages();

        tinymce.PluginManager.add("inserttag", function (editor, url) {ldelim}

            for (let i = 0; i < tags_list.availabletags.ids.length; i++)
            {ldelim}
                editor.ui.registry.addMenuItem(tags_list.availabletags.ids[i], {ldelim}
                    text: tags_list.availabletags.titles[i],
                    value: tags_list.availabletags.ids[i],
                    onAction: function () {ldelim}
                        editor.insertContent(tags_list.availabletags.ids[i]);
                        {rdelim}
                    {rdelim});
                {rdelim}

            {rdelim});

        image_list_for_menu = [];

        if (images_list !== null)
        {ldelim}
            for (let i = 0; i < images_list.asset_ids.length; i++)
            {ldelim}
                image_list_for_menu.push(
                    {ldelim}title: images_list.asset_titles[i], value:images_list.asset_ids[i]{rdelim}
                );
                {rdelim}
            {rdelim}

        tinymce.init({ldelim}
            selector: '.tinymce5_1_6'{if $_settings.tinymce5_1_6_customization neq ""}{$_settings.tinymce5_1_6_customization}{else}{$sm.tinymce5_1_6_default_params}{/if},
            menu: {ldelim}
                insert_tag: {ldelim}title: 'Insert Tag', items: tags_list.menu{rdelim},
                {rdelim},

            image_list: image_list_for_menu,
            height : "380"
            {rdelim});

        {rdelim});

</script>
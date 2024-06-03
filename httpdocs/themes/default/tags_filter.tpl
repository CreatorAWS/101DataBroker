<div class="demo">
        <div class="control-group">
                <input type="text" id="input-tags" name="tags_selected" class="tags-for-regularpages" value="{$data.values_selected}">
        </div>
</div>
<script>{literal}
    $(document).ready(function() {
        $('#input-tags').selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: false,
            create: false,
            valueField: 'id',
            labelField: 'title',
            searchField: ['title'],
            options: [{/literal}
                {section name=i loop=$data.tags}
                {literal}{id: {/literal}{$data.tags[i].value}{literal}, title: '{/literal}{$data.tags[i].title}{literal}'},{/literal}
                {/section}{literal}],
        });
    });
    {/literal}
</script>



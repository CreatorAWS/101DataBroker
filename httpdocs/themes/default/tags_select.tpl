<select id="js-tags-selector" class="tags-for-regularpages" name="tags_selected[]" multiple>
    {section name=i loop=$data.tags}
        <option value="{$data.tags[i].value}" {if $data.tags[i].checked eq 1}SELECTED{/if}>{$data.tags[i].title}</option>
    {/section}
</select>

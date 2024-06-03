{section name = i loop = $data}
    <div class="review-wrapper">
        <div class="reviewAuthor">
            {if $data[i].author_img neq ""}
                <div class="reviewIcon">
                    <img src="{$data[i].author_img}" />
                </div>
            {/if}
            <div class="reviewName">
                {if $data[i].author_url neq ""}
                    <a href="{$data[i].author_url}" target="_blank">{$data[i].author_name}</a>
                {else}
                    {$data[i].author_name}
                {/if}
                <div class="reviewInfo">
                    {if $data[i].rating neq ""}
                        <div class="reviewRating"><span>Rating - </span> {$data[i].rating}</div>
                    {/if}
                    <div class="reviewTime">{$data[i].time}</div>
                </div>
            </div>
        </div>
        <div class="reviewText">{$data[i].text}</div>
    </div>
{/section}
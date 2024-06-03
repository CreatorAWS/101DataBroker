{if $modules[$index].pages.pages gt "1"}
	<ul class="pagination">
		{if $modules[$index].pages.selected neq 1}
			<li>
				<a href="{$modules[$index].pages.url}&from={math equation="x*y" x=$modules[$index].pages.interval y=$modules[$index].pages.selected-2}">
					<svg width="7px" height="10px" viewBox="0 0 7 10" version="1.1" xmlns="http://www.w3.org/2000/svg">
						<g id="Symbols" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<g id="pagination" transform="translate(-31.000000, -11.000000)" fill="CurrentColor">
								<g id="Group-2">
									<g id="ico/arrow-down" transform="translate(35.000000, 16.000000) rotate(-270.000000) translate(-35.000000, -16.000000) translate(25.000000, 6.000000)">
										<path d="M14.2857143,13.5714286 C14.3526786,13.5714286 14.4335938,13.5546875 14.5284598,13.5212054 C14.6233259,13.4877232 14.7098214,13.4319196 14.7879464,13.3537946 C14.9330357,13.2087054 15.0055804,13.0412946 15.0055804,12.8515625 C15.0055804,12.6618304 14.9330357,12.4944196 14.7879464,12.3493304 L10.5022321,8.06361607 C10.3571429,7.9296875 10.1897321,7.86272321 10,7.86272321 C9.81026786,7.86272321 9.64285714,7.9296875 9.49776786,8.06361607 L5.21205357,12.3493304 C5.06696429,12.4944196 4.99441964,12.6618304 4.99441964,12.8515625 C4.99441964,13.0412946 5.06696429,13.2087054 5.21205357,13.3537946 C5.35714286,13.4988839 5.52455357,13.5714286 5.71428571,13.5714286 C5.90401786,13.5714286 6.07142857,13.4988839 6.21651786,13.3537946 L10,9.5703125 L13.7834821,13.3537946 C13.8616071,13.4319196 13.9481027,13.4877232 14.0429688,13.5212054 C14.1378348,13.5546875 14.21875,13.5714286 14.2857143,13.5714286 Z" transform="translate(10.000000, 10.717076) scale(1, -1) translate(-10.000000, -10.717076) "></path>
									</g>
								</g>
							</g>
						</g>
					</svg>
				</a>
			</li>
		{/if}
		{if $modules[$index].pages.pages lte 20}
			{section name="i" loop=5000 max=$modules[$index].pages.pages start="1"}
				<li{if $smarty.section.i.index eq $modules[$index].pages.selected} class="active"{/if}>
					<a href="{$modules[$index].pages.url}&from={math equation="x*y" x=$modules[$index].pages.interval y=$smarty.section.i.index-1}">{$smarty.section.i.index}</a>
				</li>
			{/section}
		{elseif $modules[$index].pages.selected lt 9 or $modules[$index].pages.selected gt $modules[$index].pages.pages-9}
			{section name="i" loop=10 max=$modules[$index].pages.pages start="1"}
				<li{if $smarty.section.i.index eq $modules[$index].pages.selected} class="active"{/if}>
					<a href="{$modules[$index].pages.url}&from={math equation="x*y" x=$modules[$index].pages.interval y=$smarty.section.i.index-1}">{$smarty.section.i.index}</a>
				</li>
			{/section}
			<li class="disabled"><a>...</a></li>
			{section name="i" loop=$modules[$index].pages.pages+1 start=$modules[$index].pages.pages-9}
				<li{if $smarty.section.i.index eq $modules[$index].pages.selected} class="active"{/if}>
					<a href="{$modules[$index].pages.url}&from={math equation="x*y" x=$modules[$index].pages.interval y=$smarty.section.i.index-1}">{$smarty.section.i.index}</a>
				</li>
			{/section}
		{else}
			{section name="i" loop=4 max=$modules[$index].pages.pages start="1"}
				<li{if $smarty.section.i.index eq $modules[$index].pages.selected} class="active"{/if}>
					<a href="{$modules[$index].pages.url}&from={math equation="x*y" x=$modules[$index].pages.interval y=$smarty.section.i.index-1}">{$smarty.section.i.index}</a>
				</li>
			{/section}
			<li class="disabled"><a>...</a></li>
			{section name="i" loop=$modules[$index].pages.selected+4  start=$modules[$index].pages.selected-4}
				<li{if $smarty.section.i.index eq $modules[$index].pages.selected} class="active"{/if}>
					<a href="{$modules[$index].pages.url}&from={math equation="x*y" x=$modules[$index].pages.interval y=$smarty.section.i.index-1}">{$smarty.section.i.index}</a>
				</li>
			{/section}
			<li class="disabled"><a>...</a></li>
			{section name="i" loop=$modules[$index].pages.pages+1 start=$modules[$index].pages.pages-4}
				<li{if $smarty.section.i.index eq $modules[$index].pages.selected} class="active"{/if}>
					<a href="{$modules[$index].pages.url}&from={math equation="x*y" x=$modules[$index].pages.interval y=$smarty.section.i.index-1}">{$smarty.section.i.index}</a>
				</li>
			{/section}
		{/if}
		{if $modules[$index].pages.selected neq $modules[$index].pages.pages}
			<li>
				<a href="{$modules[$index].pages.url}&from={math equation="x*y" x=$modules[$index].pages.interval y=$modules[$index].pages.selected}">
					<svg width="7px" height="10px" viewBox="0 0 7 10" version="1.1" xmlns="http://www.w3.org/2000/svg">
						<g id="Symbols" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<g id="pagination" transform="translate(-269.000000, -11.000000)" fill="CurrentColor" fill-rule="nonzero">
								<g id="Group-2">
									<g id="ico/arrow-down" transform="translate(272.000000, 16.000000) rotate(-90.000000) translate(-272.000000, -16.000000) translate(262.000000, 6.000000)">
										<path d="M14.2857143,13.5714286 C14.3526786,13.5714286 14.4335938,13.5546875 14.5284598,13.5212054 C14.6233259,13.4877232 14.7098214,13.4319196 14.7879464,13.3537946 C14.9330357,13.2087054 15.0055804,13.0412946 15.0055804,12.8515625 C15.0055804,12.6618304 14.9330357,12.4944196 14.7879464,12.3493304 L10.5022321,8.06361607 C10.3571429,7.9296875 10.1897321,7.86272321 10,7.86272321 C9.81026786,7.86272321 9.64285714,7.9296875 9.49776786,8.06361607 L5.21205357,12.3493304 C5.06696429,12.4944196 4.99441964,12.6618304 4.99441964,12.8515625 C4.99441964,13.0412946 5.06696429,13.2087054 5.21205357,13.3537946 C5.35714286,13.4988839 5.52455357,13.5714286 5.71428571,13.5714286 C5.90401786,13.5714286 6.07142857,13.4988839 6.21651786,13.3537946 L10,9.5703125 L13.7834821,13.3537946 C13.8616071,13.4319196 13.9481027,13.4877232 14.0429688,13.5212054 C14.1378348,13.5546875 14.21875,13.5714286 14.2857143,13.5714286 Z" id="" transform="translate(10.000000, 10.717076) scale(1, -1) translate(-10.000000, -10.717076) "></path>
									</g>
								</g>
							</g>
						</g>
					</svg>
				</a>
			</li>
		{/if}
	</ul>
{/if}
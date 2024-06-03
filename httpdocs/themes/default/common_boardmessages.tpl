{if $postfix eq ""}
	{assign var=postfix value=$board.postfix}
{/if}

<script type="text/javascript">
function  boardmessages_msgbox{$postfix}(url, message)
{literal}
	{
		if (confirm(message))
			{
				setTimeout(function() { document.location.href = url; }, 30);
			}
	}
{/literal}

function  set_boardmessages_style{$postfix}()
{literal}
	{
	}
{/literal}

</script>

<div class="boardmessages">
	{section name=brdmsgindex loop=$board.rows}
	{if $board.type eq "forum"}
		<div class="boardmessages-row-{$board.type} {cycle values="boardmessages-row-header-first, boardmessages-row-header-second"}{if $board.rows[brdmsgindex].customstyle neq ""} {$board.rows[brdmsgindex].customstyle}{/if}">
			<div class="boardmessages-row-header-{$board.type}">
				<span class="boardmessages-row-header-datetime-{$board.type}">{$board.rows[brdmsgindex].time}</span>
				<a class="boardmessages-row-header-number-{$board.type}" href="{$special.page.url}#message-{$board.rows[brdmsgindex].showmumber}" id="message-{$board.rows[brdmsgindex].showmumber}">#{$board.rows[brdmsgindex].showmumber}</a>
			</div>
			<div class="boardmessages-row-body">
				<div class="boardmessages-row-infopanel">
					<div class="boardmessages-row-infopanel_sender">{if $board.rows[brdmsgindex].sender.url neq ""}<a class="boardmessages-row-header-title" href="{$board.rows[brdmsgindex].sender.url}">{/if}
						{$board.rows[brdmsgindex].sender.name}
					{if $board.rows[brdmsgindex].sender.url neq ""}</a>{/if}</div>
					{if $board.rows[brdmsgindex].sender.status neq ""}<div class="boardmessages-row-status">{$board.rows[brdmsgindex].sender.status}</div>{/if}
					{if $board.rows[brdmsgindex].sender.statusimage neq ""}<div class="boardmessages-row-statusimage"><img src="{$board.rows[brdmsgindex].sender.statusimage}" /></div>{/if}
					{if $board.rows[brdmsgindex].sender.avatar neq ""}<div class="boardmessages-row-avatar"><img src="{$board.rows[brdmsgindex].sender.avatar}" /></div>{/if}
					{section name=infoindex loop=$board.rows[brdmsgindex].info}
						<div class="boardmessages-row-infopanel-item">
							{$board.rows[brdmsgindex].info[infoindex].caption}: 
							{if $board.rows[brdmsgindex].info[infoindex].url neq ""}<a href="{$board.rows[brdmsgindex].info[infoindex].url}">{/if}
								{$board.rows[brdmsgindex].info[infoindex].name}
							{if $board.rows[brdmsgindex].info[infoindex].url neq ""}</a>{/if}
						</div>
					{/section}
				</div>
				<div class="boardmessages-row-message-forum">
					{if $board.rows[brdmsgindex].title neq ""}<div class="boardmessages-row-title">{$board.rows[brdmsgindex].title}</div>{/if}
					{$board.rows[brdmsgindex].message}
					<div style="clear:both"></div>
					{if $board.rows[brdmsgindex].signature neq ""}<div class="boardmessages-row-signature">{$board.rows[brdmsgindex].signature}</div>{/if}
					<div style="clear:both"></div>
				</div>
			</div>
			<div style="clear:both"></div>
			{if $board.rows[brdmsgindex].buttonscount gt 0}
			<div class="boardmessages-row-buttonbar">
				{section name=btnbrdmsgbtnsindex loop=$board.rows[brdmsgindex].buttons}
					{if $board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].messagebox eq 1}
					<a href="javascript:;" onclick="boardmessages_msgbox{$postfix}('{$board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].url}', '{$board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].message}')">
					{else}
					<a href="{$board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].url}">
					{/if}
						{if $board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].image neq ""}<img src="{$board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].image}" border="0" />{/if}
						{$board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].title}
					</a>
				{/section}
			</div>
			{/if}
		</div>
		<div style="clear:both"></div>
	{else}
		<div class="boardmessages-row-{$board.type} {cycle values="boardmessages-row-header-first, boardmessages-row-header-second"}">
			<div class="boardmessages-row-header-{$board.type}">
				{if $board.rows[brdmsgindex].sender.url neq ""}<a class="boardmessages-row-header-title" href="{$board.rows[brdmsgindex].sender.url}">{/if}
					{$board.rows[brdmsgindex].sender.name}
				{if $board.rows[brdmsgindex].sender.url neq ""}</a>{/if}
				<span class="boardmessages-row-header-datetime-{$board.type}">{$board.rows[brdmsgindex].time}</span>
				<a class="boardmessages-row-header-number-{$board.type}" href="{$special.page.url}#message-{$board.rows[brdmsgindex].showmumber}" id="message-{$board.rows[brdmsgindex].showmumber}">#{$board.rows[brdmsgindex].showmumber}</a>
			</div>
			<div class="boardmessages-row-message">
				{if $board.rows[brdmsgindex].title neq ""}<div class="boardmessages-row-title">{$board.rows[brdmsgindex].title}</div>{/if}
				{$board.rows[brdmsgindex].message}
				{if $board.rows[brdmsgindex].signature neq ""}<div class="boardmessages-row-signature">{$board.rows[brdmsgindex].signature}</div>{/if}
			</div>
			{if $board.rows[brdmsgindex].buttonscount gt 0}
			<div class="boardmessages-row-buttonbar">
				{section name=btnbrdmsgbtnsindex loop=$board.rows[brdmsgindex].buttons}
					{if $board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].messagebox eq 1}
					<a href="javascript:;" onclick="boardmessages_msgbox{$postfix}('{$board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].url}', '{$board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].message}')">
					{else}
					<a href="{$board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].url}">
					{/if}
						{if $board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].image neq ""}<img src="{$board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].image}" border="0" />{/if}
						{$board.rows[brdmsgindex].buttons[btnbrdmsgbtnsindex].title}
					</a>
				{/section}
			</div>
			{/if}
		</div>
	{/if}
	{/section}
</div>

<script type="text/javascript">
set_boardmessages_style{$postfix}();
</script>

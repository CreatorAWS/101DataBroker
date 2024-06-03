{assign var=m value=$modules[$index]}
<div class="flex align-items-start">

	<div class="row col-md-4">
		<div class="col-md-12 profilesidebar-inside-padding">
			<div class="img-user-contact">
				<div class="profilesidebars user-pro-left">
					<div>
						<span class="profile-round"><img src="themes/current/images/prospectheaderbg.png" /></span>
						{if $m.customer.photo neq ""}
							<a href="{$m.customer.photo_change_url}" class="customerimg"><img src="{$m.customer.photo}" /></a>
						{else}
							{* <div class="customerimg"><span>{$m.customer.initials}</span></div> *}
						{/if}
					</div>
					<div class="customer-info-name">
						{if $m.customer.first_name eq "" AND $m.customer.last_name eq "" AND $m.customer.company_name neq ""}
							<h2 class="customer-side-info-title customer-side-info-card">{$m.customer.company_name}</h2>
						{else}
							<h2 class="customer-side-info-title customer-side-info-card">{$m.customer.first_name} {$m.customer.last_name}</h2>
						{/if}
						{if $m.customer.additional_caption neq ""}<div class="customer-side-info customer-side-info-phone">{$m.customer.additional_caption}</div>{/if}

						<div class="flex align-items-top">
							<div class="customer-details-column">
								{if $m.customer.address neq ""}
									Address: <span>{$m.customer.address}</span>
								{/if}
							</div>
						</div>
					</div>
				</div>
				<div class="customer-details-column-description">
					<div class="row user-card-right">
						<div class="col-md-12">
							<div class="customer-info-section-label">Personal Info</div>
							<div class="flex flex-wrap">
								{if $m.customer.first_name neq ""}
									<div class="personal-info-item">
										<span>First Name</span>
										<div>{$m.customer.first_name}</div>
									</div>
								{/if}
								{if $m.customer.last_name neq ""}
									<div class="personal-info-item">
										<span>Last Name</span>
										<div>{$m.customer.last_name}</div>
									</div>
								{/if}
								{if $m.customer.company_name neq ""}
									<div class="personal-info-item">
										<span>Business Name</span>
										<div>{$m.customer.company_name}</div>
									</div>
								{/if}
								{if $m.customer.cellphone neq ""}
									<div class="personal-info-item">
										<span>Phone</span>
										<div class="customer-side-info customer-side-info-phone">{$m.customer.cellphone}</div>

										{if $m.customer.hasAdditionalPhones}
											<div class="other-emails">
												{section name=i loop=$m.customer.phones}
													<div class="flex align-items-normal">
														<span class="iconclass-left"><i class="fa-solid fa-phone"></i></span>
														{$m.customer.phones[i].phone}
														<div class="flex">
															<a href="{$m.customer.phones[i].primaryURL}" class="ml-5" title="Set as primary"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg></a>
															<a href="javascript:;" onclick="admintable_msgbox('Do you really want to delete this item', '{$m.customer.phones[i].deleteURL}')" class="ml-5"><svg width="18" height="20" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.82692 6L8.59615 12M5.40385 12L5.17308 6M11.8184 3.86038C12.0464 3.89481 12.2736 3.93165 12.5 3.97086M11.8184 3.86038L11.1065 13.115C11.0464 13.8965 10.3948 14.5 9.61095 14.5H4.38905C3.60524 14.5 2.95358 13.8965 2.89346 13.115L2.18157 3.86038M11.8184 3.86038C11.0542 3.74496 10.281 3.65657 9.5 3.59622M1.5 3.97086C1.72638 3.93165 1.95358 3.89481 2.18157 3.86038M2.18157 3.86038C2.94585 3.74496 3.719 3.65657 4.5 3.59622M9.5 3.59622V2.98546C9.5 2.19922 8.8929 1.54282 8.10706 1.51768C7.73948 1.50592 7.37043 1.5 7 1.5C6.62957 1.5 6.26052 1.50592 5.89294 1.51768C5.1071 1.54282 4.5 2.19922 4.5 2.98546V3.59622M9.5 3.59622C8.67504 3.53247 7.84131 3.5 7 3.5C6.15869 3.5 5.32496 3.53247 4.5 3.59622" stroke="#747B88" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path></svg></a>
														</div>
													</div>
												{/section}
											</div>
										{/if}
									</div>
								{/if}
								{if $m.customer.email neq ""}
									<div class="personal-info-item">
										<span>Email</span>
										<div class="customer-side-info customer-side-info-email flex align-items-baseline flex-column">
											<div class="flex main-email">
												{$m.customer.email}
											</div>
											{if $m.customer.hasAdditionalEmails}
												<div class="other-emails">
													{section name=i loop=$m.customer.emails}
														<div class="flex align-items-normal">
															<span class="iconclass-left"><i class="fa-regular fa-envelope"></i></span>
															{$m.customer.emails[i].email}
															<div class="flex">
																<a href="{$m.customer.emails[i].primaryURL}" class="ml-5" title="Set as primary"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg></a>
																<a href="javascript:;" onclick="admintable_msgbox('Do you really want to delete this item', '{$m.customer.emails[i].deleteURL}')" class="ml-5"><svg width="18" height="20" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.82692 6L8.59615 12M5.40385 12L5.17308 6M11.8184 3.86038C12.0464 3.89481 12.2736 3.93165 12.5 3.97086M11.8184 3.86038L11.1065 13.115C11.0464 13.8965 10.3948 14.5 9.61095 14.5H4.38905C3.60524 14.5 2.95358 13.8965 2.89346 13.115L2.18157 3.86038M11.8184 3.86038C11.0542 3.74496 10.281 3.65657 9.5 3.59622M1.5 3.97086C1.72638 3.93165 1.95358 3.89481 2.18157 3.86038M2.18157 3.86038C2.94585 3.74496 3.719 3.65657 4.5 3.59622M9.5 3.59622V2.98546C9.5 2.19922 8.8929 1.54282 8.10706 1.51768C7.73948 1.50592 7.37043 1.5 7 1.5C6.62957 1.5 6.26052 1.50592 5.89294 1.51768C5.1071 1.54282 4.5 2.19922 4.5 2.98546V3.59622M9.5 3.59622C8.67504 3.53247 7.84131 3.5 7 3.5C6.15869 3.5 5.32496 3.53247 4.5 3.59622" stroke="#747B88" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path></svg></a>
															</div>
														</div>
													{/section}
												</div>
											{/if}
										</div>
									</div>
								{/if}
								{section name= j loop = $m.customer.stafffields}
									{if $m.customer.stafffields[j].value neq " "}
										<div class="personal-info-item">
											<span>{$m.customer.stafffields[j].title}</span>
											<div>{$m.customer.stafffields[j].value}</div>
										</div>
									{/if}
								{/section}
								{section name= i loop = $m.customer.customfields}
									{if $m.customer.customfields[i].value neq ""}
										<div class="personal-info-item">
											<span>{$m.customer.customfields[j].title}</span>
											<div>{$m.customer.customfields[j].value}</div>
										</div>
									{/if}
								{/section}
							</div>

							{if $m.customer.tagscount gt 0}
								<div class="customer-info-section-label">Tags</div>
								<p class="tags-violet-text">
									{section name=i loop=$m.customer.tags}
										<span><a class="label label-info" href="{$m.customer.tags[i].url}">{$m.customer.tags[i].title}</a></span>
									{/section}
								</p>
							{/if}

							{if $m.customer.has_social_urls}
								<div class="customer-info-section-label">Social Media</div>
								<div class="customer-side-info social-account-list">
									{$m.customer.social_media}
								</div>
							{/if}
						</div>
					</div>
					{if $m.customer.note neq ""}
						<div class="row user-card-right">
							<div class="col-md-12">
								<div class="customer-info-section-label">Note</div>
								<p class="username-title">{$m.customer.note} </p>
							</div>
						</div>
					{/if}
				</div>
				<div class="user-pro-right">
					{* <div class="customer-edit-icon">
						<a href="{$m.customer.editurl}"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg></a>
					</div> *}
					{* {if $m.customer.tagscount gt 0}
					<div class="customer-side-info customer-side-info-tags">
						{section name=i loop=$m.customer.tags}
							<a class="label label-info" href="{$m.customer.tags[i].url}">{$m.customer.tags[i].title}</a>
						{/section}
					</div>{/if} *}

					{* <ul class="list-group">
						{if ($m.customer.first_name neq "" OR $m.customer.last_name neq "") AND $m.customer.company_name neq ""}
							<li class="list-group-item">Business Name:<span> {$m.customer.company_name}</span></li>
						{/if}
						{section name= j loop = $m.customer.stafffields}
							{if $m.customer.stafffields[j].value neq " "}<li class="list-group-item">{$m.customer.stafffields[j].title}: <span>{$m.customer.stafffields[j].value}</span></li>{/if}
						{/section}

						{section name= i loop = $m.customer.customfields}
							{if $m.customer.customfields[i].value neq ""}<li class="list-group-item">{$m.customer.customfields[i].title}: <span>{$m.customer.customfields[i].value}</span></li>{/if}
						{/section}

						{if $m.customer.address neq ""}<li class="list-group-item">Address: <span>{$m.customer.address}</span></li>{/if}
						{if $m.customer.note neq ""}
							<li class="list-group-item">Note <span>{$m.customer.note}</span></li>
						{/if}
					</ul> *}
					{section name=i loop=$m.customer.buttons}
						{if $m.customer.buttons[i].onclick neq ""}
							<a href="javascript:;" {if $m.customer.buttons[i].onclick neq ""}onclick="{$m.customer.buttons[i].onclick}"{/if} class="btn {$m.customer.buttons[i].class} btn-quirk btn-block profile-btn-follow">{$m.customer.buttons[i].title}</a>
						{else}
							<a href="{$m.customer.buttons[i].url}" class="btn {$m.customer.buttons[i].class} btn-quirk btn-block profile-btn-follow">{$m.customer.buttons[i].title}</a>
						{/if}
					{/section}
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-9">
		<div class="col-md-12 profilesidebarright user-tabs-ext" >

			<ul class="nav nav-tabs nav-user-border">
				<li role="newmsg"{if $m.mode eq "newmsg"} class="active"{/if}><a href="{$m.tabs.sms}">Send SMS</a></li>
				<li role="newemail"{if $m.mode eq "newemail"} class="active"{/if}><a href="{$m.tabs.email}">Send Email</a></li>
				<li role="appointments"{if $m.mode eq "appointments"} class="active"{/if}><a href="{$m.tabs.appointments}">Appointments</a></li>
				<li role="campaigns"{if $m.mode eq "campaigns"} class="active"{/if}><a href="{$m.tabs.campaigns}">Sequences</a></li>
				<li role="conversation"{if $m.mode eq "conversation"} class="active"{/if}><a href="{$m.tabs.conversation}">Conversation</a></li>
				<li role="allmessages"{if $m.mode eq "allmessages"} class="active"{/if}><a href="{$m.tabs.messages}">All Messages</a></li>
				<li role="marketingmessages"{if $m.mode eq "marketingmessages"} class="active"{/if}><a href="{$m.tabs.marketingmessages}">Marketing Messages</a></li>
				<li role="tags"{if $m.mode eq "tags"} class="active"{/if}><a href="{$m.tabs.tags}">Tags</a></li>
				<li role="notes"{if $m.mode eq "notes"} class="active"{/if}><a href="{$m.tabs.notes}">Notes</a></li>
				<li role="call"{if $m.mode eq "call"} class="active"{/if}><a href="{$m.tabs.calls}" title="Call" data-toggle="tooltip" data-placement="bottom">Calls</a></li>
			</ul>
		</div>


		<div class="row main-block-section">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-12">
						{* {include file="customerdetails_mobile.tpl"} *}

						{include file="customerdetails_mobile.tpl"}
						{* <p class="tags-violet-text"><span>Certified</span><span>Lease</span><span>Sonata</span><span>Affiliate</span><span>Azera</span><span>Dee</span><span>DNC</span><span>Elantra</span><span>Email</span><span>Equus</span><span>Genesis</span><span>Network</span><span>Genesis</span></p> *}
					</div>
				</div>
			</div>
		</div>

	</div>

</div>


<div class="col-md-12" style="padding-left: 0px;">
	{* profile *}
	{* <div class="row profilesidebar-inside-padding">
		<div class="col-md-4">
			<div class="img-user-contact flex-left-border">
				<div class="profilesidebars user-pro-left">
					<div>
						<span class="profile-round"><img src="themes/current/images/prospectheaderbg.png" /></span>
						{if $m.customer.photo neq ""}
							<a href="{$m.customer.photo_change_url}" class="customerimg"><img src="{$m.customer.photo}" /></a>
						{/if}
					</div>
					<div class="customer-info-name">
						{if $m.customer.first_name eq "" AND $m.customer.last_name eq "" AND $m.customer.company_name neq ""}
							<h2 class="customer-side-info-title customer-side-info-card">{$m.customer.company_name}</h2>
						{else}
							<h2 class="customer-side-info-title customer-side-info-card">{$m.customer.first_name} {$m.customer.last_name}</h2>
						{/if}
						{if $m.customer.additional_caption neq ""}<div class="customer-side-info customer-side-info-phone">{$m.customer.additional_caption}</div>{/if}
						{if $m.customer.cellphone neq ""}
							<div class="customer-side-info customer-side-info-phone"><span class="iconclass-left"><i class="fa-solid fa-phone"></i></span>{$m.customer.cellphone}</div>
						{/if}
						{if $m.customer.email neq ""}
							<div class="customer-side-info customer-side-info-email">
								<span class="iconclass-left"><i class="fa-regular fa-envelope"></i></span>
								{$m.customer.email}
							</div>
						{/if}
						<div class="user-pro-right user-profile-card">	
							{if $m.customer.has_social_urls}
							<div class="customer-side-info" style="padding-top:10px;">
								{$m.customer.social_media}
							</div>
							{/if}
							{section name=i loop=$m.customer.buttons}
								{if $m.customer.buttons[i].onclick neq ""}
									<a href="javascript:;" {if $m.customer.buttons[i].onclick neq ""}onclick="{$m.customer.buttons[i].onclick}"{/if} class="btn {$m.customer.buttons[i].class} btn-quirk btn-block profile-btn-follow">{$m.customer.buttons[i].title}</a>
								{else}
									<a href="{$m.customer.buttons[i].url}" class="btn {$m.customer.buttons[i].class} btn-quirk btn-block profile-btn-follow">{$m.customer.buttons[i].title}</a>
								{/if}
							{/section}
						</div>
					</div>					
				</div>				
			</div>
		</div>
		<div class="col-md-8 flex-right-details">
			<div class="row user-card-right">				
				<div class="col-md-4">
				{if ($m.customer.first_name neq "" OR $m.customer.last_name neq "") AND $m.customer.company_name neq ""}
					<p class="user-name-card">Business Name</p>
					<p class="username-title">{$m.customer.company_name}Business </p>
				{/if}
				{section name= j loop = $m.customer.stafffields}
					{if $m.customer.stafffields[j].value neq " "}<li class="list-group-item">{$m.customer.stafffields[j].title}: <span>{$m.customer.stafffields[j].value}</span></li>{/if}
				{/section}

				{section name= i loop = $m.customer.customfields}
					{if $m.customer.customfields[i].value neq ""}<li class="list-group-item">{$m.customer.customfields[i].title}: <span>{$m.customer.customfields[i].value}</span></li>{/if}
				{/section}
				</div>
				<div class="col-md-4">
				{if $m.customer.address neq ""}
					<p class="user-name-card">Support Team</p>
					<p class="username-title">{$m.customer.address}Team </p>
				{/if}
				</div>
				<div class="col-md-4">
				{if $m.customer.address neq ""}
					<p class="user-name-card">Seller Support</p>
					<p class="username-title">{$m.customer.address} Support</p>
				{/if}
				</div>				
			</div>
			<div class="row user-card-right">
				<div class="col-md-12">
				{if $m.customer.note neq ""}
					<p class="user-name-card">Note</p>
					<p class="username-title">{$m.customer.note}note </p>
				{/if}
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					{include file="customerdetails_mobile.tpl"}
					<p class="user-name-card">Tags</p>
					{if $m.customer.tagscount gt 0}
					<p class="tags-violet-text">
						{section name=i loop=$m.customer.tags}
							<span><a class="label label-info" href="{$m.customer.tags[i].url}">{$m.customer.tags[i].title}</a></span>
						{/section}
					</p>{/if}
				</div>
			</div>
		</div>
	</div> *}
	{* profile *}

</div>

<script>
function  admintable_msgbox(question, url)
	{ldelim}
		if (confirm(question+(question.indexOf('?', 0)>=0?'':'?')))
		{ldelim}
			setTimeout(function() {ldelim} document.location.href = url; {rdelim}, 30);
		{rdelim}
	{rdelim}
</script>
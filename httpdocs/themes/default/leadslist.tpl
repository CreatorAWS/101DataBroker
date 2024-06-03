<div class="dragable-header">
    <div class='wrapper'>
        <div class="headerstep">Received</div>
        <div class="headerstep">Contact</div>
        <div class="headerstep">Appointment</div>
        <div class="headerstep">Sold</div>
        <div class="headerstep">Lost</div>
    </div>
</div>
<div class="dragable-container">
    <div class='wrapper'>
        <div id="received" class="leadcontainer">
            {section name = i loop = $data.customer.received}
                <div class="card" id="{$data.customer.received[i].id}">
                    <div class="locationwrap">
                        {if $data.customer.received[i].unreaded neq ""}
                            <div class="location label label-danger">{$data.customer.received[i].unreaded}</div>
                        {/if}
                    </div>
                    <div class="name"> 
                        <a href="{$data.customer.received[i].view_url}" >{$data.customer.received[i].name}</a>
                        <div class="dropdown dropdown-userlist">
                            <button class="btn btn-secondary dropdown-toggle btn-focus-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-compaign">
                                <a class="dropdown-item" href="{$data.customer.received[i].editurl}"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M8.48533 4.45739L7.54267 3.51472L1.33333 9.72406V10.6667H2.276L8.48533 4.45739ZM9.428 3.51472L10.3707 2.57206L9.428 1.62939L8.48533 2.57206L9.428 3.51472ZM2.828 12.0001H0V9.17139L8.95667 0.214722C9.08169 0.0897416 9.25122 0.0195312 9.428 0.0195312C9.60478 0.0195312 9.77432 0.0897416 9.89933 0.214722L11.7853 2.10072C11.9103 2.22574 11.9805 2.39528 11.9805 2.57206C11.9805 2.74883 11.9103 2.91837 11.7853 3.04339L2.82867 12.0001H2.828Z" fill="#9CA3AF"/>
                                    </svg> Edit</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="admintable_msgbox('Do you really want to delete this item', '{$data.customer.received[i].deleteurl}')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M10.3335 2.99967H13.6668V4.33301H12.3335V12.9997C12.3335 13.1765 12.2633 13.3461 12.1382 13.4711C12.0132 13.5961 11.8436 13.6663 11.6668 13.6663H2.3335C2.15668 13.6663 1.98712 13.5961 1.86209 13.4711C1.73707 13.3461 1.66683 13.1765 1.66683 12.9997V4.33301H0.333496V2.99967H3.66683V0.999674C3.66683 0.822863 3.73707 0.653294 3.86209 0.52827C3.98712 0.403246 4.15668 0.333008 4.3335 0.333008H9.66683C9.84364 0.333008 10.0132 0.403246 10.1382 0.52827C10.2633 0.653294 10.3335 0.822863 10.3335 0.999674V2.99967ZM11.0002 4.33301H3.00016V12.333H11.0002V4.33301ZM5.00016 6.33301H6.3335V10.333H5.00016V6.33301ZM7.66683 6.33301H9.00016V10.333H7.66683V6.33301ZM5.00016 1.66634V2.99967H9.00016V1.66634H5.00016Z" fill="#9CA3AF"/>
                                    </svg> Delete</a>
                            </div>
                        </div>
                    </div>
                    {if $data.customer.received[i].cellphone neq ""}
                        <div class="cellphone"><label><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M4.244 5.12133C4.86956 6.22032 5.77968 7.13044 6.87867 7.756L7.468 6.93067C7.56277 6.79796 7.7029 6.70459 7.86187 6.66822C8.02084 6.63186 8.18762 6.65502 8.33067 6.73333C9.27355 7.24862 10.3148 7.55852 11.386 7.64267C11.5532 7.65592 11.7092 7.73169 11.823 7.85488C11.9368 7.97807 12 8.13963 12 8.30733V11.282C12 11.4471 11.9388 11.6063 11.8282 11.7288C11.7177 11.8513 11.5655 11.9285 11.4013 11.9453C11.048 11.982 10.692 12 10.3333 12C4.62667 12 0 7.37333 0 1.66667C0 1.308 0.018 0.952 0.0546667 0.598667C0.0715031 0.434465 0.148656 0.282347 0.271193 0.171756C0.39373 0.0611648 0.552937 -3.55718e-05 0.718 1.55115e-08H3.69267C3.86037 -2.10123e-05 4.02193 0.0631677 4.14512 0.176967C4.26831 0.290767 4.34408 0.446816 4.35733 0.614C4.44148 1.68519 4.75138 2.72645 5.26667 3.66933C5.34498 3.81238 5.36814 3.97916 5.33178 4.13813C5.29541 4.2971 5.20204 4.43723 5.06933 4.532L4.244 5.12133ZM2.56267 4.68333L3.82933 3.77867C3.46986 3.00273 3.22357 2.17923 3.098 1.33333H1.34C1.336 1.444 1.334 1.55533 1.334 1.66667C1.33333 6.63733 5.36267 10.6667 10.3333 10.6667C10.4447 10.6667 10.556 10.6647 10.6667 10.66V8.902C9.82077 8.77643 8.99727 8.53014 8.22133 8.17067L7.31667 9.43733C6.95244 9.29581 6.59867 9.12873 6.258 8.93733L6.21933 8.91533C4.91172 8.17115 3.82885 7.08828 3.08467 5.78067L3.06267 5.742C2.87127 5.40133 2.70419 5.04756 2.56267 4.68333Z" fill="#949BA2"/>
                                </svg></label> {$data.customer.received[i].cellphone}</div>
                    {/if}
                    {if $data.customer.received[i].email neq ""}
                        <div class="cellphone"><label><svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12" fill="none">
                                    <path d="M1.00016 0H13.0002C13.177 0 13.3465 0.0702379 13.4716 0.195262C13.5966 0.320286 13.6668 0.489856 13.6668 0.666667V11.3333C13.6668 11.5101 13.5966 11.6797 13.4716 11.8047C13.3465 11.9298 13.177 12 13.0002 12H1.00016C0.823352 12 0.653782 11.9298 0.528758 11.8047C0.403734 11.6797 0.333496 11.5101 0.333496 11.3333V0.666667C0.333496 0.489856 0.403734 0.320286 0.528758 0.195262C0.653782 0.0702379 0.823352 0 1.00016 0ZM12.3335 2.82533L7.04816 7.55867L1.66683 2.81067V10.6667H12.3335V2.82533ZM2.0075 1.33333L7.04083 5.77467L12.0015 1.33333H2.0075Z" fill="#949BA2"/>
                                </svg> {$data.customer.received[i].email}</div>
                        {if $data.customer.received[i].emailstatus eq 1}
                            <div class="location label label-success">Verified Email</div>
                        {elseif $data.customer.received[i].emailstatus eq 2}
                            <div class="location label label-danger">Not Valid Email</div>
                        {/if}
                    {/if}
                    {if $data.customer.received[i].note neq ""}
                        <div class="note tooltips"><label><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <path d="M11.6668 13.6663H2.3335C1.80306 13.6663 1.29436 13.4556 0.919283 13.0806C0.54421 12.7055 0.333496 12.1968 0.333496 11.6663V0.999674C0.333496 0.822863 0.403734 0.653294 0.528758 0.52827C0.653782 0.403246 0.823352 0.333008 1.00016 0.333008H10.3335C10.5103 0.333008 10.6799 0.403246 10.8049 0.52827C10.9299 0.653294 11.0002 0.822863 11.0002 0.999674V8.99967H13.6668V11.6663C13.6668 12.1968 13.4561 12.7055 13.081 13.0806C12.706 13.4556 12.1973 13.6663 11.6668 13.6663ZM11.0002 10.333V11.6663C11.0002 11.8432 11.0704 12.0127 11.1954 12.1377C11.3204 12.2628 11.49 12.333 11.6668 12.333C11.8436 12.333 12.0132 12.2628 12.1382 12.1377C12.2633 12.0127 12.3335 11.8432 12.3335 11.6663V10.333H11.0002ZM9.66683 12.333V1.66634H1.66683V11.6663C1.66683 11.8432 1.73707 12.0127 1.86209 12.1377C1.98712 12.2628 2.15668 12.333 2.3335 12.333H9.66683ZM3.00016 3.66634H8.3335V4.99967H3.00016V3.66634ZM3.00016 6.33301H8.3335V7.66634H3.00016V6.33301ZM3.00016 8.99967H6.3335V10.333H3.00016V8.99967Z" fill="#949BA2"/>
                                </svg></label> <span class="tooltiptext">{$data.customer.received[i].note}</span></div>
                    {/if}
                    {if $data.customer.received[i].tags_count gt 0}
                        <hr class="hr-card-user">
                    {/if}
                    <div class="locationwrap">
                        {section name = j loop = $data.customer.received[i].tags}
                            <a class="location label {if $data.customer.received[i].tags[j].class eq ""}label-info label-info-color{else}{$data.customer.received[i].tags[j].class}{/if}" href="{$data.customer.received[i].tags[j].url}">{$data.customer.received[i].tags[j].title}</a>
                        {/section}
                    </div>
                </div>
            {/section}
            {if $data.received.showlostleadsbutton neq ""}
                <div class="showmoreleads">
                    <a href="index.php?m=customers&d=listview&status=received" class="add_asset_button ab-button">{$data.received.showlostleadsbutton}</a>
                </div>
            {/if}
        </div>
        <div id="contact" class="leadcontainer">
            {section name = i loop = $data.customer.contact}
                <div class="card" id="{$data.customer.contact[i].id}">
                    <div class="locationwrap">
                        {if $data.customer.contact[i].unreaded neq ""}
                            <div class="location label label-danger">{$data.customer.contact[i].unreaded}</div>
                        {/if}
                    </div>
                    <div class="name"> 
                        <a href="{$data.customer.contact[i].view_url}" >{$data.customer.contact[i].name}</a>
                        <div class="dropdown dropdown-userlist">
                            <button class="btn btn-secondary dropdown-toggle btn-focus-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-compaign">
                                <a class="dropdown-item" href="{$data.customer.contact[i].editurl}"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M8.48533 4.45739L7.54267 3.51472L1.33333 9.72406V10.6667H2.276L8.48533 4.45739ZM9.428 3.51472L10.3707 2.57206L9.428 1.62939L8.48533 2.57206L9.428 3.51472ZM2.828 12.0001H0V9.17139L8.95667 0.214722C9.08169 0.0897416 9.25122 0.0195312 9.428 0.0195312C9.60478 0.0195312 9.77432 0.0897416 9.89933 0.214722L11.7853 2.10072C11.9103 2.22574 11.9805 2.39528 11.9805 2.57206C11.9805 2.74883 11.9103 2.91837 11.7853 3.04339L2.82867 12.0001H2.828Z" fill="#9CA3AF"/>
                                    </svg> Edit</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="admintable_msgbox('Do you really want to delete this item', '{$data.customer.contact[i].deleteurl}')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M10.3335 2.99967H13.6668V4.33301H12.3335V12.9997C12.3335 13.1765 12.2633 13.3461 12.1382 13.4711C12.0132 13.5961 11.8436 13.6663 11.6668 13.6663H2.3335C2.15668 13.6663 1.98712 13.5961 1.86209 13.4711C1.73707 13.3461 1.66683 13.1765 1.66683 12.9997V4.33301H0.333496V2.99967H3.66683V0.999674C3.66683 0.822863 3.73707 0.653294 3.86209 0.52827C3.98712 0.403246 4.15668 0.333008 4.3335 0.333008H9.66683C9.84364 0.333008 10.0132 0.403246 10.1382 0.52827C10.2633 0.653294 10.3335 0.822863 10.3335 0.999674V2.99967ZM11.0002 4.33301H3.00016V12.333H11.0002V4.33301ZM5.00016 6.33301H6.3335V10.333H5.00016V6.33301ZM7.66683 6.33301H9.00016V10.333H7.66683V6.33301ZM5.00016 1.66634V2.99967H9.00016V1.66634H5.00016Z" fill="#9CA3AF"/>
                                    </svg> Delete</a>
                            </div>
                        </div>
                    </div>
                    {if $data.customer.contact[i].cellphone neq ""}
                        <div class="cellphone"><label><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M4.244 5.12133C4.86956 6.22032 5.77968 7.13044 6.87867 7.756L7.468 6.93067C7.56277 6.79796 7.7029 6.70459 7.86187 6.66822C8.02084 6.63186 8.18762 6.65502 8.33067 6.73333C9.27355 7.24862 10.3148 7.55852 11.386 7.64267C11.5532 7.65592 11.7092 7.73169 11.823 7.85488C11.9368 7.97807 12 8.13963 12 8.30733V11.282C12 11.4471 11.9388 11.6063 11.8282 11.7288C11.7177 11.8513 11.5655 11.9285 11.4013 11.9453C11.048 11.982 10.692 12 10.3333 12C4.62667 12 0 7.37333 0 1.66667C0 1.308 0.018 0.952 0.0546667 0.598667C0.0715031 0.434465 0.148656 0.282347 0.271193 0.171756C0.39373 0.0611648 0.552937 -3.55718e-05 0.718 1.55115e-08H3.69267C3.86037 -2.10123e-05 4.02193 0.0631677 4.14512 0.176967C4.26831 0.290767 4.34408 0.446816 4.35733 0.614C4.44148 1.68519 4.75138 2.72645 5.26667 3.66933C5.34498 3.81238 5.36814 3.97916 5.33178 4.13813C5.29541 4.2971 5.20204 4.43723 5.06933 4.532L4.244 5.12133ZM2.56267 4.68333L3.82933 3.77867C3.46986 3.00273 3.22357 2.17923 3.098 1.33333H1.34C1.336 1.444 1.334 1.55533 1.334 1.66667C1.33333 6.63733 5.36267 10.6667 10.3333 10.6667C10.4447 10.6667 10.556 10.6647 10.6667 10.66V8.902C9.82077 8.77643 8.99727 8.53014 8.22133 8.17067L7.31667 9.43733C6.95244 9.29581 6.59867 9.12873 6.258 8.93733L6.21933 8.91533C4.91172 8.17115 3.82885 7.08828 3.08467 5.78067L3.06267 5.742C2.87127 5.40133 2.70419 5.04756 2.56267 4.68333Z" fill="#949BA2"/>
                                </svg></label> {$data.customer.contact[i].cellphone}</div>
                    {/if}
                    {if $data.customer.contact[i].email neq ""}
                        <div class="cellphone"><label><svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12" fill="none">
                                    <path d="M1.00016 0H13.0002C13.177 0 13.3465 0.0702379 13.4716 0.195262C13.5966 0.320286 13.6668 0.489856 13.6668 0.666667V11.3333C13.6668 11.5101 13.5966 11.6797 13.4716 11.8047C13.3465 11.9298 13.177 12 13.0002 12H1.00016C0.823352 12 0.653782 11.9298 0.528758 11.8047C0.403734 11.6797 0.333496 11.5101 0.333496 11.3333V0.666667C0.333496 0.489856 0.403734 0.320286 0.528758 0.195262C0.653782 0.0702379 0.823352 0 1.00016 0ZM12.3335 2.82533L7.04816 7.55867L1.66683 2.81067V10.6667H12.3335V2.82533ZM2.0075 1.33333L7.04083 5.77467L12.0015 1.33333H2.0075Z" fill="#949BA2"/>
                                </svg> {$data.customer.contact[i].email}</div>
                        {if $data.customer.contact[i].emailstatus eq 1}
                            <div class="location label label-success">Verified Email</div>
                        {elseif $data.customer.contact[i].emailstatus eq 2}
                            <div class="location label label-danger">Not Valid Email</div>
                        {/if}
                    {/if}
                    {if $data.customer.contact[i].note neq ""}
                        <div class="note"><label><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <path d="M11.6668 13.6663H2.3335C1.80306 13.6663 1.29436 13.4556 0.919283 13.0806C0.54421 12.7055 0.333496 12.1968 0.333496 11.6663V0.999674C0.333496 0.822863 0.403734 0.653294 0.528758 0.52827C0.653782 0.403246 0.823352 0.333008 1.00016 0.333008H10.3335C10.5103 0.333008 10.6799 0.403246 10.8049 0.52827C10.9299 0.653294 11.0002 0.822863 11.0002 0.999674V8.99967H13.6668V11.6663C13.6668 12.1968 13.4561 12.7055 13.081 13.0806C12.706 13.4556 12.1973 13.6663 11.6668 13.6663ZM11.0002 10.333V11.6663C11.0002 11.8432 11.0704 12.0127 11.1954 12.1377C11.3204 12.2628 11.49 12.333 11.6668 12.333C11.8436 12.333 12.0132 12.2628 12.1382 12.1377C12.2633 12.0127 12.3335 11.8432 12.3335 11.6663V10.333H11.0002ZM9.66683 12.333V1.66634H1.66683V11.6663C1.66683 11.8432 1.73707 12.0127 1.86209 12.1377C1.98712 12.2628 2.15668 12.333 2.3335 12.333H9.66683ZM3.00016 3.66634H8.3335V4.99967H3.00016V3.66634ZM3.00016 6.33301H8.3335V7.66634H3.00016V6.33301ZM3.00016 8.99967H6.3335V10.333H3.00016V8.99967Z" fill="#949BA2"/>
                                </svg></label> {$data.customer.contact[i].note}</div>
                    {/if}
                    {if $data.customer.contact[i].tags_count gt 0}
                        <hr class="hr-card-user">
                    {/if}
                    <div class="locationwrap">
                        {section name = j loop = $data.customer.contact[i].tags}
                            <a class="location label {if $data.customer.contact[i].tags[j].class eq ""}label-info label-info-color{else}{$data.customer.contact[i].tags[j].class}{/if}" href="{$data.customer.contact[i].tags[j].url}">{$data.customer.contact[i].tags[j].title}</a>
                        {/section}
                    </div>
                </div>
            {/section}
            {if $data.contact.showlostleadsbutton neq ""}
                <div class="showmoreleads">
                    <a href="index.php?m=customers&d=listview&status=contact" class="add_asset_button ab-button">{$data.contact.showlostleadsbutton}</a>
                </div>
            {/if}
        </div>
        <div id="appointment" class="leadcontainer">
            {section name = i loop = $data.customer.appointment}
                <div class="card" id="{$data.customer.appointment[i].id}">
                    <div class="locationwrap">
                        {if $data.customer.appointment[i].unreaded neq ""}
                            <div class="location label label-danger">{$data.customer.appointment[i].unreaded}</div>
                        {/if}
                    </div>
                    <div class="name"> 
                        <a href="{$data.customer.appointment[i].view_url}" >{$data.customer.appointment[i].name}</a>
                        <div class="dropdown dropdown-userlist">
                            <button class="btn btn-secondary dropdown-toggle btn-focus-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-compaign">
                                <a class="dropdown-item" href="{$data.customer.appointment[i].editurl}"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M8.48533 4.45739L7.54267 3.51472L1.33333 9.72406V10.6667H2.276L8.48533 4.45739ZM9.428 3.51472L10.3707 2.57206L9.428 1.62939L8.48533 2.57206L9.428 3.51472ZM2.828 12.0001H0V9.17139L8.95667 0.214722C9.08169 0.0897416 9.25122 0.0195312 9.428 0.0195312C9.60478 0.0195312 9.77432 0.0897416 9.89933 0.214722L11.7853 2.10072C11.9103 2.22574 11.9805 2.39528 11.9805 2.57206C11.9805 2.74883 11.9103 2.91837 11.7853 3.04339L2.82867 12.0001H2.828Z" fill="#9CA3AF"/>
                                    </svg> Edit</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="admintable_msgbox('Do you really want to delete this item', '{$data.customer.appointment[i].deleteurl}')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M10.3335 2.99967H13.6668V4.33301H12.3335V12.9997C12.3335 13.1765 12.2633 13.3461 12.1382 13.4711C12.0132 13.5961 11.8436 13.6663 11.6668 13.6663H2.3335C2.15668 13.6663 1.98712 13.5961 1.86209 13.4711C1.73707 13.3461 1.66683 13.1765 1.66683 12.9997V4.33301H0.333496V2.99967H3.66683V0.999674C3.66683 0.822863 3.73707 0.653294 3.86209 0.52827C3.98712 0.403246 4.15668 0.333008 4.3335 0.333008H9.66683C9.84364 0.333008 10.0132 0.403246 10.1382 0.52827C10.2633 0.653294 10.3335 0.822863 10.3335 0.999674V2.99967ZM11.0002 4.33301H3.00016V12.333H11.0002V4.33301ZM5.00016 6.33301H6.3335V10.333H5.00016V6.33301ZM7.66683 6.33301H9.00016V10.333H7.66683V6.33301ZM5.00016 1.66634V2.99967H9.00016V1.66634H5.00016Z" fill="#9CA3AF"/>
                                    </svg> Delete</a>
                            </div>
                        </div>
                    </div>
                    {if $data.customer.appointment[i].cellphone neq ""}
                        <div class="cellphone"><label><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M4.244 5.12133C4.86956 6.22032 5.77968 7.13044 6.87867 7.756L7.468 6.93067C7.56277 6.79796 7.7029 6.70459 7.86187 6.66822C8.02084 6.63186 8.18762 6.65502 8.33067 6.73333C9.27355 7.24862 10.3148 7.55852 11.386 7.64267C11.5532 7.65592 11.7092 7.73169 11.823 7.85488C11.9368 7.97807 12 8.13963 12 8.30733V11.282C12 11.4471 11.9388 11.6063 11.8282 11.7288C11.7177 11.8513 11.5655 11.9285 11.4013 11.9453C11.048 11.982 10.692 12 10.3333 12C4.62667 12 0 7.37333 0 1.66667C0 1.308 0.018 0.952 0.0546667 0.598667C0.0715031 0.434465 0.148656 0.282347 0.271193 0.171756C0.39373 0.0611648 0.552937 -3.55718e-05 0.718 1.55115e-08H3.69267C3.86037 -2.10123e-05 4.02193 0.0631677 4.14512 0.176967C4.26831 0.290767 4.34408 0.446816 4.35733 0.614C4.44148 1.68519 4.75138 2.72645 5.26667 3.66933C5.34498 3.81238 5.36814 3.97916 5.33178 4.13813C5.29541 4.2971 5.20204 4.43723 5.06933 4.532L4.244 5.12133ZM2.56267 4.68333L3.82933 3.77867C3.46986 3.00273 3.22357 2.17923 3.098 1.33333H1.34C1.336 1.444 1.334 1.55533 1.334 1.66667C1.33333 6.63733 5.36267 10.6667 10.3333 10.6667C10.4447 10.6667 10.556 10.6647 10.6667 10.66V8.902C9.82077 8.77643 8.99727 8.53014 8.22133 8.17067L7.31667 9.43733C6.95244 9.29581 6.59867 9.12873 6.258 8.93733L6.21933 8.91533C4.91172 8.17115 3.82885 7.08828 3.08467 5.78067L3.06267 5.742C2.87127 5.40133 2.70419 5.04756 2.56267 4.68333Z" fill="#949BA2"/>
                                </svg></label> {$data.customer.appointment[i].cellphone}</div>
                    {/if}
                    {if $data.customer.appointment[i].email neq ""}
                        <div class="cellphone"><label><svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12" fill="none">
                                    <path d="M1.00016 0H13.0002C13.177 0 13.3465 0.0702379 13.4716 0.195262C13.5966 0.320286 13.6668 0.489856 13.6668 0.666667V11.3333C13.6668 11.5101 13.5966 11.6797 13.4716 11.8047C13.3465 11.9298 13.177 12 13.0002 12H1.00016C0.823352 12 0.653782 11.9298 0.528758 11.8047C0.403734 11.6797 0.333496 11.5101 0.333496 11.3333V0.666667C0.333496 0.489856 0.403734 0.320286 0.528758 0.195262C0.653782 0.0702379 0.823352 0 1.00016 0ZM12.3335 2.82533L7.04816 7.55867L1.66683 2.81067V10.6667H12.3335V2.82533ZM2.0075 1.33333L7.04083 5.77467L12.0015 1.33333H2.0075Z" fill="#949BA2"/>
                                </svg> {$data.customer.appointment[i].email}</div>
                        {if $data.customer.appointment[i].emailstatus eq 1}
                            <div class="location label label-success">Verified Email</div>
                        {elseif $data.customer.appointment[i].emailstatus eq 2}
                            <div class="location label label-danger">Not Valid Email</div>
                        {/if}
                    {/if}
                    {if $data.customer.appointment[i].note neq ""}
                        <div class="note"><label><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <path d="M11.6668 13.6663H2.3335C1.80306 13.6663 1.29436 13.4556 0.919283 13.0806C0.54421 12.7055 0.333496 12.1968 0.333496 11.6663V0.999674C0.333496 0.822863 0.403734 0.653294 0.528758 0.52827C0.653782 0.403246 0.823352 0.333008 1.00016 0.333008H10.3335C10.5103 0.333008 10.6799 0.403246 10.8049 0.52827C10.9299 0.653294 11.0002 0.822863 11.0002 0.999674V8.99967H13.6668V11.6663C13.6668 12.1968 13.4561 12.7055 13.081 13.0806C12.706 13.4556 12.1973 13.6663 11.6668 13.6663ZM11.0002 10.333V11.6663C11.0002 11.8432 11.0704 12.0127 11.1954 12.1377C11.3204 12.2628 11.49 12.333 11.6668 12.333C11.8436 12.333 12.0132 12.2628 12.1382 12.1377C12.2633 12.0127 12.3335 11.8432 12.3335 11.6663V10.333H11.0002ZM9.66683 12.333V1.66634H1.66683V11.6663C1.66683 11.8432 1.73707 12.0127 1.86209 12.1377C1.98712 12.2628 2.15668 12.333 2.3335 12.333H9.66683ZM3.00016 3.66634H8.3335V4.99967H3.00016V3.66634ZM3.00016 6.33301H8.3335V7.66634H3.00016V6.33301ZM3.00016 8.99967H6.3335V10.333H3.00016V8.99967Z" fill="#949BA2"/>
                                </svg></label> {$data.customer.appointment[i].note}</div>
                    {/if}
                    {if $data.customer.appointment[i].tags_count gt 0}
                        <hr class="hr-card-user">
                    {/if}
                    <div class="locationwrap">
                        {section name = j loop = $data.customer.appointment[i].tags}
                            <a class="location label {if $data.customer.appointment[i].tags[j].class eq ""}label-info label-info-color{else}{$data.customer.appointment[i].tags[j].class}{/if}" href="{$data.customer.appointment[i].tags[j].url}">{$data.customer.appointment[i].tags[j].title}</a>
                        {/section}
                    </div>
                </div>
            {/section}
            {if $data.appointment.showlostleadsbutton neq ""}
                <div class="showmoreleads">
                    <a href="index.php?m=customers&d=listview&status=appointment" class="add_asset_button ab-button">{$data.appointment.showlostleadsbutton}</a>
                </div>
            {/if}
        </div>
        <div id="sold" class="leadcontainer">
            {section name = i loop = $data.customer.sold}
                <div class="card" id="{$data.customer.sold[i].id}">
                    <div class="locationwrap">
                        {if $data.customer.sold[i].unreaded neq ""}
                            <div class="location label label-danger">{$data.customer.sold[i].unreaded}</div>
                        {/if}
                    </div>
                    <div class="name"> 
                        <a href="{$data.customer.sold[i].view_url}" >{$data.customer.sold[i].name}</a>
                        <div class="dropdown dropdown-userlist">
                            <button class="btn btn-secondary dropdown-toggle btn-focus-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-compaign">
                                <a class="dropdown-item" href="{$data.customer.sold[i].editurl}"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M8.48533 4.45739L7.54267 3.51472L1.33333 9.72406V10.6667H2.276L8.48533 4.45739ZM9.428 3.51472L10.3707 2.57206L9.428 1.62939L8.48533 2.57206L9.428 3.51472ZM2.828 12.0001H0V9.17139L8.95667 0.214722C9.08169 0.0897416 9.25122 0.0195312 9.428 0.0195312C9.60478 0.0195312 9.77432 0.0897416 9.89933 0.214722L11.7853 2.10072C11.9103 2.22574 11.9805 2.39528 11.9805 2.57206C11.9805 2.74883 11.9103 2.91837 11.7853 3.04339L2.82867 12.0001H2.828Z" fill="#9CA3AF"/>
                                    </svg> Edit</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="admintable_msgbox('Do you really want to delete this item', '{$data.customer.sold[i].deleteurl}')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M10.3335 2.99967H13.6668V4.33301H12.3335V12.9997C12.3335 13.1765 12.2633 13.3461 12.1382 13.4711C12.0132 13.5961 11.8436 13.6663 11.6668 13.6663H2.3335C2.15668 13.6663 1.98712 13.5961 1.86209 13.4711C1.73707 13.3461 1.66683 13.1765 1.66683 12.9997V4.33301H0.333496V2.99967H3.66683V0.999674C3.66683 0.822863 3.73707 0.653294 3.86209 0.52827C3.98712 0.403246 4.15668 0.333008 4.3335 0.333008H9.66683C9.84364 0.333008 10.0132 0.403246 10.1382 0.52827C10.2633 0.653294 10.3335 0.822863 10.3335 0.999674V2.99967ZM11.0002 4.33301H3.00016V12.333H11.0002V4.33301ZM5.00016 6.33301H6.3335V10.333H5.00016V6.33301ZM7.66683 6.33301H9.00016V10.333H7.66683V6.33301ZM5.00016 1.66634V2.99967H9.00016V1.66634H5.00016Z" fill="#9CA3AF"/>
                                    </svg> Delete</a>
                            </div>
                        </div>
                    </div>
                    {if $data.customer.sold[i].cellphone neq ""}
                        <div class="cellphone"><label><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M4.244 5.12133C4.86956 6.22032 5.77968 7.13044 6.87867 7.756L7.468 6.93067C7.56277 6.79796 7.7029 6.70459 7.86187 6.66822C8.02084 6.63186 8.18762 6.65502 8.33067 6.73333C9.27355 7.24862 10.3148 7.55852 11.386 7.64267C11.5532 7.65592 11.7092 7.73169 11.823 7.85488C11.9368 7.97807 12 8.13963 12 8.30733V11.282C12 11.4471 11.9388 11.6063 11.8282 11.7288C11.7177 11.8513 11.5655 11.9285 11.4013 11.9453C11.048 11.982 10.692 12 10.3333 12C4.62667 12 0 7.37333 0 1.66667C0 1.308 0.018 0.952 0.0546667 0.598667C0.0715031 0.434465 0.148656 0.282347 0.271193 0.171756C0.39373 0.0611648 0.552937 -3.55718e-05 0.718 1.55115e-08H3.69267C3.86037 -2.10123e-05 4.02193 0.0631677 4.14512 0.176967C4.26831 0.290767 4.34408 0.446816 4.35733 0.614C4.44148 1.68519 4.75138 2.72645 5.26667 3.66933C5.34498 3.81238 5.36814 3.97916 5.33178 4.13813C5.29541 4.2971 5.20204 4.43723 5.06933 4.532L4.244 5.12133ZM2.56267 4.68333L3.82933 3.77867C3.46986 3.00273 3.22357 2.17923 3.098 1.33333H1.34C1.336 1.444 1.334 1.55533 1.334 1.66667C1.33333 6.63733 5.36267 10.6667 10.3333 10.6667C10.4447 10.6667 10.556 10.6647 10.6667 10.66V8.902C9.82077 8.77643 8.99727 8.53014 8.22133 8.17067L7.31667 9.43733C6.95244 9.29581 6.59867 9.12873 6.258 8.93733L6.21933 8.91533C4.91172 8.17115 3.82885 7.08828 3.08467 5.78067L3.06267 5.742C2.87127 5.40133 2.70419 5.04756 2.56267 4.68333Z" fill="#949BA2"/>
                                </svg></label> {$data.customer.sold[i].cellphone}</div>
                    {/if}
                    {if $data.customer.sold[i].email neq ""}
                        <div class="cellphone"><label><svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12" fill="none">
                                    <path d="M1.00016 0H13.0002C13.177 0 13.3465 0.0702379 13.4716 0.195262C13.5966 0.320286 13.6668 0.489856 13.6668 0.666667V11.3333C13.6668 11.5101 13.5966 11.6797 13.4716 11.8047C13.3465 11.9298 13.177 12 13.0002 12H1.00016C0.823352 12 0.653782 11.9298 0.528758 11.8047C0.403734 11.6797 0.333496 11.5101 0.333496 11.3333V0.666667C0.333496 0.489856 0.403734 0.320286 0.528758 0.195262C0.653782 0.0702379 0.823352 0 1.00016 0ZM12.3335 2.82533L7.04816 7.55867L1.66683 2.81067V10.6667H12.3335V2.82533ZM2.0075 1.33333L7.04083 5.77467L12.0015 1.33333H2.0075Z" fill="#949BA2"/>
                                </svg> {$data.customer.sold[i].email}</div>
                        {if $data.customer.sold[i].emailstatus eq 1}
                            <div class="location label label-success">Verified Email</div>
                        {elseif $data.customer.sold[i].emailstatus eq 2}
                            <div class="location label label-danger">Not Valid Email</div>
                        {/if}
                    {/if}
                    {if $data.customer.sold[i].note neq ""}
                        <div class="note"><label><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <path d="M11.6668 13.6663H2.3335C1.80306 13.6663 1.29436 13.4556 0.919283 13.0806C0.54421 12.7055 0.333496 12.1968 0.333496 11.6663V0.999674C0.333496 0.822863 0.403734 0.653294 0.528758 0.52827C0.653782 0.403246 0.823352 0.333008 1.00016 0.333008H10.3335C10.5103 0.333008 10.6799 0.403246 10.8049 0.52827C10.9299 0.653294 11.0002 0.822863 11.0002 0.999674V8.99967H13.6668V11.6663C13.6668 12.1968 13.4561 12.7055 13.081 13.0806C12.706 13.4556 12.1973 13.6663 11.6668 13.6663ZM11.0002 10.333V11.6663C11.0002 11.8432 11.0704 12.0127 11.1954 12.1377C11.3204 12.2628 11.49 12.333 11.6668 12.333C11.8436 12.333 12.0132 12.2628 12.1382 12.1377C12.2633 12.0127 12.3335 11.8432 12.3335 11.6663V10.333H11.0002ZM9.66683 12.333V1.66634H1.66683V11.6663C1.66683 11.8432 1.73707 12.0127 1.86209 12.1377C1.98712 12.2628 2.15668 12.333 2.3335 12.333H9.66683ZM3.00016 3.66634H8.3335V4.99967H3.00016V3.66634ZM3.00016 6.33301H8.3335V7.66634H3.00016V6.33301ZM3.00016 8.99967H6.3335V10.333H3.00016V8.99967Z" fill="#949BA2"/>
                                </svg></label> {$data.customer.sold[i].note}</div>
                    {/if}
                    {if $data.customer.sold[i].tags_count gt 0}
                        <hr class="hr-card-user">
                    {/if}
                    <div class="locationwrap">
                        {section name = j loop = $data.customer.sold[i].tags}
                            <a class="location label {if $data.customer.sold[i].tags[j].class eq ""}label-info label-info-color{else}{$data.customer.sold[i].tags[j].class}{/if}" href="{$data.customer.sold[i].tags[j].url}">{$data.customer.sold[i].tags[j].title}</a>
                        {/section}
                    </div>
                </div>
            {/section}
            {if $data.sold.showlostleadsbutton neq ""}
                <div class="showmoreleads">
                    <a href="index.php?m=customers&d=listview&status=sold" class="add_asset_button ab-button">{$data.sold.showlostleadsbutton}</a>
                </div>
            {/if}
        </div>
        <div id="lost" class="leadcontainer">
            {section name = i loop = $data.customer.lost}
                <div class="card" id="{$data.customer.lost[i].id}">
                    <div class="locationwrap">
                        {if $data.customer.lost[i].unreaded neq ""}
                            <div class="location label label-danger">{$data.customer.lost[i].unreaded}</div>
                        {/if}
                    </div>
                    <div class="name">
                        <a href="{$data.customer.lost[i].view_url}" >{$data.customer.lost[i].name}</a>
                        <div class="dropdown dropdown-userlist">
                            <button class="btn btn-secondary dropdown-toggle btn-focus-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-compaign">
                                <a class="dropdown-item" href="{$data.customer.lost[i].editurl}"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M8.48533 4.45739L7.54267 3.51472L1.33333 9.72406V10.6667H2.276L8.48533 4.45739ZM9.428 3.51472L10.3707 2.57206L9.428 1.62939L8.48533 2.57206L9.428 3.51472ZM2.828 12.0001H0V9.17139L8.95667 0.214722C9.08169 0.0897416 9.25122 0.0195312 9.428 0.0195312C9.60478 0.0195312 9.77432 0.0897416 9.89933 0.214722L11.7853 2.10072C11.9103 2.22574 11.9805 2.39528 11.9805 2.57206C11.9805 2.74883 11.9103 2.91837 11.7853 3.04339L2.82867 12.0001H2.828Z" fill="#9CA3AF"/>
                                    </svg> Edit</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="admintable_msgbox('Do you really want to delete this item', '{$data.customer.lost[i].deleteurl}')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M10.3335 2.99967H13.6668V4.33301H12.3335V12.9997C12.3335 13.1765 12.2633 13.3461 12.1382 13.4711C12.0132 13.5961 11.8436 13.6663 11.6668 13.6663H2.3335C2.15668 13.6663 1.98712 13.5961 1.86209 13.4711C1.73707 13.3461 1.66683 13.1765 1.66683 12.9997V4.33301H0.333496V2.99967H3.66683V0.999674C3.66683 0.822863 3.73707 0.653294 3.86209 0.52827C3.98712 0.403246 4.15668 0.333008 4.3335 0.333008H9.66683C9.84364 0.333008 10.0132 0.403246 10.1382 0.52827C10.2633 0.653294 10.3335 0.822863 10.3335 0.999674V2.99967ZM11.0002 4.33301H3.00016V12.333H11.0002V4.33301ZM5.00016 6.33301H6.3335V10.333H5.00016V6.33301ZM7.66683 6.33301H9.00016V10.333H7.66683V6.33301ZM5.00016 1.66634V2.99967H9.00016V1.66634H5.00016Z" fill="#9CA3AF"/>
                                    </svg> Delete</a>
                            </div>
                        </div>
                    </div>
                    {if $data.customer.lost[i].cellphone neq ""}
                        <div class="cellphone"><label><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M4.244 5.12133C4.86956 6.22032 5.77968 7.13044 6.87867 7.756L7.468 6.93067C7.56277 6.79796 7.7029 6.70459 7.86187 6.66822C8.02084 6.63186 8.18762 6.65502 8.33067 6.73333C9.27355 7.24862 10.3148 7.55852 11.386 7.64267C11.5532 7.65592 11.7092 7.73169 11.823 7.85488C11.9368 7.97807 12 8.13963 12 8.30733V11.282C12 11.4471 11.9388 11.6063 11.8282 11.7288C11.7177 11.8513 11.5655 11.9285 11.4013 11.9453C11.048 11.982 10.692 12 10.3333 12C4.62667 12 0 7.37333 0 1.66667C0 1.308 0.018 0.952 0.0546667 0.598667C0.0715031 0.434465 0.148656 0.282347 0.271193 0.171756C0.39373 0.0611648 0.552937 -3.55718e-05 0.718 1.55115e-08H3.69267C3.86037 -2.10123e-05 4.02193 0.0631677 4.14512 0.176967C4.26831 0.290767 4.34408 0.446816 4.35733 0.614C4.44148 1.68519 4.75138 2.72645 5.26667 3.66933C5.34498 3.81238 5.36814 3.97916 5.33178 4.13813C5.29541 4.2971 5.20204 4.43723 5.06933 4.532L4.244 5.12133ZM2.56267 4.68333L3.82933 3.77867C3.46986 3.00273 3.22357 2.17923 3.098 1.33333H1.34C1.336 1.444 1.334 1.55533 1.334 1.66667C1.33333 6.63733 5.36267 10.6667 10.3333 10.6667C10.4447 10.6667 10.556 10.6647 10.6667 10.66V8.902C9.82077 8.77643 8.99727 8.53014 8.22133 8.17067L7.31667 9.43733C6.95244 9.29581 6.59867 9.12873 6.258 8.93733L6.21933 8.91533C4.91172 8.17115 3.82885 7.08828 3.08467 5.78067L3.06267 5.742C2.87127 5.40133 2.70419 5.04756 2.56267 4.68333Z" fill="#949BA2"/>
                                </svg></label> {$data.customer.lost[i].cellphone}</div>
                    {/if}
                    {if $data.customer.lost[i].email neq ""}
                        <div class="cellphone"><label><svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12" fill="none">
                                    <path d="M1.00016 0H13.0002C13.177 0 13.3465 0.0702379 13.4716 0.195262C13.5966 0.320286 13.6668 0.489856 13.6668 0.666667V11.3333C13.6668 11.5101 13.5966 11.6797 13.4716 11.8047C13.3465 11.9298 13.177 12 13.0002 12H1.00016C0.823352 12 0.653782 11.9298 0.528758 11.8047C0.403734 11.6797 0.333496 11.5101 0.333496 11.3333V0.666667C0.333496 0.489856 0.403734 0.320286 0.528758 0.195262C0.653782 0.0702379 0.823352 0 1.00016 0ZM12.3335 2.82533L7.04816 7.55867L1.66683 2.81067V10.6667H12.3335V2.82533ZM2.0075 1.33333L7.04083 5.77467L12.0015 1.33333H2.0075Z" fill="#949BA2"/>
                                </svg> {$data.customer.lost[i].email}</div>
                        {if $data.customer.lost[i].emailstatus eq 1}
                            <div class="location label label-success">Verified Email</div>
                        {elseif $data.customer.lost[i].emailstatus eq 2}
                            <div class="location label label-danger">Not Valid Email</div>
                        {/if}
                    {/if}
                    {if $data.customer.lost[i].note neq ""}
                        <div class="note"><label><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <path d="M11.6668 13.6663H2.3335C1.80306 13.6663 1.29436 13.4556 0.919283 13.0806C0.54421 12.7055 0.333496 12.1968 0.333496 11.6663V0.999674C0.333496 0.822863 0.403734 0.653294 0.528758 0.52827C0.653782 0.403246 0.823352 0.333008 1.00016 0.333008H10.3335C10.5103 0.333008 10.6799 0.403246 10.8049 0.52827C10.9299 0.653294 11.0002 0.822863 11.0002 0.999674V8.99967H13.6668V11.6663C13.6668 12.1968 13.4561 12.7055 13.081 13.0806C12.706 13.4556 12.1973 13.6663 11.6668 13.6663ZM11.0002 10.333V11.6663C11.0002 11.8432 11.0704 12.0127 11.1954 12.1377C11.3204 12.2628 11.49 12.333 11.6668 12.333C11.8436 12.333 12.0132 12.2628 12.1382 12.1377C12.2633 12.0127 12.3335 11.8432 12.3335 11.6663V10.333H11.0002ZM9.66683 12.333V1.66634H1.66683V11.6663C1.66683 11.8432 1.73707 12.0127 1.86209 12.1377C1.98712 12.2628 2.15668 12.333 2.3335 12.333H9.66683ZM3.00016 3.66634H8.3335V4.99967H3.00016V3.66634ZM3.00016 6.33301H8.3335V7.66634H3.00016V6.33301ZM3.00016 8.99967H6.3335V10.333H3.00016V8.99967Z" fill="#949BA2"/>
                                </svg></label> {$data.customer.lost[i].note}</div>
                    {/if}
                    {if $data.customer.lost[i].tags_count gt 0}
                        <hr class="hr-card-user">
                    {/if}
                    <div class="locationwrap">
                        {section name = j loop = $data.customer.lost[i].tags}
                            <a class="location label {if $data.customer.lost[i].tags[j].class eq ""}label-info label-info-color{else}{$data.customer.lost[i].tags[j].class}{/if}" href="{$data.customer.lost[i].tags[j].url}">{$data.customer.lost[i].tags[j].title}</a>
                        {/section}
                    </div>
                </div>
            {/section}
            {if $data.lost.showlostleadsbutton neq ""}
                <div class="showmoreleads">
                    <a href="index.php?m=customers&d=listview&status=lost" class="add_asset_button ab-button">{$data.lost.showlostleadsbutton}</a>
                </div>
            {/if}
        </div>

        {if $data.nodata neq ""}
            <div class="nodatafound">
                {$data.nodata}
            </div>
        {/if}
    </div>
</div>

<script>{literal}
    function  admintable_msgbox(question, url)
    {
        if (confirm(question+(question.indexOf('?', 0)>=0?'':'?')))
        {
            setTimeout(function() { document.location.href = url; }, 30);
        }
    }
{/literal}
</script>
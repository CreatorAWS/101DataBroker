{assign var=m value=$modules[$index]}
{if $modules[$index].mode eq 'view'}
    <link type="text/css" href="themes/{$special.theme}/progressbar/loading-bar.css" rel="stylesheet">
    <div class="row first_dashboard_row">
        {include file="block_begin.tpl"}
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}

        <div class="dash-nav-row">
            <ul>
                <li class="{$m.dashboard_day_nav_class}"><a href="index.php?m=statistics">Day</a></li>
                <li class="{$m.dashboard_week_nav_class}"><a href="index.php?m=statistics&time=week">Week</a></li>
                <li class="{$m.dashboard_twoweek_nav_class}"><a href="index.php?m=statistics&time=twoweek">2 Weeks</a></li>
                <li class="{$m.dashboard_month_nav_class}"><a href="index.php?m=statistics&time=month">Month</a></li>
            </ul>
        </div>
        <div class="row">
            <div class="daylistats">
                <div>
                    <div class="col-md-6 box box-1 col-xs-12 statsbox">
                        <div class="wrapper">
                            <div>
                                <div class="panel-heading"><h4>Messages</h4></div>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="day">
                                        <div class="content-container">
                                            <div class="col-md-4">
                                                <div class="messages-stats">
                                                    <div>
                                                        <div class="icon">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                                        </div>
                                                        <div class="numbers">{$m.messages_sent}</div>
                                                        <div class="text">Messages Sent</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="messages-stats">
                                                    <div>
                                                        <div class="icon">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                                                        </div>
                                                        <div class="numbers">{$m.messages_received}</div>
                                                        <div class="text">Messages Received</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="messages-stats">
                                                    <div>
                                                        <div class="icon">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone-outgoing"><polyline points="23 7 23 1 17 1"/><line x1="16" y1="8" x2="23" y2="1"/><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                                        </div>
                                                        <div class="numbers">{$m.calls}</div>
                                                        <div class="text">Calls</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 box statsbox col-xs-12">
                        <div class="wrapper">
                            <div>
                                <div class="panel-heading"><h4>{$sm.label.customers}</h4></div>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="statsday">
                                        <div class="content-container">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="tablestats">
                                                    <table class="table table-striped fullstats">
                                                        <tr>
                                                            <td>Received</td>
                                                            <td>{$m.received.count}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Contacted</td>
                                                            <td>{$m.contact.count}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Appoinment</td>
                                                            <td>{$m.appointment.count}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Sold</td>
                                                            <td>{$m.sold.count}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Lost</td>
                                                            <td>{$m.lost.count}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        {include file="block_end.tpl"}
    </div>
    <div class="row">
        <div class="daylistats">
            <div class="dashboard-section-headline">Status</div>
            <div class="status-stats">

                <div class="boxwrapper">
                    <div class="col-md-6 box otherstats col-xs-12">
                        <div class="wrapper">
                            <div class="statsrow col-md-6">
                                <div>Total Leads <span>{$m.total_leads}</span></div>
                            </div>
                            <div class="statsrow col-md-6">
                                <div>Total Contacts <span>{$m.total_count}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 box progressbar col-xs-12">
                        <div class="wrapper">
                            <div class="title">Percentage of Leads</div>
                            <div class="ldBar label-center" data-preset="circle" data-stroke-trail="#e1ebfc" data-stroke-trail-width="3" data-stroke="#00aeef" data-stroke-width="3" data-value="{$m.percentage.count}"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="daylistats">
            <div class="dashboard-section-headline">Campaigns</div>
            <div class="status-stats">

                <div class="boxwrapper">
                    <div class="col-md-8 box otherstats col-xs-12">
                        <div class="wrapper">
                            <div class="statsrow col-md-4">
                                <div>Total Opens <span>{$m.total_openers}</span></div>
                            </div>
                            <div class="statsrow col-md-4" style="border-right: 1px solid #eee; border-left: 1px solid #eee">
                                <div>Total Clicks <span>{$m.total_clickers}</span></div>
                            </div>
                            <div class="statsrow col-md-4">
                                <div>Total Unsubscribes <span>{$m.total_unsubscribers}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 box statsbox col-xs-12">
                        <div class="wrapper">
                            <div>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="statsday">
                                        <div class="content-container">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="tablestats">
                                                    <table class="table table-striped fullstats">
                                                        <tr>
                                                            <td>Sent</td>
                                                            <td>{$m.campaigns_sent.day.count}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Scheduled</td>
                                                            <td>{$m.campaigns_scheduled.day.count}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Drafted</td>
                                                            <td>{$m.campaigns_draft.day.count}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <script type="text/javascript" src="themes/{$special.theme}/progressbar/loading-bar.js"></script>
{/if}

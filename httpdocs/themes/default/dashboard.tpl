{assign var=m value=$modules[$index]}
{if $modules[$index].mode eq 'view'}
    <link type="text/css" href="themes/{$special.theme}/progressbar/loading-bar.css" rel="stylesheet">
    {include file="block_begin.tpl" panel_title="Dashboard"}
    {if $m.error_message neq ""}
        <div class="aui-message aui-message-error">{$m.error_message}</div>
    {/if}

    <div class="row">
        <div class="daylistats">
            <div>
                <div class="col-md-12 box box-1 col-xs-12">
                    <div class="wrapper">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="day">
                                <div class="content-container">
                                    <div style="width:100%;">
                                        <div class="chartjs-size-monitor">
                                            <div class="chartjs-size-monitor-expand">
                                                <div class=""></div>
                                            </div>
                                            <div class="chartjs-size-monitor-shrink">
                                                <div class=""></div>
                                            </div>
                                        </div>
                                        <canvas id="canvas" style="display: block; width: 100%; height: 400px;"  class="chartjs-render-monitor"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {if $m.google_search_available eq 1 OR $m.builtwith_search_available eq 1}
                <div class="status-stats">

                    <div class="boxwrapper box-wrap-dashboard">
                        <div class="col-md-6 box statsbox col-xs-12">
                            <div class="wrapper wrap-border">
                                <div class="buttons-container status-total-head">
                                    <div class="dashboard-section-headline status-text-pad">Leads</div>
                                    <ul class="nav nav-tabs nav-padding-dash" role="tablist">
                                        <li role="presentation" class="col-md-4 col-xs-4 active"><a href="#statsday" aria-controls="statsday" role="tab" data-toggle="tab">Day</a></li>
                                        <li role="presentation" class="col-md-4 col-xs-4"><a href="#statsweek" aria-controls="statsweek" role="tab" data-toggle="tab">Week</a></li>
                                        <li role="presentation" class="col-md-4 col-xs-4"><a href="#statsmonth" aria-controls="statsmonth" role="tab" data-toggle="tab">Month</a></li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="statsday">
                                        <div class="content-container tab-content-border">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="tablestats">
                                                    <table class="table table-striped fullstats">
                                                        {if $m.google_search_available eq 1}
                                                            <tr>
                                                                <td>Google Search</td>
                                                                <td>{$m.google.day.count}</td>
                                                            </tr>
                                                        {/if}
                                                        {if $m.builtwith_search_available eq 1}
                                                            <tr>
                                                                <td>Search By Technology</td>
                                                                <td>{$m.builtwith.day.count}</td>
                                                            </tr>
                                                        {/if}
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="statsweek">
                                        <div class="content-container">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="tablestats tabledash">
                                                    <table class="table table-striped fullstats">
                                                        {if $m.google_search_available eq 1}
                                                            <tr>
                                                                <td>Google Search</td>
                                                                <td>{$m.google.week.count}</td>
                                                            </tr>
                                                        {/if}
                                                        {if $m.builtwith_search_available eq 1}
                                                            <tr>
                                                                <td>Search By Technology</td>
                                                                <td>{$m.builtwith.week.count}</td>
                                                            </tr>
                                                        {/if}

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="statsmonth">
                                        <div class="content-container">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="tablestats">
                                                    <table class="table table-striped fullstats">
                                                        {if $m.google_search_available eq 1}
                                                            <tr>
                                                                <td>Google Search</td>
                                                                <td>{$m.google.month.count}</td>
                                                            </tr>
                                                        {/if}
                                                        {if $m.builtwith_search_available eq 1}
                                                            <tr>
                                                                <td>Search By Technology</td>
                                                                <td>{$m.builtwith.month.count}</td>
                                                            </tr>
                                                        {/if}
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 box progressbar col-xs-12">
                            <div class="total-lead-per">
                                <div class="wrapper statsrow-row">
                                    <div class="statsrow">
                                        <div class="total-leads">Total Searches <span>{$m.total_searches}</span></div>
                                    </div>
                                </div>
                                <div class="vert-line"></div>
                                <div class="wrapper statsrow-row">
                                    <div class="statsrow">
                                        <div class="total-leads">Total Leads <span>{$m.total_leads}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>

    {include file="block_end.tpl"}

    {if $m.google_search_available eq 1}
        <div class="row">
            <div class="dash-total-requested memberstats ">
                <div class="panel campaignsstats">
                    <div class="panel-body panel-body-bg">
                        {include file="block_begin.tpl" panel_title="Latest Google Searches"}
                        <div class="row secondblock secondblock-pad">
                            <div class="stats stats-div col-md-12 col-xs-12">
                                <div class="emailcampaigns app-table-border">
                                    {include file="common_adminpanel.tpl" panelblocks=$modules[$index].panel_google_search}
                                    {if $m.googlesearchlist.norecords neq "0"}
                                        <div class="startcampaignbtn"><a href="index.php?m=searchleads&d=search_history" class="btn-success btn submitnewbutton">Show More</a></div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        {include file="block_end.tpl"}
                    </div>
                </div>
            </div>
        </div>
    {/if}
    {if $m.builtwith_search_available eq 1}
        <div class="row">
            <div class="dash-total-requested memberstats ">
                <div class="panel campaignsstats">
                    <div class="panel-body panel-body-bg">
                        {include file="block_begin.tpl" panel_title="Latest Searches By Technology"}
                        <div class="row secondblock secondblock-pad">
                            <div class="stats stats-div col-md-12 col-xs-12">
                                <div class="emailcampaigns app-table-border">
                                    {include file="common_adminpanel.tpl" panelblocks=$modules[$index].panel_builtwith_search}
                                    {if $m.builtwithsearchlist.norecords neq "0"}
                                        <div class="startcampaignbtn"><a href="index.php?m=searchtechleads" class="btn-success btn submitnewbutton">Show More</a></div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        {include file="block_end.tpl"}
                    </div>
                </div>
            </div>
        </div>
    {/if}
    <script type="text/javascript" src="themes/{$special.theme}/progressbar/loading-bar.js"></script>
    <script type="text/javascript" src="themes/{$special.theme}/chart/pie-chart.js"></script>
    <script type="text/javascript" src="themes/{$special.theme}/chart/graph.js"></script>
    <script type="text/javascript" src="themes/{$special.theme}/chart/graphchart.js"></script>

    <script>{literal}
        var config = {
            type: 'line',
            data: {
                labels: ['00', '02', '04', '06', '08', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30'],
                datasets: [{
                    label: 'Google Search',
                    backgroundColor: "#ff6666",
                    borderColor: "#ff6666",
                    fill: false,
                    data: [{/literal}{$m.google_search}{literal}],
                }, {
                    label: 'Search By Technology',
                    backgroundColor: "#4dffa6",
                    borderColor: "#4dffa6",
                    fill: false,
                    data: [{/literal}{$m.builtwith_search}{literal}],
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: ''
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Day'
                        },

                    }],
                    yAxes: [{
                        display: true,
                        //type: 'logarithmic',
                        scaleLabel: {
                            display: true,
                            labelString: 'Messages'
                        },
                        ticks: {
                            min: 00,
                            max: {/literal}{$m.max_value}{literal},

                            // forces step size to be 5 units
                            stepSize: {/literal}{$m.max_value/5|ceil}{literal}
                        }
                    }]
                }
            }
        };

        window.onload = function() {
            var ctx = document.getElementById('canvas').getContext('2d');
            window.myLine = new Chart(ctx, config);
        };

        document.getElementById('randomizeData').addEventListener('click', function() {
            config.data.datasets.forEach(function(dataset) {
                dataset.data = dataset.data.map(function() {
                    return randomScalingFactor();
                });

            });

            window.myLine.update();
        });
        {/literal}</script>
{/if}

<style>{literal}
    .panel{
        background: transparent;
    }
    {/literal}</style>

<div class="row">
    <div class="dash-total-requested memberstats ">
        <div class="panel campaignsstats">
            <div class="panel-body">

                <div class="row secondblock">
                    <div class="stats col-md-3 col-xs-12">
                        <div class="card-padds">
                            <div class="user-icon-card">                                
                                <p class="contact-iconfa"><i class="fas fa-users blue"></i></p>
                                <p>Contacts</p>
                            </div>
                            <div class="card-contact-list">
                                <p class="contact-count">{$data.campaignstats.customerscount}</p>
                                <a class="contact-link-card" href="{$data.customer.url}">List of contacts </a>
                            </div>
                        </div>
                    </div>
                    <div class="stats col-md-3 col-xs-12">
                        <div class="card-padds">
                            <div class="user-icon-card">
                                <p class="contact-iconfa"><i class="far fa-eye green"></i></p>
                                <p>Opened</p>
                            </div>
                            <div class="card-contact-list">
                                <div class="percent-count">
                                    <p class="contact-count">{$data.campaignstats.openedcount}</p>
                                    <span class="percentage">{$data.campaignstats.openedpercent}%</span>
                                </div>
                                <a class="contact-link-card" href="{$data.openers.url}">List of openers </a>
                            </div>
                        </div>
                    </div>
                    <div class="stats col-md-3 col-xs-12">
                        <div class="card-padds">
                            <div class="user-icon-card">
                                <p class="contact-iconfa"><i class="fa fa-hand-pointer-o violet"></i></p>
                                <p>Clicked</p>
                            </div>
                            <div class="card-contact-list">
                                <div class="percent-count">
                                    <p class="contact-count">{$data.campaignstats.clicked}</p>
                                    <span class="percentage">{$data.campaignstats.clickedpercent}%</span>
                                </div>
                                <a class="contact-link-card" href="{$data.clickers.url}">List of clickers </a>
                            </div>
                        </div>
                    </div>
                    <div class="stats col-md-3 col-xs-12">
                        <div class="card-padds">
                            <div class="user-icon-card">
                                <p class="contact-iconfa"><i class="fas fa-comments yellow"></i></p>
                                <p>SMS Delivered</p>
                            </div>
                            <div class="card-contact-list">
                                <div class="percent-count">
                                    <p class="contact-count">{$data.campaignstats.smsdelivered}</p>
                                    <span class="percentage">{$data.campaignstats.smsdeliveredpercent}%</span>
                                </div>
                                <a class="contact-link-card" href="{$data.smsdelivered.url}">List </a>
                            </div>
                        </div>
                    </div>
                </div>    

                <div class="row secondblock">
                    <div class="stats stats-padding-summary col-md-12 col-xs-12">
                        <div class="emailcampaigns">
                            <div class="blocktitle compaign-summart-title">Campaign Summary</div>
                            <section class="inline-table-style inline-table-box">
                                <div class="compaign-summary-table">
                                    <div class="td-table-cell" style="width: 30%;">
                                        <div class="compaign-summary-flex">
                                            <p>ID : {$data.campaign.id}</p>
                                            <p class="com-black">{$data.campaign.title}</p>
                                        </div>	
                                    </div>
                                    <div class="td-table-cell" style="width: 20%;">
                                        <div class="compaign-summary-flex">
                                            <p>DATE</p>
                                            <p class="com-black">{$data.campaign.time}</p>
                                        </div>	
                                    </div>
                                    <div class="td-table-cell" style="width: 10%;">
                                        <div class="compaign-summary-flex">
                                            <p>RECIPIENTS</p>
                                            <p class="com-blue">{$data.campaign.recipients}</p>
                                        </div>	
                                    </div>
                                    <div class="td-table-cell" style="width: 15%;">
                                        <div class="compaign-summary-flex">
                                            <p>EMAIL OPENERS</p>
                                            <p class="com-lightblue">{$data.campaign.openers}</p>
                                        </div>
                                    </div>
                                    <div class="td-table-cell" style="width: 15%;">
                                        <div class="compaign-summary-flex">
                                            <p>EMAIL CLICKERS</p>
                                            <p class="com-green">{$data.campaign.clickers}</p>
                                        </div>	
                                    </div>
                                    <div class="td-table-cell" style="width: 10%;">
                                        <div class="compaign-summary-flex">
                                            <p>SMS DELIVERED</p>
                                            <p class="com-red">{$data.campaign.smsdelivered}</p>
                                        </div>	
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>


                <div class="row secondblock">
                    <div class="stats col-md-12 col-xs-12">
                        <div class="emailcampaigns compaign-sequence">
                            <div class="blocktitle compaign-summart-title">Sequence Details</div>
                            {section name=i loop=$data.sequence}
                            <section class="inline-table-style inline-table-box">                                
                                <div class="compaign-summary-table">
                                    <div class="td-table-cell" style="width: 30%;">
                                        <div class="compaign-summary-flex">
                                            <p>DATE</p>
                                            <p class="com-black">{$data.sequence[i].date}</p>
                                        </div>	
                                    </div>
                                    <div class="td-table-cell" style="width: 10%;">
                                        <div class="compaign-summary-flex">
                                            <p>TEXT</p>
                                            <p class="com-link-color">{$data.sequence[i].mode}</p>
                                        </div>	
                                    </div>
                                    <div class="td-table-cell" style="width: 10%;">
                                        <div class="compaign-summary-flex">
                                            <p>RECIPIENTS</p>
                                            <p class="com-blue">{$data.sequence[i].customers}</p>
                                        </div>	
                                    </div>
                                    <div class="td-table-cell" style="width: 10%;">
                                        <div class="compaign-summary-flex">
                                            <p>SENT</p>
                                            <p class="com-lightblue">{$data.sequence[i].sent}</p>
                                        </div>
                                    </div>
                                    <div class="td-table-cell" style="width: 10%;">
                                        <div class="compaign-summary-flex">
                                            <p>SCHEDULED</p>
                                            <p class="com-green">{$data.sequence[i].scheduled}</p>
                                        </div>	
                                    </div>
                                    <div class="td-table-cell" style="width: 20%;">
                                        <div class="compaign-summary-flex">
                                            <p>TEMPLATE</p>
                                            <p>
                                                {if $data.sequence[i].template_url neq ""}
                                                    <a href="{$data.sequence[i].template_url}">{$data.sequence[i].template}</a>
                                                {else}
                                                    {$data.sequence[i].template}
                                                {/if}
                                            </p>
                                        </div>	
                                    </div>
                                    <div class="td-table-cell" style="width: 10%;">
                                        <div class="compaign-summary-flex">
                                            <p>STATS.</p>
                                            <a href="{$data.sequence[i].details}">Details</a>
                                        </div>	
                                    </div>
                                </div>
                            </section>
                            {/section}
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </div>
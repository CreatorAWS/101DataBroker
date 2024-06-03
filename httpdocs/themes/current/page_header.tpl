<!DOCTYPE html>
<html lang="en"><head>{$special.document.headstart}{$special.document.headdef}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900' rel='stylesheet' type='text/css'>
    
    <!-- Vendor styles -->
    <link rel="stylesheet" href="themes/{$special.theme}/styles/css/regular.css"/>
    <link rel="stylesheet" href="themes/{$special.theme}/styles/css/solid.css"/>
    <link rel="stylesheet" href="themes/{$special.theme}/styles/css/fontawesome.min.css"/>
    <link rel="stylesheet" href="themes/{$special.theme}/vendor/fontawesome/css/font-awesome.css"/>
    <link rel="stylesheet" href="themes/{$special.theme}/vendor/animate.css/animate.css"/>
    <link rel="stylesheet" href="themes/{$special.theme}/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="themes/{$special.theme}/vendor/toastr/toastr.min.css"/>
    <link type="text/css" href="themes/{$special.theme}/assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.min.css" rel="stylesheet" />
    <link type="text/css" href="themes/{$special.theme}/assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.structure.min.css" rel="stylesheet" />
    <link type="text/css" href="themes/{$special.theme}/assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="themes/{$special.theme}/styles/selectize.bootstrap3.css"/>
    <!-- App styles -->
    <link rel="stylesheet" href="themes/{$special.theme}/styles/pe-icons/pe-icon-7-stroke.css"/>
    <link rel="stylesheet" href="themes/{$special.theme}/styles/pe-icons/helper.css"/>
    <link rel="stylesheet" href="themes/{$special.theme}/styles/stroke-icons/style.css"/>
    <link rel="stylesheet" href="themes/{$special.theme}/styles/style.css">

    <script src="themes/{$special.theme}/vendor/jquery/dist/jquery.min.js"></script>

	<script type="text/javascript" src="themes/{$special.theme}/script.js"></script>
    <link type="text/css" href="themes/{$special.theme}/vendor/niceselect/css/nice-select.css" rel="stylesheet">
	<link type="text/css" href="themes/{$special.theme}/stylesheets.css" rel="stylesheet">
	<link type="text/css" href="themes/{$special.theme}/styles.css" rel="stylesheet">
	<link type="text/css" href="themes/{$special.theme}/header.css" rel="stylesheet">

{if $sm.g.m eq "companiesmgmt" AND $sm.g.d eq "edit" OR $sm.g.d eq "add"}
    <script>{literal}
        $(document).ready(function()  {
            var characters = 160;
            $(".sms_template_initial").append("You have <strong>"+  characters+"</strong> characters remaining");
            $("#sms_template_initial").keyup(function(){
                if($(this).val().length > characters){
                    $(this).val($(this).val().substr(0, characters));
                }
                var remaining = characters -  $(this).val().length;
                $(".sms_template_initial").html("You have <strong>"+  remaining+"</strong> characters remaining");
                if(remaining <= 10)
                {
                    $(".sms_template_initial").css("color","red");
                }
                else
                {
                    $(".sms_template_initial").css("color","black");
                }
            });

            $(".sms_template_credentials").append("You have <strong>"+  characters+"</strong> characters remaining");
            $("#sms_template_credentials").keyup(function(){
                if($(this).val().length > characters){
                    $(this).val($(this).val().substr(0, characters));
                }
                var remaining = characters -  $(this).val().length;
                $(".sms_template_credentials").html("You have <strong>"+  remaining+"</strong> characters remaining");
                if(remaining <= 10)
                {
                    $(".sms_template_credentials").css("color","red");
                }
                else
                {
                    $(".sms_template_credentials").css("color","black");
                }
            });



        });

    {/literal}</script>
    {/if}
{$_settings.meta_header_text}
{$special.document.headend}</head>
{config_load file="main.cfg"}
<body {if $special.body_onload neq ""} onload="{$special.body_onload}"{/if}{$special.document.bodymodifier} class="{if $sm.s.body_class neq ""}{$sm.s.body_class}{/if} {if $userinfo.level eq 0}signwrapper{/if}">{$special.document.bodystart}
{if $_settings.header_static_text neq ""}{$_settings.header_static_text}{/if}


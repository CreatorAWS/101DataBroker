
function transition(){
    $("tr.admintable_row").each(function() {
        var id = $(this).find("td.at-cell-id span").html();
        var status = $(this).find("td.at-cell-email").html();
        if (status !== 'No Email' || status !== '<img src="themes/default/images/admintable/processing.gif">')
            {
                $(this).find("td.at-cell-email").load('index.php?m=searchleads&d=getemailstatus&id=' + id);
            }

    });
}

$( document ).ready(function(){
    setInterval(transition, 10000);
});

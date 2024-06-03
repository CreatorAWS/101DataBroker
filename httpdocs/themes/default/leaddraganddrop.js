dragula([document.getElementById('received'), document.getElementById('contact'), document.getElementById('appointment'), document.getElementById('sold'), document.getElementById('lost')])
    .on('drag', function (el) {
        el.className = el.className.replace('ex-moved', '');
    })

    .on('drop', function (el) {
        el.className += ' ex-moved';
        var parentElId = $(el).parent().attr('id');
        var droppedElId = $(el).attr('id');
        var droppedElIndex = $(el).index();
        $.ajax({
            url: "index.php?m=customers&d=setstatus&theonepage=1",
            type: 'GET',
            data: { status: parentElId,
                    id: droppedElId,
                    position: droppedElIndex
            }
            }).done(function() {
            //do something else
//            alert(droppedElIndex);
            });

        })
    .on('over', function (el, container) {
    container.className += ' ex-over';
    })
    .on('out', function (el, container) {
    container.className = container.className.replace('ex-over', '');
    });
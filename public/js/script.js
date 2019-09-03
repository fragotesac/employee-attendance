function buscarAction(method, url, data, response, beforeSend)
{
    $.ajax({
        method: method,
        url: url,
        data: data,
        beforeSend: function() {
            showOverlay(1)
            if (typeof beforeSend != 'undefined') {
                beforeSend();
            }
        },
        success: function(result) {
            response(result);
            setTimeout(function(){
                showOverlay(0)
            }, 800);
        }
    });
}

function showOverlay(num)
{
    var overlay = document.createElement("div");
    overlay.classList.add("overlayLoading");
    if (num === 1) {
        overlay.innerHTML = "<span class='text-overlay'> <i class='fa fa-spinner fa-spin '></i> Cargando... </span>";
        document.body.appendChild(overlay);
    }else{
        $( ".overlayLoading").remove();
    }
}
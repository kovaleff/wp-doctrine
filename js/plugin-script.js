jQuery('form.request-form').submit(function (e){
    e.preventDefault();
    let user_id = jQuery('[name=request-user]').val();
    let title = jQuery('[name=request-title]').val();
        if(title == ''){
            jQuery('[name=request-title]').addClass('err')
            return;
        } else {
            jQuery('[name=request-title]').removeClass('err')
        }
    let request = jQuery('[name=request-content]').val()
        if(request == ''){
            jQuery('[name=request-content]').addClass('err')
            return;
        } else {
            jQuery('[name=request-content]').removeClass('err')
        }
    jQuery.ajax({
        method: "POST",
        url: `${request_obj.rest_url}${user_id}`,
        data: { title: title,  request: request }
        }).done(function(data){
            jQuery('[name=request-title]').val('')
            jQuery('[name=request-content]').val('')
            alert(data.result);
        })
})

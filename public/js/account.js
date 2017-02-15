function isExistsEmail()
{
    var email = $('#email').val();
    var id = $('#id').val();
    var url = url_base + '/account/isExistsEmail';

    $.get(url, {email : email, id : id}, function(data)
    {
        var json = _json(data);
        if(json)
        {
            var html;
            switch (json['error_code'])
            {
                case 0:
                case 1:
                    html = '';
                    break;
                case 2:
                    html = '<ul><li>'+json['message']+'</li></ul>';
                    break;
            }
            $('#alert-danger-email').html(html);
        }
    });
}

function isExistsPhone()
{
    var phone = $('#phone').val();
    var id = $('#id').val();
    var url = url_base + '/account/isExistsPhone';

    $.get(url, {phone : phone, id : id}, function(data)
    {
        var json = _json(data);
        if(json)
        {
            var html;
            switch (json['error_code'])
            {
                case 0:
                case 1:
                    html = '';
                    break;
                case 2:
                    html = '<ul><li>'+json['message']+'</li></ul>';
                    break;
            }
            $('#alert-danger-phone').html(html);
        }
    });
}

//if(window.innerHeight > window.innerWidth){
//    alert("Please use Landscape!");
//}
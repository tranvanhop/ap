function replaceUrlParam(url, paramName, paramValue)
{
    if (paramValue == null)
        paramValue = '';
    var pattern = new RegExp('\\b(' + paramName + '=).*?(&|$)')
    if (url.search(pattern) >= 0) {
        return url.replace(pattern, '$1' + paramValue + '$2');
    }
    return url + (url.indexOf('?') > 0 ? '&' : '?') + paramName + '=' + paramValue
}

function _json(str)
{
    try
    {
        var json = JSON.parse(str);
        if(json.hasOwnProperty('error_code'))
            return json;
    }
    catch(e)
    {

    }

    return false;
}

function show_popup(id, title, body)
{
    $("#modal-title").html(title);
    $("#modal-body").html(body);
    $("#"+id).modal('toggle');
}

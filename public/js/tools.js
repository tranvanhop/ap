function getTools(page)
{
    if(page === undefined)
        page = 1;

    var search = window.location.search;
    var url = replaceUrlParam(url_base + '/tools/getItems' + search, 'page', page);

    $.get(url, {}, function(data)
    {
        var urlAjax = replaceUrlParam(url_base + '/tools' + search, 'page', page);
        window.history.pushState('', '', urlAjax);
        $('#tools').html(data);
    });
}
function getToolsDetail(id)
{
    var url = url_base + '/tools/getItem/' + id;

    $.get(url, {}, function(data)
    {
        $('#tools-item').html(data);
    });
}
function getSampleOutputs(oR_k, oR_v, oR_u, rS_k, rS_v, rS_u, c_k, c_v, c_u, n_k, n_v, n_u, index, regex)
{
    var url = url_base + '/tools/getSampleOutputs';

    var params = {};
    params[oR_k] = {value:oR_v, unit:oR_u};
    params[rS_k] = {value:rS_v, unit:rS_u};
    params[c_k] = {value:c_v, unit:c_u};
    params[n_k] = {value:n_v, unit:n_u};
    params['index'] = index;


    $.get(url, params, function(data)
    {
        var id = '#sample-outputs';
        if(index > 0)
            id += regex + index;

        $(id).html(data);
    });
}
function getSampleOutput(key, value, unit, index, required, regex)
{
    var url = url_base + '/tools/getSampleOutput';

    $.get(url, {key:key, value:value, unit:unit, index:index, required:required}, function(data)
    {
        var id = '#' + key + regex;
        if(index > 0)
            id += index;

        $(id).html(data);
    });
}

function addInputs()
{
    var url = url_base + '/tools/addInputs';
    $.get(url, {}, function(data)
    {
        $('#tools-item').html(data);
    });
}
function removeInputs()
{
    var url = url_base + '/tools/removeInputs';
    $.get(url, {}, function(data)
    {
        $('#tools-item').html(data);
    });
}


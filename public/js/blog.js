function getBlog(page)
{
    if(page === undefined)
        page = 1;

    var search = window.location.search;
    var url = replaceUrlParam(url_base + '/blog/getBlog' + search, 'page', page);

    $.get(url, {}, function(data)
    {
        var urlAjax = replaceUrlParam(url_base + '/blog' + search, 'page', page);
        window.history.pushState('', '', urlAjax);
        $('#blog-posts').html(data);
    });
}
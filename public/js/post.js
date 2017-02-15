function getPosts(page)
{
    if(page === undefined)
        page = 1;

    var search = window.location.search;
    var url = replaceUrlParam(url_base + '/post/getItems' + search, 'page', page);

    $.get(url, {}, function(data)
    {
        var urlAjax = replaceUrlParam(url_base + '/post' + search, 'page', page);
        window.history.pushState('', '', urlAjax);
        $('#posts').html(data);
    });
}

function getRecent()
{
    var url = url_base + '/post/getRecent';

    $.get(url, {}, function(data)
    {
        $('#recent').html(data);
    });
}

function getCategories()
{
    var search = window.location.search;
    var url = url_base + '/post/getCategories' + search;

    $.get(url, {}, function(data)
    {
        $('#categories').html(data);
    });
}

function getTags()
{
    var url = url_base + '/post/getTags';

    $.get(url, {}, function(data)
    {
        $('#tags').html(data);
    });
}

function getComments(post_id)
{
    if(post_id === undefined)
        post_id = 0;

    var url = url_base + '/post/getComments/' + post_id;

    $.get(url, {}, function(data)
    {
        $('#comments').html(data);
    });
}

function getSubComments(id)
{
    if(id === undefined)
        id = 0;

    var url = url_base + '/post/getSubComments/' + id;

    $.get(url, {}, function(data)
    {
        $('#sub-comments-'+id).html(data);
    });
}

function reply(id)
{
    if(id === undefined)
        id = 0;

    var url = url_base + '/post/getFormSubComment/' + id;
    $('#reply-'+id).hide();

    $.get(url, {}, function(data)
    {
        $('#sub-comments-'+id).append(data);
    });
}
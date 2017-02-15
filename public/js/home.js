function getCaoAnhPhuong()
{
    var url = url_base + '/home/getCaoAnhPhuong?token=';

    $.get(url, {}, function(data)
    {
        $('#cao-anh-phuong').html(data);
    });
}
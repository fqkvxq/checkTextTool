var ajax,
source = $('#source'),
change = $('#change'),
out_source = $('#out_source'),
out_change = $('#out_change'),
out_data = $('#out_data'),
data = out_data.find('pre:first');

$('#button').on('click', function() {
    if (ajax) {
        ajax.abort();
    }

    ajax = $.ajax({
        url: location.href,
        type: 'post',
        dataType: 'json',
        data: {
            source: source.val(),
            change: change.val()
        },
        success: function(res) {
            if (!res) {
                var alert = '<div class="alert alert-warning">No Result</div>';
                out_source.html(alert);
                out_change.html(alert);
            } else {
                if ('source' in res) {
                    out_source.html(res.source);
                } else {
                    out_source.empty();
                }

                if (res.change) {
                    out_change.html(res.change);
                } else {
                    out_change.empty();
                }

                // レスポンスデータ表示部分
                // if (res.data) {
                //     out_data.show();
                //     data.html(res.data);
                // } else {
                //     out_data.hide();
                // }
            }
        },
        error: function(res) {
            var alert = '<div class="alert alert-danger">' + res.responseText + '</div>';
            out_source.html(alert);
            out_change.html(alert);
        }
    });
});
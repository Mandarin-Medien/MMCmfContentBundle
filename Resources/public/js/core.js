function BootUpMmCmfContentBundle()
{
    var $contentNodes = $('.ContentNode');
    var $options = {};

    $contentNodes.mmCmfContentEditor($options);
    $contentNodes.mmCmfContentFieldEditor($options);
    $contentNodes.mmCmfContentStructureEditor($options);
}


$(document).ready(function () {

    BootUpMmCmfContentBundle();
});


var fetchModal = function (url) {
    $.ajax({
        'url': url,
        'method': 'GET',
        'success': function (request) {
            $('body').append(request);
            $('.modal')
                .modal()
                .on('hidden.bs.modal', function () {
                    $(this).remove();
                })
        }
    });
};

var loadForm = function (item) {

    $.ajax({
        'url': item.getAttribute('href'),
        'method': 'GET',
        'success': function (request) {
            $('body').append(request);
            $('.modal')
                .modal()
                .on('hidden.bs.modal', function () {
                    $(this).remove();
                })
        }
    });

};

var submitForm = function (form) {

    var formData = new FormData(form);
    var action = form.getAttribute('action');
    var method = form.getAttribute('method');

    $.ajax({
        'url': action,
        'type': method,
        'data': formData,
        'processData': false,
        'contentType': false,
        'success': function (data) {
            if (data.success = true) {

                var target = $("[data-cmf-id='" + data.data.id + "']");

                $(target).replaceWith(data.data.markup);
                $('.modal').modal('toggle');

                BootUpMmCmfContentBundle();

            }
        }
    });

    return false;
};


var addContentNode = function (markup) {
    $('body').append(markup);

}

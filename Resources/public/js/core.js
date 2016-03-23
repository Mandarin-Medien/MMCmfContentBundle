/**
 * @todo: needs to be refactored to not sit in global scope
 */
var mmFormFieldhandler;
var mmCmfContentEditorIntilized = false;

function BootUpMmCmfContentBundle()
{
    var $contentNodes = $('.ContentNode');

    /**
     * defined in cmf_javascript_vars.html.twig
     */
    var $options = document.mm_cmf_content;

    if(!mmCmfContentEditorIntilized)
        $contentNodes.mmCmfContentEditor($options);

    mmCmfContentEditorIntilized = true;

    $contentNodes.mmCmfContentFieldEditor($options);
    $contentNodes.mmCmfContentStructureEditor($options);

    $('body').mmCmfContentActionBar();
}


$(document).ready(function () {

    if(typeof document.mm_cmf_content != 'undefined') {
        BootUpMmCmfContentBundle();

        // admin form field types
        mmFormFieldhandler = new FormHandler();
        mmFormFieldhandler.init();
    }
});


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
                $('.modal').modal('hide');

                BootUpMmCmfContentBundle();

            }
        }
    });

    return false;
};


var addContentNode = function (markup) {
    $('body').append(markup);
}

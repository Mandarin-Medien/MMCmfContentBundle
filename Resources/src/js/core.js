import jQuery from 'jquery';

require('summernote');
require('dragula');

require('./cmfField/mmCmfFieldString.js');
require("./cmfField/mmCmfFieldWYSIWYG.js");

require("./mmCmfContentEditor.js");
require("./mmCmfContentFieldEditor.js");
require("./mmCmfContentStructureEditor.js");
require("./mmCmfContentActionBar.js");
require("./core.js");

/**
 * @todo: needs to be refactored to not sit in global scope
 */
global.mmCmfContentEditorIntilized = false;

function BootUpMmCmfContentBundle()
{
    const $contentNodes = jQuery('.ContentNode');

    /**
     * defined in cmf_javascript_vars.html.twig
     */
    const $options = document.mm_cmf_content;

    if(!global.mmCmfContentEditorIntilized)
        $contentNodes.mmCmfContentEditor($options);

    global.mmCmfContentEditorIntilized = true;

    $contentNodes.mmCmfContentFieldEditor($options);
    $contentNodes.mmCmfContentStructureEditor($options);

    jQuery('body').mmCmfContentActionBar();
}


jQuery(document).ready(function () {

    if(typeof document.mm_cmf_content !== 'undefined') {
        BootUpMmCmfContentBundle();
    }
});


global.submitForm = function (form) {

    const formData = new FormData(form);
    const action = form.getAttribute('action');
    const method = form.getAttribute('method');

    jQuery.ajax({
        'url': action,
        'type': method,
        'data': formData,
        'processData': false,
        'contentType': false,
        'success': function (data) {
            if (data.success = true) {

                const target = jQuery("[data-cmf-id='" + data.data.id + "']");

                jQuery(target).replaceWith(data.data.markup);
                jQuery('.modal').modal('hide');

                BootUpMmCmfContentBundle();

            }
        }
    });

    return false;
};


global.addContentNode = function (markup) {
    jQuery('body').append(markup);
};

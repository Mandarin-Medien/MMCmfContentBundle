import jQuery from 'jquery';

jQuery.fn.HTMLType = function () {
    return this.each(function () {
        jQuery(this).summernote();
    });
};

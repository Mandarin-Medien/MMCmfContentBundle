jQuery.fn.NodeTreeType = function () {
    return this.each(function () {
        let target = $(this).parent().find('[name="' + $(this).attr('rel') + '"]');
        let root = this;

        let setActive = function (item) {
            $(root).find('.node-tree-item').removeClass('active');
            $(item).addClass('active');
        };

        $(this).find('.node-tree-item').each(function () {

            let id = $(this).data('id');
            let item = this;

            if (id == target.val())
                setActive(item);


            $(this).children('span').on('click', function (e) {

                e.preventDefault();

                // write back to hidden input
                $(target).val(id);

                // set active state
                setActive(item);

            })
        });
    });
};
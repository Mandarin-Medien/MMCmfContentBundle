$.fn.NodeTreeType = function()
{
    return this.each(function()
    {
        var target =  $('[name="'+$(this).attr('rel')+'"]');
        var root = this;

        var setActive = function(item)
        {
            $(root).find('.node-tree-item').removeClass('active');
            $(item).addClass('active');
        }

        $(this).find('.node-tree-item').each(function() {

            var id = $(this).data('id');
            var item = this;

            if(id == target.val()) setActive(item);


            $(this).children('span').on('click', function(e) {

                e.preventDefault();

                // write back to hidden input
                $(target).val(id);

                // set active state
                setActive(item);

            })
        });
    });
};
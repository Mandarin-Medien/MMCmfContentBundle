(function($) {
    $.extend({
        MenuType : new function() {

            var __construct = function (settings) {

                return this.each(function () {

                    var menu = this;
                    var collideInterval;


                    // make things draggable
                    dragula({

                        isContainer: function (el) {
                            return $(el).hasClass('admin-menu-list') || $(el).hasClass('admin-menu-list');
                        },

                        invalid: function (el, handle) {
                            return !$(handle).hasClass('draggable');
                        }
                    }).on('dragend', function () {
                        //removeCollideHandler(collideInterval);

                        $('.admin-menu-item').removeClass('showsublist');

                        updateMenu(menu);
                    }).on('drag', function (el, source) {
                       //bindCollideHandler(collideInterval);

                       setTimeout(function() {$('.admin-menu-item').not('.gu-mirror, .gu-transit').addClass('showsublist')});
                    });


                    $(document).on('click', '.menu-add-item', menu, addItem);
                    $(document).on('click', '.menu-remove-item', menu, removeItem);


                });
            };

            /**
             * adds a new menu entry based on the prototype data property
             * @param e
             */
            var addItem = function (e) {
                e.preventDefault();

                var proto = $(e.currentTarget).data('prototype');

                $(e.data).append(proto);

                updateMenu(e.data);
            };


            /**
             * removes an menu entry from the list
             * @param e
             */
            var removeItem = function (e) {
                e.preventDefault();

                $($(e.currentTarget).parents('.admin-menu-item')[0]).remove();

                updateMenu(e.data);
            };


            /**
             * updates the fielnames and values of the current given menu
             * @param menu
             */
            var updateMenu = function (menu, _menu_field_base, parent) {
                var menu_field_base = typeof _menu_field_base == 'undefined' ? $(menu).data('name') : _menu_field_base + '[' + $(menu).data('name') + ']';

                if (typeof parent == 'undefiend') parent = 1;

                $.each($(menu).children('li'), function (key, item) {

                    // build the field name base
                    var item_field_base = menu_field_base + '[' + key + ']';

                    // update field names
                    $(this).find('input, select').each(function () {
                        var field_name = item_field_base + $(this).attr('name').match(/\[([_\w]+)\]$/)[0];
                        $(this).attr('name', field_name);
                    });

                    // set the position and parent values
                    $(this).find('.position-field').val(key);
                    $(this).find('.parent-field').val(parent);


                    // update submenus recursively
                    var submenu = $(this).children('ul');
                    if (submenu.length) {
                        updateMenu(submenu, item_field_base, key);
                    }
                });
            };


            var bindCollideHandler = function(interval)
            {
                interval = setInterval(function() {

                    var el1 = $('.admin-menu-item.gu-mirror')[0];
                    var check = $('.admin-menu-item').not('.gu-transit, .gu-mirror');

                    $.each(check, function(key, el2) {
                        el1.offsetBottom = el1.offsetTop + 30;
                        el1.offsetRight = el1.offsetLeft + el1.offsetWidth;
                        el2.offsetBottom = el2.offsetTop + el2.offsetHeight;
                        el2.offsetRight = el2.offsetLeft + el2.offsetWidth;

                        if( !((el1.offsetBottom -120 < el2.offsetTop) ||
                            (el1.offsetTop-120 > el2.offsetBottom) ||
                            (el1.offsetRight < el2.offsetLeft) ||
                            (el1.offsetLeft > el2.offsetRight))
                        ) {
                            console.log(el1.offsetHeight, el2.offsetHeight);
                            $(el2).addClass('showsublist');
                        } else {
                            $(el2).removeClass('showsublist');
                        }
                    });
                }, 100);
            };

            var removeCollideHandler = function(interval)
            {
                clearInterval(interval);
                $('.admin-menu-item').removeClass('showsublist');
            };




            // make plugin callable
            $.fn.extend({
                MenuType : __construct
            });
        }
    });
})(jQuery);
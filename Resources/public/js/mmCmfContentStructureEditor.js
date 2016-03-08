/**
 * This jQuery plugin will be the javascript hub for all contentNodeField actions of the MMCmfContentBundle
 */
(function ($) {

    /**
     *
     * @param $contentNode
     * @param $options
     */
    var mmCmfContentStructureEditor = function ($contentNodes, $options) {

        var $this = this;
        this.contentNodes = $contentNodes;
        this.settings = $options;
        this.modalParent = $('body');

        this.initiateDragula($('.ContentNodeChildren'));


        $(this.contentNodes).each(function (key, $contentNode) {
            $this.appendSettingsMenu($($contentNode));
        });
    };

    /**
     * creates CmfContentNode settings gui
     */
    mmCmfContentStructureEditor.prototype.appendSettingsMenu = function ($contentNode) {

        console.log('$contentNode', $contentNode);
        var $this = this;
        var $settings = this.settings;
        /**
         * append to inner box div
         */
        var $box = this.generateSettingsBox($contentNode);

        var $boxInner = $('<div class="inner" />');

        for (var i in $settings.gridSizes) {
            var $selectBox = this.generateColumnSelect($contentNode, $settings.gridSizes[i], $settings.gridCount);
            $boxInner.append($selectBox);
        }

        /**
         * bind change event
         */
        $boxInner.on('change', 'select', function (e) {

            e.preventDefault();
            $this.onChangeGridsystem($contentNode, $boxInner, $box);
        });


        /**
         * append position switch widget
         */
        var $posSwitch = this.generatePositionSwitchControls($contentNode);
        $boxInner.append($posSwitch);


        /**
         * append inner controlls to main menu
         */
        $box.append($boxInner);

        $contentNode.append($box);


        $contentNode.mouseover(function (e) {

            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('hover');
        });
        $contentNode.mouseout(
            function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('hover');
            }
        );

    };


    mmCmfContentStructureEditor.prototype.onChangeGridsystem = function ($contentNode, $boxInner, $box) {

        $box.parent().removeClass(function (index, css) {
            return (css.match(/col-[a-z1-9\-]*/g) || []).join(' ');
        });

        $boxInner.find('select').each(function () {
            $box.parent().addClass($(this).val());
        });

        console.log("mmCmfContentStructureEditor.onChangeGridsystem", $contentNode);

        var $fieldName = 'classes';
        var $cmfId = $contentNode.data('cmf-id');
        var $value = $contentNode.attr('class');

        $(document).trigger('updated.MMCmfContentFieldEditor',
            {
                value: $value,
                name: $fieldName,
                'cmf-id': $cmfId
            }
        );
    };


    /**
     * Returns gui elemets which lets the user move the position of the current ContentNode
     *
     * @returns {*|HTMLElement}
     */
    mmCmfContentStructureEditor.prototype.generatePositionSwitchControls = function ($contentNode) {

        var $this = this;
        /**
         * manual position switcher
         */
        var $posiswitch = $('<div class="posiswitch"><div class="upper"><i class="fa fa-chevron-up"></i></div><div class="downer"><i class="fa fa-chevron-down"></i></div></div>');

        /**
         * bind events
         */
        $posiswitch.on('click', '.upper , .downer', function (e) {

            e.preventDefault();

            var $ele = $(this);

            if ($ele.hasClass('upper')) {
                if ($contentNode.prev().hasClass('ContentNode'))
                    $contentNode.prev().before($contentNode);
            }

            if ($ele.hasClass('downer')) {
                if ($contentNode.next().hasClass('ContentNode'))
                    $contentNode.next().after($contentNode);
            }

            $this.refreshPositions();
        });

        return $posiswitch;
    };

    /**
     * Returns Grid-System-Size selectbox which lets the user change the grid size based on the viewport
     *
     * @returns {*|HTMLElement}
     */
    mmCmfContentStructureEditor.prototype.generateColumnSelect = function ($contentNode, $gridSize, $gridCount) {

        var $select = $('<select />').data('grid-size', $gridSize);

        $select.append('<option></option>');

        for (var i = 1; i <= $gridCount; i++) {

            var $colClass = 'col-' + $gridSize + '-' + i;
            var $option = $('<option value="' + $colClass + '">' + i + '</option>');

            if ($contentNode.hasClass($colClass))
                $option.attr('selected', 'selected');

            $select.append($option);
        }

        var $selectContainer = $('<div class="select-container select-container-' + $gridSize + '" />');
        $selectContainer.append('<label>' + $gridSize.toUpperCase() + '</label>');
        $selectContainer.append($select);

        return $selectContainer;

    };


    mmCmfContentStructureEditor.prototype.loadSettingsForm = function ($url) {

        var $this = this;

        console.log('loadSettingsForm',this);



        if($url != "")
            $.ajax({
                'url': $url,
                'method': 'GET',
                'success': function (request) {
                    $this.modalParent.append(request);
                    $('.modal')
                        .modal()
                        .on('hidden.bs.modal', function () {
                            $(this).remove();
                        });

                    mmFormFieldhandler.init();
                }
            });

    };

    /**
     * Returns the CMF-Settings DOM-Element which lets the user control some ContentNode attributes
     *
     * @returns {*|HTMLElement}
     */
    mmCmfContentStructureEditor.prototype.generateSettingsBox = function ($contentNode) {

        var $this = this;

        var $div = $('<div class="ContentNode-settings">' +
            '<b class="ContentNode-settings-arrows"><i class="fa fa-arrows"></i></b>' +
            '<br>' +
            '</div>');

        $div.on('click', 'b.ContentNode-settings-arrows', function (e) {

            e.preventDefault();

            $(this).parent().toggleClass('open');
            $(this).parent().parent().toggleClass('ContentNode-highlighted');

        });


        // append settings simple form opener
        var $gearButton =  $('<b class="ContentNode-settings-gear"><i class="fa fa-gear"></i></b>');
        $gearButton.click(function(e){

            e.preventDefault();

            var $route = $contentNode.data('cmf-simple-form');

            $this.loadSettingsForm($route);
        });

        $div.prepend($gearButton);



        return $div;
    };

    /**
     * Load Parents of given ContentNodes and initiates Dragula on it
     *
     * @param $elements
     */
    mmCmfContentStructureEditor.prototype.initiateDragula = function ($elements) {

        var $this = this;

        /**
         * declaration of draggable Container should be more complex in the future
         * @type {*|jQuery|HTMLElement}
         */
        var $draggableContainers = new Array();
        var $draggableContainersObjects = $elements;

        $draggableContainersObjects.each(function (k, v) {
            $draggableContainers.push(v);

        });

        console.log('$draggableContainers', $draggableContainers);

        var $draguala = dragula($draggableContainers, {
            ignoreInputTextSelection: true,

            moves: function (el, source, handle, sibling) {

                if (!($(el).hasClass('ContentNode') ))
                    return false;

                return true;
            },

            accepts: function (el, target, source, sibling) {

                if (!($(el).hasClass('ContentNode') || $(el).hasClass('ParagraphContentNode')))
                    return false;

                return true; // elements can be dropped in any of the `containers` by default
            }
        });


        $draguala.on('drag', function (el, source) {

        });

        $draguala.on('dragend', function (el) {


            $this.refreshPositions(el);
        });

    };

    mmCmfContentStructureEditor.prototype.refreshPositions = function ($contentNode) {

        console.log("refreshPositions", $($contentNode).parent());

        $($contentNode).parent().children('.contentNode').each(function (i) {
            console.log('dragend inner ', i, $(this));
            $(this).attr('data-cmf-position', i);
        });
    };

    /**
     * bootstrap jquery plugin
     *
     * @param options
     */
    $.fn.mmCmfContentStructureEditor = function (options) {

        // Establish our default settings
        var settings = $.extend({

            // mmCmfContentStructureEditor
            gridCount: 12,
            gridSizes: ['xs', 'sm', 'md', 'lg']

        }, options);


        new mmCmfContentStructureEditor(this, settings);
    };

}(jQuery));
/**
 * This jQuery plugin will add all GUI functionalities for the MMCmfContentBundle
 */
(function ($) {


    /**
     *
     * @param elements
     * @param options
     * @constructor
     */
    var MmCmfContentController = function (elements, options) {

        console.log('MmCmfContentController::__construct');
        var $this = this;
        this.elements = elements;
        this.settings = options;

        $(document).on('MmCmfContentController.save', function () {
            console.log('trigger MmCmfContentController::prepareJson');
            var $json = $this.prepareJson();

            $this.save($json);
        });

        this.saveContainer = this.createSaveContainer();

        $('body').append(this.saveContainer);
    };

    MmCmfContentController.prototype.createSaveContainer = function () {

        var $saveBtn = $('<div class="cmf-save-container"><div class="cmf-save-btn ' + this.settings.classes.save_btn + '">' + this.settings.lang.save_btn + '</div></div>')
        $saveBtn.on('click','.cmf-save-btn',function(){
            $(document).trigger('MmCmfContentController.save');
        });

        $(document).on('MmCmfContentController.enableSave', function () {
            $saveBtn.addClass('enable');
        });

        $(document).on('MmCmfContentController.disableSave', function () {
            $saveBtn.removeClass('enable');
        });

        return $saveBtn;
    };

    MmCmfContentController.prototype.save = function ($json) {

        console.log('MmCmfContentController::save', $json);
        $.ajax({
                method: "POST",
                url: this.settings.saveRoute,
                data: {nodes: $json}
            })
            .done(function (msg) {
                console.log("Data Saved: ", msg);
            });

    };

    MmCmfContentController.prototype.prepareJson = function () {

        console.log('MmCmfContentController::prepareJson');

        var $json = {};
        this.elements.find('[data-cmf-field]').each(function () {

            var $cmfObj = $(this).parents(".ContentNode");
            var $cmfId = $cmfObj.data('cmf-id');

            if (typeof $json[$cmfId] == "undefined") {
                $json[$cmfId] = {};

                if ($cmfObj.length > 0 && $cmfObj.data('cmf-class'))
                    $json[$cmfId].class = $cmfObj.data('cmf-class');

                if ($cmfObj.length > 0 && $cmfObj.data('cmf-position'))
                    $json[$cmfId].position = $cmfObj.data('cmf-position');

                var $parentCmf = $cmfObj.parents(".ContentNode");

                if ($parentCmf.length > 0 && $parentCmf.data('cmf-id'))
                    $json[$cmfId].parent = $parentCmf.data('cmf-id');

            }

            $json[$cmfId][$(this).data('cmf-field')] = $(this).html();

        });


        return $json;
    };

    /**
     *
     * @param element
     * @param options
     * @constructor
     */
    var MmCmfContentEditor = function (element, options) {

        this.element = $(element)
        this.settings = options;
    };


    /**
     * Returns gui elemets which lets the user move the position of the current ContentNode
     *
     * @returns {*|HTMLElement}
     */
    MmCmfContentEditor.prototype.generatePositionSwitchControls = function () {

        var $contentNode = this.element;

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
        });

        return $posiswitch;
    };

    /**
     * Returns Grid-System-Size selectbox which lets the user change the grid size based on the viewport
     *
     * @returns {*|HTMLElement}
     */
    MmCmfContentEditor.prototype.generateColumnSelect = function ($gridSize, $gridCount) {

        var $select = $('<select />').data('grid-size', $gridSize);

        $select.append('<option></option>');

        for (var i = 1; i <= $gridCount; i++) {

            var $colClass = 'col-' + $gridSize + '-' + i;
            var $option = $('<option value="' + $colClass + '">' + i + '</option>');

            if (this.element.hasClass($colClass))
                $option.attr('selected', 'selected');

            $select.append($option);
        }

        var $selectContainer = $('<div class="select-container select-container-' + $gridSize + '" />');
        $selectContainer.append('<label>' + $gridSize.toUpperCase() + '</label>');
        $selectContainer.append($select);

        return $selectContainer;

    };

    /**
     * Returns the CMF-Settings DOM-Element which lets the user control some ContentNode attributes
     *
     * @returns {*|HTMLElement}
     */
    MmCmfContentEditor.prototype.generateSettingsBox = function () {

        var $div = $('<div class="ContentNode-settings"><b class="ContentNode-settings-gear"><i class="fa fa-gear"></i></b><br></div>');

        $div.on('click', 'b', function (e) {

            e.preventDefault();

            $(this).parent().toggleClass('open');
            $(this).parent().parent().toggleClass('ContentNode-highlighted');

        });

        return $div;
    };

    /**
     * creates CmfContentNode settings gui
     */
    MmCmfContentEditor.prototype.buildGUI = function () {

        var $settings = this.settings;
        var $boxInner = $('<div class="inner" />');

        for (var i in $settings.gridSizes) {
            var $selectBox = this.generateColumnSelect($settings.gridSizes[i], $settings.gridCount);
            $boxInner.append($selectBox);
        }

        /**
         * bind change event
         */
        $boxInner.on('change', 'select', function (e) {

            e.preventDefault();

            $box.parent().removeClass(function (index, css) {
                return (css.match(/col-[a-z1-9\-]*/g) || []).join(' ');
            });

            $boxInner.find('select').each(function () {
                $box.parent().addClass($(this).val());
            });
        });


        /**
         * append position switch widget
         */
        var $posSwitch = this.generatePositionSwitchControls();
        $boxInner.append($posSwitch);

        /**
         * append to inner box div
         */
        var $box = this.generateSettingsBox();
        $box.append($boxInner);

        return $box;

    };

    /**
     * looks for data-cmf-field DOM-Elements and binds contenteditable functionality
     * @param $elements
     */
    function initiateContenteditable($elements) {

        var $dataFields = $($elements).find('[data-cmf-field]');

        /**
         * bin editable transformation
         */
        $dataFields
            .on('dblclick', function () {
                $(this).attr('contenteditable', 'true').focus();
            })
            .on('blur', function () {
                $(this).attr('contenteditable', 'false');
            })
            .on('DOMCharacterDataModified', function () {
                var $parent = $(this).parents('[data-cmf-id]');

                if ($parent.length > 0)
                {
                    $parent.addClass('cmf-changed');
                    $(document).trigger('MmCmfContentController.enableSave');
                }

            });


    }


    /**
     * Load Parents of given ContentNodes and initiates Dragula on it
     *
     * @param $elements
     */
    function initiateDragula($elements) {
        var $draggableContainers = new Array();

        $elements.parent().each(function () {
            if (typeof $(this).attr('class') != "undefined" && $(this).attr('class').indexOf('ContentNode') != -1)
                $draggableContainers.push(this);

        });

        console.log($elements.parent(), $draggableContainers);

        var $draguala = dragula($draggableContainers, {

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
            $(el).css('background-color', 'rgba( 255 , 0 , 255 ,.5)');
        });

        $draguala.on('dragend', function (el) {
            $(el).css('background-color', 'transparent');
        });

    }

    /**
     * bootstrap jquery plugin
     *
     * @param options
     */
    $.fn.mmCmfContentEditor = function (options) {

        // Establish our default settings
        var settings = $.extend({

            // MmCmfContentEditor
            gridCount: 12,
            gridSizes: ['xs', 'sm', 'md', 'lg'],
            lang: {
                save_btn: 'Seite speichern'
            },
            classes:
            {
                save_btn: 'btn btn-primary'
            },

            // MmCmfContentController
            saveRoute: '/app_dev.php/mmcmfcontent/save'

        }, options);


        this.each(function () {

            var $MmCmfContentEditor = new MmCmfContentEditor(this, settings);
            var $frontendEditWidget = $MmCmfContentEditor.buildGUI();

            $(this).append($frontendEditWidget);

        });

        new MmCmfContentController(this, settings);

        initiateContenteditable(this);
        initiateDragula(this);
    };

}(jQuery));
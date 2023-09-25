const dragula = require('dragula');
import FormHandler from './form/FormHandler';

/**
 * This jQuery plugin will be the javascript hub for all contentNodeField actions of the MMCmfContentBundle
 */
(function ($) {

    /**
     *
     * @param $contentNodes
     * @param $options
     */
    const mmCmfContentStructureEditor = function ($contentNodes, $options) {

        const $this = this;
        this.contentNodes = $contentNodes;
        this.settings = $options;
        this.modalParent = jQuery('body');

        this.initiateDragula(jQuery('.ContentNodeChildren.draggable'));


        jQuery(this.contentNodes).each(function (key, _contentNode) {

            const $contentNode = jQuery(_contentNode);

            if (!$contentNode.data('mmCmfContentStructureEditor')) {
                $this.appendSettingsMenu($contentNode);
                $contentNode.data('mmCmfContentStructureEditor', $this);
            }
        });

        if (typeof jQuery.fn.tooltip != "undefined")
            jQuery('.ContentNode-settings').tooltip({html: true, placement: 'auto top'});
    };

    mmCmfContentStructureEditor.prototype.isGridable = function ($contentNode) {

        const $this = this;

        return ($this.settings.not_gridable_classes.indexOf($contentNode.data('cmf-class')) === -1);

    };


    /**
     * creates CmfContentNode settings gui
     */
    mmCmfContentStructureEditor.prototype.appendSettingsMenu = function ($contentNode) {

        let $this = this;
        let $settings = this.settings;


        /**
         * append to inner box div
         */
        let $box = this.generateSettingsBox($contentNode);
        let $boxInner = jQuery('<div class="inner" />');

        /**
         * checks if the current contentNode
         */
        if ($this.isGridable($contentNode)) {
            for (let i in $settings.gridSizes) {
                let $selectBox = this.generateColumnSelect($contentNode, $settings.gridSizes[i], $settings.gridCount);
                $boxInner.append($selectBox);
            }
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
        let $posSwitch = this.generatePositionSwitchControls($contentNode);
        $boxInner.append($posSwitch);


        /**
         * append inner controlls to main menu
         */
        $box.append($boxInner);

        $contentNode.append($box);

    };


    mmCmfContentStructureEditor.prototype.onChangeGridsystem = function ($contentNode, $boxInner, $box) {

        $box.parent().removeClass(function (index, css) {
            return (css.match(/col-[a-z0-9\-]*/g) || []).join(' ');
        });

        $boxInner.find('select').each(function () {
            $box.parent().addClass(jQuery(this).val());
        });

        let $fieldName = 'classes';
        let $value = $contentNode.attr('class');

        let $cmfId = $contentNode.data('cmf-id');

        let $cmfForbiddenClasses = $contentNode.data('cmf-css-generated-classes') +
            " " + this.settings.highlightClass +
            " " + this.settings.hoverClass;

        let $cmfForbiddenClassesArray = $cmfForbiddenClasses.split(" ");

        for (let i = 0; i < $cmfForbiddenClassesArray.length; i++) {
            $value = $value.replace($cmfForbiddenClassesArray[i], '');
        }

        jQuery(document).trigger('updated.MMCmfContentFieldEditor',
            {
                value: $value.trim(),
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

        let $this = this;
        /**
         * manual position switcher
         */
        let $posiswitch = jQuery('<div class="posiswitch"><div class="upper"><i class="fa fa-chevron-up"></i></div><div class="downer"><i class="fa fa-chevron-down"></i></div></div>');

        /**
         * bind events
         */
        $posiswitch.on('click', '.upper , .downer', function (e) {

            e.preventDefault();

            let $ele = jQuery(this);

            if ($ele.hasClass('upper')) {
                if ($contentNode.prev().hasClass('ContentNode'))
                    $contentNode.prev().before($contentNode);
            }

            if ($ele.hasClass('downer')) {
                if ($contentNode.next().hasClass('ContentNode'))
                    $contentNode.next().after($contentNode);
            }

            $this.refreshPositions($contentNode);
        });

        return $posiswitch;
    };

    /**
     * Returns Grid-System-Size selectbox which lets the user change the grid size based on the viewport
     *
     * @returns {*|HTMLElement}
     */
    mmCmfContentStructureEditor.prototype.generateColumnSelect = function ($contentNode, $gridSize, $gridCount) {

        let $select = jQuery('<select />').data('grid-size', $gridSize);

        $select.append('<option></option>');

        for (let i = 1; i <= $gridCount; i++) {

            let $colClass = 'col-' + $gridSize + '-' + i;
            let $option = jQuery('<option value="' + $colClass + '">' + i + '</option>');

            if ($contentNode.hasClass($colClass))
                $option.attr('selected', 'selected');

            $select.append($option);
        }

        let $selectContainer = jQuery('<div class="select-container select-container-' + $gridSize + '" />');
        $selectContainer.append('<label class="icon-' + $gridSize.toLowerCase() + '"><span>' + $gridSize.toUpperCase() + '</span></label>');
        $selectContainer.append($select);

        return $selectContainer;

    };

    /**
     * loads the contentNode Modal
     *
     * @param $url
     * @param __callback
     */
    mmCmfContentStructureEditor.prototype.loadSettingsForm = function ($url, __callback) {

        let $this = this;

        if ($url != "")
            jQuery.ajax({
                'url': $url,
                'method': 'GET',
                'data': {'root_node': $this.settings.root_node},
                'success': function (request) {


                    let $modal = jQuery(request)
                        .modal()
                        .on('hidden.bs.modal', function () {

                            console.log('modalClose.settingsForm.MMCmfContentFieldEditor', jQuery(this).remove());
                            jQuery(this).remove();

                            jQuery(document).trigger('modalClose.settingsForm.MMCmfContentFieldEditor', {});

                        });

                    $this.modalParent.append($modal);

                    new FormHandler();

                    if (typeof __callback === "function")
                        __callback(request, $modal, $this.modalParent);

                    jQuery(document).trigger('modalOpen.settingsForm.MMCmfContentFieldEditor');

                }
            });

    };

    /**
     * Returns the CMF-Settings DOM-Element which lets the user control some ContentNode attributes
     *
     * @returns {*|HTMLElement}
     */
    mmCmfContentStructureEditor.prototype.generateSettingsBox = function ($contentNode) {

        let $this = this;

        let $title = $contentNode.data('cmf-tooltip');


        let $div = jQuery('<div class="ContentNode-settings" data-toggle="tooltip" title="' + $title + '">' +
            '<b class="ContentNode-settings-arrows"><i class="fa fa-arrows"></i></b>' +
            '<br>' +
            '</div>');

        $div.on('click', 'b.ContentNode-settings-arrows', function (e) {


            e.preventDefault();

            jQuery(this).parent().toggleClass('open');
            jQuery(this).parent().parent().toggleClass($this.settings.highlightClass);

        });


        // append settings simple form opener
        let $gearButton = jQuery('<b class="ContentNode-settings-gear"><i class="fa fa-gear"></i></b>');
        $gearButton.click(function (e) {

            e.preventDefault();

            let $route = $contentNode.data('cmf-simple-form');

            $this.loadSettingsForm($route);
        });

        $div.prepend($gearButton);

        /**
         *
         * append add-children-button simple form opener
         *
         */

        let $addButton = jQuery('<b class="ContentNode-settings-plus"><i class="fa fa-plus"></i></b>');

        $addButton.click(function (e) {

            e.preventDefault();

            let $route = $contentNode.data('cmf-add-child-form');

            $this.loadSettingsForm($route, function (request, $modal, $modalParent) {

                console.log('a.contentNodeType', $modal, $modal.find('a.contentNodeType'));

                $modal.on('click', 'a.contentNodeType', function (e) {

                    e.preventDefault();

                    $this.loadSettingsForm(jQuery(this).attr('href'));

                    $modal.modal('hide');

                });
            });
        });

        $div.prepend($addButton);


        return $div;
    };

    /**
     * Load Parents of given ContentNodes and initiates Dragula on it
     *
     * @param $elements
     */
    mmCmfContentStructureEditor.prototype.initiateDragula = function ($elements) {

        let $this = this;

        /**
         * declaration of draggable Container should be more complex in the future
         * @type {*|jQuery|HTMLElement}
         */
        const $draggableContainers = [];

        $elements.each(function (k, v) {
            $draggableContainers.push(v);
        });

        let $draguala = dragula($draggableContainers, {
            ignoreInputTextSelection: true,
            disableDragAndDrop: true,
            moves: function (el, source, handle, sibling) {

                if (!(jQuery(el).hasClass('ContentNode')))
                    return false;

                return true;
            },

            accepts: function (el, target, source, sibling) {

                if (!(jQuery(el).hasClass('ContentNode') || jQuery(el).hasClass('ParagraphContentNode')))
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

    /**
     * refreshs siblings of changed ContentNode
     *
     * @param $contentNode
     */
    mmCmfContentStructureEditor.prototype.refreshPositions = function ($contentNode) {

        let $this = this;

        jQuery($contentNode).parent().children('.ContentNode').each(function (i) {

            let $contentNodeInner = jQuery(this);
            let prefIndex = $contentNodeInner.attr('data-cmf-position');

            if (i != prefIndex) {
                $contentNodeInner.attr('data-cmf-position', i);

                $this.onObjectPositionChanged($contentNodeInner, i);
            }
        });
    };

    /**
     * triggers update functions to the MainController
     *
     * @param $contentNode
     * @param $value
     */
    mmCmfContentStructureEditor.prototype.onObjectPositionChanged = function ($contentNode, $value) {

        let $cmfId = $contentNode.data('cmf-id');

        jQuery(document).trigger('updated.MMCmfContentFieldEditor',
            {
                value: $value,
                name: 'position',
                'cmf-id': $cmfId
            }
        );

    };


    /**
     * bootstrap jquery plugin
     *
     * @param options
     */
    jQuery.fn.mmCmfContentStructureEditor = function (options) {

        // Establish our default settings
        let settings = jQuery.extend({

            // mmCmfContentStructureEditor
            gridCount: 12,
            gridSizes: ['xs', 'sm', 'md', 'lg'],
            highlightClass: 'ContentNode-highlighted',
            hoverClass: 'hover',
            root_node: null,
            not_gridable_classes: []

        }, options);


        new mmCmfContentStructureEditor(this, settings);
    };

}(jQuery));
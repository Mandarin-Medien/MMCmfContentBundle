/**
 * This jQuery plugin will be the javascript hub for all contentNodeField actions of the MMCmfContentBundle
 */
(function ($) {


    var MMCmfContentEditor = function ($contentNodes, $options) {

        console.log('MMCmfContentEditor::__construct');

        var $this = this;
        this.contentNodes = $contentNodes;
        this.settings = $options;

        this.bindEvents();

        $('body').append(this.getSaveContainer());


    };


    MMCmfContentEditor.prototype.bindEvents = function () {

        var $this = this;

        /**
         * $data : {  MMCmfContentFieldEditor: this, contentNode : this.contentNode }
         */
        $(document).on('hasChanged.MMCmfContentFieldEditor', function ($event, $data) {

            console.log('hasChanged.MMCmfContentFieldEditor', $data);

            $this.showSaveButton();

        });

    };

    MMCmfContentEditor.prototype.save = function () {

        var $this = this;

        $.ajax({
                method: "POST",
                url: this.settings.saveRoute,
                data: {nodes: $this.prepareJson()}
            })
            .done(function (msg) {

                console.log("Data Saved: ", msg);

                $(document).trigger('saved.MMCmfContentFieldEditor');


                $this.hideSaveButton();
            });


    };

    MMCmfContentEditor.prototype.prepareJson = function () {

        var $json = {};

        this.contentNodes.each(function () {

            var $MMCmfContentFieldEditor = $(this).data('MMCmfContentFieldEditor');


            if ($MMCmfContentFieldEditor && $MMCmfContentFieldEditor.hasChanged) {

                var $contentNodeFields = new Object();
                $contentNodeFields.class = $(this).data('cmf-class')

                for (var key in $MMCmfContentFieldEditor.updatedFields) {
                    if ($MMCmfContentFieldEditor.updatedFields.hasOwnProperty(key)) {
                        $contentNodeFields[key] = $MMCmfContentFieldEditor.updatedFields[key];
                    }
                }


                $json[$MMCmfContentFieldEditor.cmfId] = $contentNodeFields;

            }
        });


        /*

         this.elements.find('[data-cmf-field]').each(function () {

         var $cmfObj = $(this).parents(".ContentNode");
         var $cmfId = $cmfObj.data('cmf-id');

         if (typeof $json[$cmfId] == "undefined") {
         $json[$cmfId] = {};

         if ($cmfObj.length > 0 && $cmfObj.data('cmf-class'))
         $json[$cmfId].class = $cmfObj.data('cmf-class');

         if ($cmfObj.length > 0 && typeof $cmfObj.data('cmf-position') != "undefined")
         $json[$cmfId].position = parseInt($cmfObj.data('cmf-position'));

         var $parentCmf = $cmfObj.parents(".ContentNode");

         if ($parentCmf.length > 0 && $parentCmf.data('cmf-id'))
         $json[$cmfId].parent = parseInt($parentCmf.data('cmf-id'));

         }

         $json[$cmfId][$(this).data('cmf-field')] = $(this).html();

         });*/

        console.log($json);
        return $json;
    };

    /**
     *
     */
    MMCmfContentEditor.prototype.hideSaveButton = function () {
        this.getSaveContainer().removeClass('enable');
    };

    /**
     *
     */
    MMCmfContentEditor.prototype.showSaveButton = function () {
        this.getSaveContainer().addClass('enable');
    };

    MMCmfContentEditor.prototype.getSaveContainer = function () {

        var $this = this;

        if (typeof $this._saveBtn == "undefined")
            $this._saveBtn = $(
                '<div class="cmf-save-container">' +
                '<div class="cmf-save-btn ' + this.settings.classes.save_btn + '">' +
                this.settings.lang.save_btn +
                '</div>' +
                '</div>')

            //bind save event
                .on('click', '.cmf-save-btn', function () {
                    $this.save();
                });

        return $this._saveBtn;
    };


    /**
     * bootstrap jquery plugin
     *
     * @param options
     */
    $.fn.mmCmfContentEditor = function (options) {

        // Establish our default settings
        var settings = $.extend({
            lang: {
                save_btn: 'Seite speichern'
            },
            classes: {
                save_btn: 'btn btn-primary'
            },

            // MmCmfContentController
            saveRoute: '/app_dev.php/mmcmfcontent/save'

        }, options);

        new MMCmfContentEditor(this, settings);
    };

}(jQuery));
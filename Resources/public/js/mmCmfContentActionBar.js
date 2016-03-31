/**
 * This jQuery plugin will provide and toolbar for interaction with the CmfContentBundle
 */
(function ($) {

        /**
         *
         * @param $contentNode
         * @param $options
         */
        var mmCmfContentActionBar = function ($parent, $options) {

            this.settings = $options;
            this.parent = $parent;

            this.toolbar = this.generateToolBar();
            this.parent.append(this.toolbar);

        };

        mmCmfContentActionBar.prototype.addMenuEntry = function () {
        };
        mmCmfContentActionBar.prototype.removeMenuEntry = function () {
        };

        mmCmfContentActionBar.prototype.generateToolBar = function () {

            var $toolbar = $('<div class="cmf-content-action-bar" />');
            var $this = this;

            var $editModusToggle = $('<div class="cmf-content-edit-modus-toggle"><i class="fa fa-pencil-square-o"></i></div>');

            $editModusToggle.click(function () {

                var $class = "cmf-content-edit-modus";

                $this.parent.toggleClass($class);

                if ($this.parent.hasClass($class))
                    $(document).trigger('enable.MMCmfContentActionBar');
                else
                    $(document).trigger('disable.MMCmfContentActionBar')


            });

            $toolbar.append($editModusToggle);

            return $toolbar;
        };


        /**
         * bootstrap jquery plugin
         *
         * @param options
         */
        $.fn.mmCmfContentActionBar = function (options) {

            // Establish our default settings
            var settings = $.extend({}, options);


            new mmCmfContentActionBar(this, settings);
        };

    }(jQuery)
);
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

        mmCmfContentActionBar.prototype.addMenuEntry = function(){};
        mmCmfContentActionBar.prototype.removeMenuEntry = function(){};

        mmCmfContentActionBar.prototype.generateToolBar = function() {

            var $toolbar = $('<div class="cmf-content-action-bar" />');
            var $this = this;

            var $editModusToggle = $('<div class="cmf-content-edit-modus-toggle"><i class="fa fa-pencil-square-o">&nbsp;</i></div>');
            $editModusToggle.click(function(){
                $this.parent.toggleClass('cmf-content-edit-modus');
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
            var settings = $.extend({


            }, options);


            new mmCmfContentActionBar(this, settings);
        };

    }(jQuery)
);
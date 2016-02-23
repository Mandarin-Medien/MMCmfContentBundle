(function ($) {


    var MmCmfContentEditor = function (element, options) {

    };

    MmCmfContentEditor.prototype.generatePositionSwitchControls = function () {

        /**
         * man position switcher
         */

        var $posiswitch = $('<div class="posiswitch"><div class="upper"><i class="fa fa-chevron-up"></i></div><div class="downer"><i class="fa fa-chevron-down"></i></div></div>');

        /**
         * bind events
         */
        $posiswitch.on('click', '.upper , .downer', function (e) {

            e.preventDefault();

            var $ele = $(this);
            var $contentNode = $ele.parents('.ContentNode-settings').parent();

            console.log('posiswitch', $ele, $ele.hasClass('upper'));
            if ($ele.hasClass('upper')) {

                $contentNode.prev().before($contentNode);
            }

            if ($ele.hasClass('downer')) {
                $contentNode.next().after($contentNode);
            }
        });

        return $posiswitch;
    };

    MmCmfContentEditor.prototype.generateColumnSelect = function ($gridSize, $gridCount) {
        var $select = $('<select />').data('grid-size', $gridSize);

        for (var i = 1; i <= $gridCount; i++) {

            var $option = $('<option class="col-' + $gridSize + '-' + i + '">' + i + '</option>')
            $select.append($option);
        }

        var $selectContainer = $('<div class="select-container select-container-' + $gridSize + '" />');
        $selectContainer.append('<label>' + $gridSize.toUpperCase() + '</label>');
        $selectContainer.append($select);

        return $selectContainer;

    };

    MmCmfContentEditor.prototype.generateSettingsBox = function () {

        var $div = $('<div class="ContentNode-settings"><b class="ContentNode-settings-gear"><i class="fa fa-gear"></i></b><br></div>');

        $div.on('click', 'b', function (e) {

            e.preventDefault();

            $(this).parent().toggleClass('open');
        });

        return $div;
    };

    MmCmfContentEditor.prototype.buildGUI = function($settings){

        var $boxInner = $('<div class="inner" />');

        for(var i in $settings.gridSizes)
        {
            var $selectBox = this.generateColumnSelect($settings.gridSizes[i],$settings.gridCount);
            $boxInner.append($selectBox);
        }

        var $posSwitch = this.generatePositionSwitchControls();
        $boxInner.append($posSwitch);

        var $box = this.generateSettingsBox();
        $box.append($boxInner);

        return $box;

    };


    $.fn.mmCmfContentEditor = function (options) {

        // Establish our default settings
        var settings = $.extend({
            gridCount: 12,
            gridSizes: ['xs', 'sm', 'md', 'lg']
        }, options);


        this.each(function () {

            var $MmCmfContentEditor = new MmCmfContentEditor();
            var $frontendEditWidget = $MmCmfContentEditor.buildGUI(settings);

            $(this).append($frontendEditWidget);

        });
    }

}(jQuery));
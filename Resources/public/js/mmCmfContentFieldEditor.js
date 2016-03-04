/**
 *
 * @param $contentNode
 * @param $options
 */
var MMCmfContentFieldEditor = function ($contentNode, $options) {


    this.contentNode = $contentNode;
    this.cmfId = this.contentNode.data('cmf-id');
    this.fields = new Array();
    this.updatedFields = new Object();
    this.hasChanged = false;

    // fires all events to the field observer
    this.initiateFieldsOfContentNode($contentNode);

    this.bindEvents();
    this.contentNode.data('MMCmfContentFieldEditor', this);

};

MMCmfContentFieldEditor.prototype.bindEvents = function () {

    var $this = this;

    $(document).on('updated.MMCmfContentFieldEditor', function ($event, $data) {

        if ($data['cmf-id'] == $this.cmfId)
            $this.onFieldUpdate($event, $data);
    });
};

MMCmfContentFieldEditor.prototype.onFieldUpdate = function ($event, $data) {

    this.hasChanged = true;
    this.updatedFields[$data.name] = $data.value;

    console.log('this.updatedFields', $data, this.updatedFields);

    $(document).trigger('hasChanged.MMCmfContentFieldEditor', {
        MMCmfContentFieldEditor: this,
        contentNode: this.contentNode
    });

};


/**
 * returns a groupd array of ContentNode related fields
 */
MMCmfContentFieldEditor.prototype.initiateFieldsOfContentNode = function () {

    var $this = this;


    var $fields = $this.contentNode.find('[data-cmf-field]');

    /**
     * temporary workaround to not get unrelated cmf-fields
     */
    $($fields).each(function () {

        var $tField = $(this);

        if ($this.cmfId == $tField.parents('[data-cmf-id]').data('cmf-id'))
            $this.fields.push($(this));

    });


    /**
     * trigger boot boot events for all cmf-fields
     */

    var $mainEventName = 'init.MMCmfContentFieldEditor';

    if ($this.fields.length > 0) {

        $($this.fields).each(function () {

            var $field = $(this);
            var $type = $field.data('cmf-field-type');
            var $fieldName = $field.data('cmf-field');

            $field.data('cmf-id', $this.cmfId);

            var $eventName = $mainEventName;

            if ($type) {
                $eventName = $type + "." + $mainEventName;
            }

            $(document).trigger($eventName, {field: $field, name: $fieldName, type: $type, 'cmf-id': $this.cmfId});

        });
    }

};

/**
 *
 * @returns {Array}
 */
MMCmfContentFieldEditor.prototype.getFieldData = function () {

    var $fieldsData = new Array();

    $($this.fields).each(function () {
        $fieldsData.push($(this));
    });

    return $fieldsData;
};

/**
 * This jQuery plugin will be the javascript hub for all contentNodeField actions of the MMCmfContentBundle
 */
(function ($) {

    /**
     * bootstrap jquery plugin
     *
     * @param options
     */
    $.fn.mmCmfContentFieldEditor = function (options) {

        // Establish our default settings
        var settings = $.extend({
            // data: data
        }, options);


        this.each(function () {
            new MMCmfContentFieldEditor($(this), settings);
        });
    };

}(jQuery));
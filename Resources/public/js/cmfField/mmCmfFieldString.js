
var mmCmfFieldStringPlugin = function () {

    this.fieldType = 'string';

    this.bindEvents();

};

mmCmfFieldStringPlugin.prototype.bindEvents = function () {

    var $this = this;

    $(document).on(this.fieldType + '.init.MMCmfContentFieldEditor', function ($event, $data) {
        $this.onInit($event, $data)
    });
};

mmCmfFieldStringPlugin.prototype.onInit = function ($event, $data) {

    var $this = this;
    var $field = $data.field;

    if (typeof $field != "undefined") {
        $field.data('mmCmfFieldStringPlugin', $this);

        $field
            //activate editing by click
            .on('click', function ($event) {

                console.log("mmCmfField.String.click", $event);

                var $this = $(this);
                var $cssDisplay = $this.css('display');

                $this
                    .attr('contenteditable', 'true')
                    .data('pre-css-display', $cssDisplay)
                    .css('display', 'inline-block').focus();
            })
            // disabele editing if use clicks away
            .on('blur', function () {

                var $this = $(this);
                var $cssDisplay = $(this).data('pre-css-display');

                $this
                    .attr('contenteditable', 'false')
                    .css('display', $cssDisplay);
            })
            // fire event to the FieldEditor if something changed
            .on('DOMCharacterDataModified', function ($event) {
                 $this.onUpdate($field);
            });
    }

};

mmCmfFieldStringPlugin.prototype.getPreparedData = function ($field) {
    return $field.html();
};


mmCmfFieldStringPlugin.prototype.onUpdate = function ($field) {

    var $type = $field.data('cmf-field-type');
    var $fieldName = $field.data('cmf-field');
    var $cmfId = $field.data('cmf-id');

    var $value = $field.data('mmCmfFieldStringPlugin').getPreparedData($field);

    $(document).trigger('updated.MMCmfContentFieldEditor',
        {
            field: $field,
            value: $value,
            name: $fieldName,
            type: $type,
            'cmf-id': $cmfId
        }
    );
};

new mmCmfFieldStringPlugin();
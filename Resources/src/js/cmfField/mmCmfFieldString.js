var mmCmfFieldStringPlugin = function () {

    this.fieldType = 'string';
    this.enabled = false;
    this.bindEvents();

};

mmCmfFieldStringPlugin.prototype.bindEvents = function () {

    var $this = this;

    $(document).on(this.fieldType + '.init.MMCmfContentFieldEditor', function ($event, $data) {
        $this.onInit($event, $data)
    });

    $(document).on('enable.MMCmfContentActionBar', function ($event) {
        $this.enabled = true;
    });

    $(document).on('disable.MMCmfContentActionBar', function ($event) {
        $this.enabled = false;
    });

};

mmCmfFieldStringPlugin.prototype.onInit = function ($event, $data) {

    var $this = this;
    var $field = $data.field;

    if (typeof $field != "undefined") {
        $field.data('mmCmfFieldStringPlugin', $this);

        var pasteTimeoutId;

        $field
        //activate editing by click
            .on('click', function ($event) {

                if($field.data('mmCmfFieldStringPlugin').enabled == false)
                    return;

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
            .on('DOMCharacterDataModified paste', function ($event) {

                $event.stopPropagation();


                clearTimeout(pasteTimeoutId);

                pasteTimeoutId = setTimeout(function () {

                    $field.html($this.cleanHTML($field.html()));

                    $this.onUpdate($field);
                }, 100);


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

mmCmfFieldStringPlugin.prototype.cleanHTML = function (text) {

    console.log('mmCmfFieldStringPlugin:cleanHTML 2');
    return $.htmlClean(text,{
        removeTags: ["basefont", "center", "dir", "font", "frame", "frameset", "iframe", "isindex", "menu", "noframes","span"]
    });
};

new mmCmfFieldStringPlugin();
var mmCmfFieldStringPlugin = function () {

    this.fieldType = 'string';
    this.enabled = false;
    this.bindEvents();

};

mmCmfFieldStringPlugin.prototype.bindEvents = function () {

    const $this = this;

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

    const $this = this;
    let field = $data.field;

    if (typeof field !== "undefined") {
        field.data('mmCmfFieldStringPlugin', $this);

        let pasteTimeoutId;

        field
        //activate editing by click
            .on('click', () => {

                if(field.data('mmCmfFieldStringPlugin').enabled === false)
                    return;

                const $cssDisplay = field.css('display');

                field
                    .attr('contenteditable', 'true')
                    .data('pre-css-display', $cssDisplay)
                    .css('display', 'inline-block').focus();
            })
            // disabele editing if use clicks away
            .on('blur', () => {

                const $cssDisplay = field.data('pre-css-display');

                field
                    .attr('contenteditable', 'false')
                    .css('display', $cssDisplay);
            })

            // fire event to the FieldEditor if something changed
            .on('DOMCharacterDataModified paste', (event) => {

                event.stopPropagation();

                clearTimeout(pasteTimeoutId);

                pasteTimeoutId = setTimeout(() => {
                    //$field.html($this.cleanHTML($field.html()));

                    $this.onUpdate(field);
                }, 100);


            })
        ;
    }

};

mmCmfFieldStringPlugin.prototype.getPreparedData = function (field) {
    return field.html();
};


mmCmfFieldStringPlugin.prototype.onUpdate = function (field) {

    const type = field.data('cmf-field-type');
    const fieldName = field.data('cmf-field');
    const cmfId = field.data('cmf-id');

    const value = field.data('mmCmfFieldStringPlugin').getPreparedData(field);

    $(document).trigger('updated.MMCmfContentFieldEditor',
        {
            field: field,
            value: value,
            name: fieldName,
            type: type,
            'cmf-id': cmfId
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
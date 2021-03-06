var mmCmfFieldWYSIWYGPlugin = function () {

    this.fieldType = 'WYSIWYG';
    this.enabled = false;
    this.bindEvents();

};

mmCmfFieldWYSIWYGPlugin.prototype.bindEvents = function () {

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

mmCmfFieldWYSIWYGPlugin.prototype.onInit = function ($event, $data) {

    var $this = this;
    var $field = $data.field;

    if (typeof $field != "undefined") {
        $field.data('mmCmfFieldWYSIWYGPlugin', $this);

        $field
            .on('click', function (e) {

                if($field.data('mmCmfFieldWYSIWYGPlugin').enabled == false)
                    return;

                e.preventDefault();

                var getChangeTimer;
                var $dom = $(this);
                $dom.summernote({

                    airMode: true,
                    dialogsFade: true,
                    disableDragAndDrop: true,
                    codemirror: {theme: 'monokai'},
                    focus: true,

                    popover: {
                        image: [
                            ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
                            ['float', ['floatLeft', 'floatRight', 'floatNone']],
                            ['remove', ['removeMedia']]
                        ],
                        link: [
                            ['link', ['linkDialogShow', 'unlink']]
                        ],
                        air: [
                            ['color', ['color']],
                            ['font', ['bold', 'underline', 'clear']],
                            ['para', ['ul', 'paragraph']],
                            ['insert', ['link', 'picture', 'video']]
                        ]
                    },

                    callbacks: {
                        onChange: function (contents) {
                            // do new request
                            clearTimeout(getChangeTimer);

                            getChangeTimer = setTimeout(
                                function () {
                                    $this.onUpdate($field);
                                },
                                300);
                        }
                    },
                });


                $(document).one('disable.MMCmfContentActionBar', function ($event) {
                    $dom.summernote('destroy');
                });

            });
    }

};

mmCmfFieldWYSIWYGPlugin.prototype.getPreparedData = function ($field) {

    return $field.summernote('code');
};


mmCmfFieldWYSIWYGPlugin.prototype.onUpdate = function ($field) {

    var $type = $field.data('cmf-field-type');
    var $fieldName = $field.data('cmf-field');
    var $cmfId = $field.data('cmf-id');

    var $value = $field.data('mmCmfFieldWYSIWYGPlugin').getPreparedData($field);

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

new mmCmfFieldWYSIWYGPlugin();
var mmCmfFieldWYSIWYGPlugin = function () {

    this.fieldType = 'WYSIWYG';

    this.bindEvents();

};

mmCmfFieldWYSIWYGPlugin.prototype.bindEvents = function () {

    var $this = this;

    $(document).on(this.fieldType + '.init.MMCmfContentFieldEditor', function ($event, $data) {

        console.log('mmCmfFieldWYSIWYGPlugin.initField');
        $this.onInit($event, $data)
    });
};

mmCmfFieldWYSIWYGPlugin.prototype.onInit = function ($event, $data) {

    var $this = this;
    var $field = $data.field;

    if (typeof $field != "undefined") {
        $field.data('mmCmfFieldWYSIWYGPlugin', $this);

        console.log('mmCmfFieldWYSIWYGPlugin.initField inner', $field);

        $field
            .on('click', function (e) {

                e.preventDefault();

                var getChangeTimer;

                $(this).summernote({

                    airMode: true,
                    dialogsFade: true,
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

            });
    }

};

mmCmfFieldWYSIWYGPlugin.prototype.getPreparedData = function ($field) {

    return $field.summernote('code');
};


mmCmfFieldWYSIWYGPlugin.prototype.onUpdate = function ($field) {

    console.log("mmCmfFieldWYSIWYGPlugin.onUpdate", $field);

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
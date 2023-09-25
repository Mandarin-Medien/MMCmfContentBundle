
require('./HTMLType.js');
require('./NodeTreeType.js');
require('./MenuType.js');

export default class FormHandler
{
    constructor() {
        console.log('jQuery',jQuery.fn);

        jQuery('[data-form-type="html"]').HTMLType();
        jQuery('.node-tree').NodeTreeType();
        jQuery('.admin-menu-list-main').MenuType();
    };
}

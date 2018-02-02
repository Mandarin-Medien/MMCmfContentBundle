var $ = require("jquery");

export default class FormHandler
{
    constructor() {
        $('[data-form-type="html"]').HTMLType();
        $('.node-tree').NodeTreeType();
        $('.admin-menu-list-main').MenuType();
    };
};
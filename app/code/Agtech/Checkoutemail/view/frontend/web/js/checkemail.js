define([
    'ko',
    'jquery',
    'uiComponent',
], function (ko, $,Component) {
    'use strict';
    return Component.extend({
        /* Set your custom component Ko Template here*/
         /* defaults: {
            template: 'My_Module/custom-component'
        },*/
        initialize: function () {
            alert("My Custom Component");
            return this;
        }
    });
});
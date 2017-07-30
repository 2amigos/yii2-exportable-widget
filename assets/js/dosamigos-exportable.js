/**
 * @copyright Copyright (c) 2014-2017 2amigos - https://2amigos.us
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
if (typeof dosamigos === "undefined" || !dosamigos) {
    var dosamigos = {};
}
dosamigos.exportable = (function ($) {
    'use strict';

    var ensureIFrame = function (transport) {
        var id = '#' + transport;
        if (!$(id).length) {
            $('<iframe/>', {name: transport, css: {'display': 'none'}}).appendTo('body');
        }
    };

    var send = function (transport, url, type) {

        ensureIFrame(transport);

        var $type = $('<input/>', {'name': 'type', 'value': type, 'type': 'hidden'}),
            $export = $('<input/>', {'name': 'export', 'value': 1, 'type': 'hidden'}),
            $csrf = $('<input/>', {
                'name': yii.getCsrfParam() || '_csrf',
                'value': yii.getCsrfToken(),
                'type': 'hidden'
            });

        $('<form/>', {'action': url, 'target': transport, 'method': 'post', css: {'display': 'none'}})
            .append($type, $export, $csrf)
            .appendTo('body')
            .submit()
            .remove();
    };

    return {
        registerHandler: function (selector, url, hash) {
            var clickNs = 'click.' + hash,
                transport = 'iframe' + hash;

            $(document)
                .off(clickNs, selector)
                .on(clickNs, selector, function (e) {
                    e.preventDefault();
                    var $this = $(this),
                        type = $this.data('type');

                    send(transport, url, type);
                });
        }
    };
})(jQuery);

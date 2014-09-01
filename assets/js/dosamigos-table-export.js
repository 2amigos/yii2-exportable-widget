/**
 * @copyright Copyright (c) 2014 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

// the semi-colon before the function invocation is a safety
// net against concatenated scripts and/or other plugins
// that are not closed properly.
;
(function ($, window, document, undefined) {

    var pluginName = "tableExport",
        defaults = {
            ignoredColumns: [],     // specifies which columns should be ignored on exportation
            showHeader: true,       // whether to export also the header text
            filename: 'descarga',   // filename
            useDataUri: false,      // whether to use data-uri to export or use server script
            url: '#',               // if useDataUri is false, set this to the url to post form to force download
            cssFile: '',            // this is important when exporting HTML (ie include bootstrap.css),
            htmlContent: false,     // should we get the HTML content of the cell or just its text?
            type: 'csv',            // type of exportation (can be csv, xml, excel, pdf or html)
            // Excel type options
            workSheet: '',          // names the excel worksheet
            // PDF type options
            pdfLeftMargin: 2,       // sets pdf left margin
            pdfTopMargin: 12,       // sets pdf top margin
            pdfTitle: 'PDF Document',       // sets pdf title
            // CSV type options
            columnDelimiter: '","', // sets csv column delimiter
            rowDelimiter: '"\r\n"'  // sets csv row delimiter
        };

    /**
     * Holds supporting types templates and uri calls
     */
    var types = {
        excel: {
            ext: 'xls',
            uri: 'data:application/vnd.ms-excel;base64,',
            tmpl: '<html xmlns:o="urn:schemas-microsoft-com:office:office" ' +
                'xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">' +
                '<head>' +
                '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' +
                '{css}' +
                '<!--[if gte mso 9]>' +
                '<xml>' +
                '<x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>' +
                '<x:Name>{worksheet}</x:Name>' +
                '<x:WorksheetOptions>' +
                '<x:DisplayGridlines/>' +
                '</x:WorksheetOptions>' +
                '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook>' +
                '</xml><![endif]-->' +
                '</head>' +
                '<body><table>{table}</table></body></html>'
        },
        csv: {
            ext: 'csv',
            uri: 'data:application/csv;base64,'
        },
        html: {
            ext: 'html',
            uri: 'data:text/html;base64,',
            tmpl: '<!DOCTYPE html>' +
                '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>' +
                '<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1"/>' +
                '{css}' +
                '<body>' +
                '{data}' +
                '</body>'
        },
        xml: {
            ext: 'xml',
            uri: 'data:application/xml;base64,',
            tmpl: '<?xml version="1.0" encoding="utf-8"?>' +
                '<tabledata>' +
                '<fields>' +
                '{head}' +
                '</fields>' +
                '<data>' +
                '{data}' +
                '</data>' +
                '</tabledata>'
        },
        json: {
            ext: 'json',
            uri: 'data:application/json;filename=data.json;base64,'
        }
    };

    /**
     * Creates an HTML element (fastest way)
     * @param tag the tag name
     * @returns {*|HTMLElement}
     */
    var node = function (tag) {
        return  $(document.createElement(tag));
    };

    /**
     * Returns the html or text of the element
     * @param $el the HTMLElement
     * @param htmlContent whether to return true or false
     * @returns {string|*}
     */
    var parseString = function ($el, htmlContent ) {
        return htmlContent
            ? $el.html().trim()
            : $el.text().trim();
    };

    /**
     * Returns the appropriate tag to insert a style file
     * @param cssFile
     * @returns {string}
     */
    var cssFile = function (cssFile) {
        return cssFile && (cssFile.length &&
            (typeof cssFile == 'string' || cssFile instanceof String))
            ? '<link href="' + cssFile + '" rel="stylesheet">'
            : ''
    };

    /**
     * Forces download
     * @param content the content of the file
     */
    var download = function (content) {

        var type = types[this.options.type],
            filename = (this.options.filename || this._defaults.filename) + '.' + type.ext;

        if (this.options.useDataUri) { // not supported by IE: http://caniuse.com/#feat=datauri
            var uri = type.uri +
                // dosamigos.js makes sure it has appropriate encoder if missing
                window.btoa(content);

            var $a = node('a')
                .attr('href', uri)
                .attr('download', filename)
                .css('display', 'none'); // not fully supported: http://caniuse.com/#feat=download
            $a.appendTo('body')[0].click();
            $a.remove();
        } else {
            var $form = node('form').attr({
                    'action': this.options.url,
                    'method': 'post',
                    'target': '_blank'
                }),
                $csrf = node('input').attr({
                    'type': 'hidden',
                    'name': yii.getCsrfParam() || '_csrf',
                    'value': yii.getCsrfToken() || ''
                }),
                $type = node('input').attr({
                    'type': 'hidden',
                    'name': 'type',
                    'value': type.ext
                }),
                $filename = node('input').attr({
                    'type': 'hidden',
                    'name': 'filename',
                    'value': this.options.filename
                }),
                $content = node('textarea').attr({
                    'name': 'content'
                }).val(content);

            $form.append($csrf, $type, $filename, $content).submit();
        }
    };

    /**
     * Process a column
     * @param columnTags the column tags to filter
     * @param ignored the column indexes to ignore
     * @returns {*|HTMLElement}
     */
    var column = function (columnTags, ignored) {
        var $tr = node('tr');
        $(this).find(columnTags).each(function (index) {
            if (ignored.indexOf(index) == -1) {
                var tag = $(this).prop('tagName').toLowerCase(),
                    $td = node(tag).append($(this).html());
                $tr.append($td);
            }
        });
        return $tr;
    };

    /**
     * Cleans the table of unwanted data
     * @returns {*}
     */
    var clean = function () {

        var $cleaned = node('table').css({'width': '100%'}),
            $thead = node('thead'),
            $tbody = node('tbody'),
            $tfoot = node('tfoot'),
            $table = this.options.table.clone(),
            ignored = this.options.ignoredColumns;

        $table.find('thead').find('tr.filters').remove(); // yii related

        if (!this.options.showHeader && this.options.type != 'xml') {
            this.$table.find('th').remove();
        }

        $table.find('thead').find('tr').each(function () {
            $thead.append(column.call(this, 'th,td', ignored));
        });

        $table.find('tfoot').find('tr').each(function () {
            $tfoot.append(column.call(this, 'td', ignored));
        });

        $table.find('tbody').find('tr').each(function () {
            $tbody.append(column.call(this, 'td', ignored));
        });

        $cleaned.append($thead, $tfoot, $tbody);
        return $cleaned;
    };

    /**
     * Converts the table to array. Used for pdf and json types.
     * @param table the HTMLElement
     * @returns {Array}
     */
    var toArray = function (table) {
        var json = [],
            headers = [];

        for (var i = 0; i < table.rows[0].cells.length; i++) {
            headers[i] = $(table.rows[0].cells[i]).text().toLowerCase().replace(/ /gi, '');
        }
        for (i = 1; i < table.rows.length; i++) {
            var row = table.rows[i],
                data = {};
            for (var j = 0; j < row.cells.length; j++) {
                data[headers[j]] = $(row.cells[j]).text();
            }
            json.push(data);
        }
        return json;
    }

    /**
     * The actual plugin constructor
     * @param element
     * @param options
     * @constructor
     */
    function TableExport(element, options) {
        this.options = $.extend({}, defaults, options);
        this.$table = clean.call(this);
        this.element = element;
        this.$element = $(element);
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    /**
     * Plugin initialization process
     */
    TableExport.prototype.init = function () {
        var self = this;
        this.$element.off('click').on('click', function (e) {
            e.preventDefault();
            if($(this).data('type')) {
                self.options.type = $(this).data('type'); // data-type overrides type
            }
            self.export();
        });
    };

    /**
     * Export trigger
     */
    TableExport.prototype.export = function () {
        this[this.options.type.toLowerCase()]();
    };

    /**
     * Export table to csv type
     */
    TableExport.prototype.csv = function () {
        var $rows = this.$table.find('tr:has(' + (this.options.showHeader ? 'td,th' : 'td') + ')'),
            tmpColDelim = String.fromCharCode(11),
            tmpRowDelim = String.fromCharCode(0),
            colDelim = this.options.columnDelimiter || '","',
            rowDelim = this.options.rowDelimiter || '"\r\n"',
            csv = '"' + $rows.map(function (i, row) {
                var $row = $(row),
                    $cols = $row.find('td,th');
                return $cols.map(function (j, col) {
                    var $col = $(col),
                        text = $col.text();
                    return text.replace('"', '""');
                }).get().join(tmpColDelim);
            }).get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowDelim)
                .split(tmpColDelim).join(colDelim) + '"';

        download.call(this, csv);
    };

    /**
     * Export table to excel
     */
    TableExport.prototype.excel = function () {
        this.$table.find('input').remove();

        var css = cssFile(this.options.cssFile),
            xls = types['excel'].tmpl
                .replace('{css}', css)
                .replace('{worksheet}', this.options.worksheet || Worksheet)
                .replace('{data}', this.$table.wrap('<div></div>').parent().html())
                .replace(/"/g, '\'');

        download.call(this, xls);
    };

    /**
     * Export table to html
     */
    TableExport.prototype.html = function () {
        var css = cssFile(this.options.cssFile),
            html = types['html'].tmpl
                .replace('{css}', css)
                .replace('{data}', this.$table.wrap('<div></div>').parent().html());

        download.call(this, html);
    };

    /**
     * Export to xml
     */
    TableExport.prototype.xml = function () {

        var xmlHead = '',
            xmlData = '',
            rowCount = 1,
            self = this;

        this.$table.find('thead').find('tr').each(function () {
            $(this).find('th,td').each(function () {
                if ($(this).css('display') != 'none') {
                    xmlHead += '<field>' + parseString($(this), self.options.htmlContent) + '</field>'
                }
            });
        });

        this.$table.find('tbody').find('tr').each(function () {
            xmlData += '<row id="' + rowCount + '">';
            var colCount = 0;
            $(this).find('td').each(function () {
                if ($(this).css('display') != 'none') {
                    xmlData += '<column-' + colCount + '>' +
                        parseString($(this), self.options.htmlContent) +
                        '</column-' + colCount + '>';
                }
                colCount++;
            });
            rowCount++;
            xmlData += '</row>';
        });

        xml = types['xml'].tmpl.replace('{head}', xmlHead).replace('{data}', xmlData);

        download.call(this, xml);
    };

    /**
     * Export to pdf
     */
    TableExport.prototype.pdf = function () {

        var table = toArray(this.$table.get(0)),
            doc = new jsPDF('l', 'pt', 'a4', true);

        doc.setProperties({
            title: this.options.pdfTitle,
            author: '2amigOS! TableExport Plugin',
            creator: '2amigOS! Consulting Group LLC'
        });

        doc.table(this.options.pdfLeftMargin, this.options.pdfTopMargin, table, null, {
            autoSize: true,
            printHeaders: true,
            fontSize: 8
        });

        if (this.options.useDataUri) {
            doc.output('dataurlnewwindow');
        } else {
            doc.save(this.options.filename + '.pdf');
        }
    };

    /**
     * Export to json
     */
    TableExport.prototype.json = function () {
        var data = toArray(this.$table.get(0));

        download.call(this, JSON.stringify(data));
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                    new TableExport(this, options));
            }
        });
    }

})(jQuery, window, document);
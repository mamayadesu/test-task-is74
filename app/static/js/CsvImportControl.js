class CsvImportControl extends Control
{
    constructor(el, options) {
        super(el, options);

        this.columnsCount = 0;
        this.firstRowIsHeader = false;
        this.selectsPreviousValues = {};
        this.$selects = this.element.find(".js-select-column");

        if (options.data.length == 1)
        {
            // в таблице всего одна строка. Выключаем опцию "первая строка - заголовок"
            this.element.find("#first_row_is_header").prop("disabled", true);
        }

        options.data.forEach(row => {
            if (row.length > this.columnsCount)
            {
                this.columnsCount = row.length;
            }
        });

        var similarities = {};
        options.data[0].forEach(column => {
            if (typeof similarities[column] == "undefined")
            {
                similarities[column] = true;
            }
            else
            {
                // в "заголовке" есть повторяющиеся столбцы, значит это не может быть заголовком. Выключаем опцию "первая строка - заголовок"
                this.element.find("#first_row_is_header").prop("disabled", true);
            }
        });

        this.renderTable();
        this.resetColumns();
    }

    getPreparedData() {
        if (this.firstRowIsHeader)
        {
            return this.options.data;
        }
        else
        {
            var data = [];
            var header = [];
            for (let i = 1; i <= this.columnsCount; i++)
            {
                header.push("Стробец №" + i);
            }
            data.push(header);
            this.options.data.forEach(row => {
                data.push(row);
            });

            return data;
        }
    }

    renderTable() {
        var html = "";

        var preparedData = this.getPreparedData();

        var tag;
        for (let i = 0; i < preparedData.length; i++) {
            html += "<tr>";
            if (i == 0)
            {
                tag = "th";
            }
            else
            {
                tag = "td";
            }

            preparedData[i].forEach(column => {
                html += "<" + tag + ">" + column + "</" + tag + ">";
            })

            html += "</tr>";
        }

        this.element.find("#csv-table").html(html);
    }

    resetColumns() {
        var data = this.getPreparedData();
        var headers = data[0];

        this.$selects.val("-notset");
        this.$selects.each(function(index, node) {
            var $el = $(node);

            $el.html("<option value=\"-notset\" selected>не задано</option>");
            var k = -1;
            headers.forEach(header => {
                k++;
                $el.append($("<option>", {
                    value: k,
                    text: header
                }));
            });
        });

        this.$selects.each(function(index, node) {
            this.selectsPreviousValues[$(node).prop("id")] = $(node).val();
        }.bind(this));
    }

    getCollectedData() {
        var finalResult = [];

        for (var k in this.selectsPreviousValues) {
            if (this.selectsPreviousValues[k] == "-notset") {
                return false;
            }
        }

        var preparedData = this.getPreparedData();

        var first = true;
        preparedData.forEach(row => {
            if (first)
            {
                first = false;
                return true;
            }

            var newDataRow = {};
            for (var k in this.selectsPreviousValues) {
                var columnName = k.replace("_column", "");
                newDataRow[columnName] = row[this.selectsPreviousValues[k]];
            }
            finalResult.push(newDataRow);
        });

        return finalResult;
    }

    getEvents() {
        return {

            "#first_row_is_header change": function(ev) {
                var el = $(ev.currentTarget);

                this.firstRowIsHeader = el.prop("checked");
                this.renderTable();
                this.resetColumns();
            },

            ".js-select-column change": function(ev) {
                var el = $(ev.currentTarget);

                var elId = el.prop("id");
                var elVal = el.val();

                var self = this;
                this.$selects.each(function(index, node) {
                    var $element = $(node);

                    $element.find("option").each(function(index, option) {
                        if ($(option).prop("value") == self.selectsPreviousValues[elId])
                        {
                            $(option).prop("disabled", false);
                        }

                        if ($(option).prop("value") == elVal && elVal != "-notset" && elId != $element.prop("id"))
                        {
                            $(option).prop("disabled", true);
                        }
                    });
                });

                this.selectsPreviousValues[elId] = elVal;
            },

            "#import_csv click": function(ev) {
                var el = $(ev.currentTarget);

                console.log(this.getCollectedData());
            }
        };
    }
}
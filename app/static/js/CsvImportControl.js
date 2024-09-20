class CsvImportControl extends Control
{
    constructor(el, options) {
        super(el, options);

        this.columnsCount = 0;
        this.firstRowIsHeader = false;

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

    getEvents() {
        return {

            "#first_row_is_header change": function(ev) {
                var el = $(ev.currentTarget);

                this.firstRowIsHeader = el.prop("checked");
                this.renderTable();
            }

        };
    }
}
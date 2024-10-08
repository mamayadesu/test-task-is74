class TariffEditControl extends Control
{
    constructor(el, options)
    {
        super(el, options);

        this.loadedFile = null;

        this.$name = this.element.find('input[name=name]');
        this.$description = this.element.find('textarea[name=description]');
        this.$speed = this.element.find('input[name=speed]');
        this.$price = this.element.find('input[name=price]');
        this.$end = this.element.find('input[name=end]');
        this.$image = this.element.find('input[name=image]');

        this.$end.mask("99.99.9999", { placeholder: "дд.мм.гггг" });
    }

    onSave(response)
    {
        if (!response.success)
        {
            window.alert(response.error);
        }
        else
        {
            window.alert("Тариф сохранён!");
            window.location.href = "/edit/" + response.id;
        }
    }

    getEvents()
    {
        return {

            "button[name=save] click": function(ev) {
                var el = $(ev.currentTarget);

                var isNew = this.options.tariff_id === null;
                var fields = {
                    name: this.$name.val(),
                    description: this.$description.val(),
                    speed: this.$speed.val(),
                    price: this.$price.val(),
                    end: this.$end.val()
                };

                for (var k in fields)
                {
                    if (fields[k].trim().length == 0)
                    {
                        window.alert("Не все поля заполнены");
                        return;
                    }
                }

                if (this.loadedFile === null && isNew)
                {
                    window.alert("Необходимо выбрать изображение");
                    return;
                }

                var formData = new FormData();

                formData.append("name", fields.name);
                formData.append("description", fields.description);
                formData.append("speed", fields.speed);
                formData.append("price", fields.price);
                formData.append("end", fields.end);
                formData.append("image", this.loadedFile === null ? "" : this.loadedFile);

                var tariff_id = this.options.tariff_id;
                if (tariff_id === null)
                {
                    tariff_id = "";
                }

                $.ajax({
                    url: "/rest/save_tariff/" + tariff_id,
                    data: formData,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        this.onSave(JSON.parse(response));
                    }.bind(this),
                    error: function(response) {
                        this.onSave(JSON.parse(response));
                    }.bind(this)
                })
            },

            "input[name=image] change": function(ev) {
                this.loadedFile = ev.target.files[0];
            },

            "#delete_tariff click": function(ev) {
                if (window.confirm("Тариф невозможно будет восстановить. Удалить?"))
                {
                    $.get("/rest/delete_tariff/" + this.options.tariff_id, function(response) {
                        if (response.success)
                        {
                            window.location.href = "/";
                        }
                        else
                        {
                            window.alert(response.error);
                        }
                    }, "json");
                }
            }
        }
    }
}
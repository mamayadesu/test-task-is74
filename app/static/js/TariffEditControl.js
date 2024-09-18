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
    }

    onSave(response)
    {
        console.log(response);
    }

    getEvents()
    {
        return {

            "button[name=save] click": function(ev) {
                var el = $(ev.target);

                if (this.loadedFile === null)
                {
                    alert("Необходимо выбрать изображение");
                    return;
                }

                var formData = new FormData();

                formData.append("name", this.$name.val());
                formData.append("description", this.$description.val());
                formData.append("speed", this.$speed.val());
                formData.append("price", this.$price.val());
                formData.append("end", this.$end.val());
                formData.append("image", this.loadedFile);

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
                        this.onSave(JSON.parse(response.responseText));
                    }.bind(this),
                    error: function(response) {
                        this.onSave(JSON.parse(response.responseText));
                    }.bind(this)
                })
            },

            "input[name=image] change": function(ev) {
                this.loadedFile = ev.target.files[0];
            }

        }
    }
}
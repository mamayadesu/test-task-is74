class TariffsControl extends Control
{
    constructor(el, options)
    {
        super(el, options);

        this.element.find(".js-edit-tariff").show();
        this.element.find(".js-delete-tariff").show();
    }

    getEvents()
    {
        return {

            ".js-delete-tariff click": function(ev) {
                var el = $(ev.currentTarget);

                var id = el.attr("data-tariff-id");

                if (window.confirm("Удалить тариф?"))
                {
                    $.get("/rest/delete_tariff/" + id, function(response) {
                        if (response.success)
                        {
                            window.location.reload();
                            return;
                        }
                        window.alert("Ошибка! " + response.error);
                    }, "json");
                }
            }

        };
    }
}
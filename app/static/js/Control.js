class Control
{
    constructor(el, options) {
        this.element = el;
        this.options = options;
        this.subscribe();
    }

    getEvents()
    {
        return {};
    }

    subscribe()
    {
        var events = this.getEvents();
        for (let k in events)
        {
            var query = k.split(" ");
            this.element.find(query[0]).on(query[1], events[k].bind(this));
        }
    }
}
var TableHeaderManager = Class.create({
initialize: function initialize(headerElt) {
        this.tableHeader = $(headerElt);

        this.homePosn = { x: this.tableHeader.cumulativeOffset()[0], y: this.tableHeader.cumulativeOffset()[1] };
        Event.observe(window, 'scroll', this.handleScroll.bind(this));
        this.handleScroll();
},
handleScroll: function handleScroll() {
        this.scrollOffset = document.viewport.getScrollOffsets().top;
        if (this.scrollOffset > this.homePosn.y) {
                this.tableHeader.style.position = 'fixed';
                this.tableHeader.style.top = 0;
        } else {
                this.tableHeader.style.position = null;
                this.tableHeader.style.top = null;
                this.tableHeader.style.left = null;
        }
}
});

function initTableHeader() {
    var tableHeaders = $$('.fixedHeader');

    if (tableHeaders) {
        for (var i = 0; i < tableHeaders.length; i++) {
            new TableHeaderManager(tableHeaders[i]);
        }
    }
}

Event.observe(window,'load',initTableHeader,false);
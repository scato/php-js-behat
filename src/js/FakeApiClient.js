(function (exports) {
    function FakeApiClient() {
        this.id = 1;
        this.requests = {};
        this.resolvers = {};
        this.rejecters = {};
    }

    FakeApiClient.prototype.call = function (request) {
        var id = this.id++;

        this.requests[id] = request;

        return new Promise((function (resolve, reject) {
            this.resolvers[id] = resolve;
            this.rejecters[id] = reject;
        }).bind(this));
    };

    exports.FakeApiClient = FakeApiClient;
}(window));

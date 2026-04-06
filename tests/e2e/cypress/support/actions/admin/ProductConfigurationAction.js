import AdminAPIClient from "Services/shopware/AdminAPIClient";

export default class ProductConfigurationAction {

    constructor() {
        this.apiClient = new AdminAPIClient();
    }

    setupProducts() {
        // configure plugin
        this._createOutOfStockDummyProduct();
    }

    /**
     * @private
     */
    _createOutOfStockDummyProduct() {
        const data = {
            "write-product": {
                "entity": "product",
                "action": "upsert",
                "payload": [{
                    "name": "test",
                    "productNumber": "random",
                    "stock": 0,
                    "taxId": "db6f3ed762d14b0395a3fd2dc460db42",
                    "price": [
                        {
                            "currencyId": "b7d2554b0ce847cd82f3ac9bd1c0dfca",
                            "gross": 15,
                            "net": 10,
                            "linked": false
                        }
                    ]
                }]
            }
        };

        this.apiClient.post('/_action/sync', data);
    }

    /**
     *
     * @returns {*}
     */
    _clearCache() {
        return this.apiClient.delete('/_action/cache').catch((err) => {
            console.log('Cache could not be cleared')
        });
    }
}

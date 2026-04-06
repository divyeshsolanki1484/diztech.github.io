export default class ListingRepository {

    /**
     *
     * @returns {*}
     */
    getFirstProduct() {
        return cy.get(':nth-child(1) > .card > .card-body > .product-image-wrapper');
    }

    /**
     *
     * @returns {*}
     */
    getSecondProduct() {
        return cy.get(':nth-child(2) > .card > .card-body > .product-image-wrapper');
    }

}

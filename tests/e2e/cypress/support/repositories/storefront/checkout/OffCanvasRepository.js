export default class OffCanvasRepository {

    /**
     *
     * @returns {*}
     */
    getCheckoutButton() {
        return cy.get('.begin-checkout-btn');
    }

    /**
     *
     * @returns {*}
     */
    getCloseButton() {
        return cy.get('.offcanvas-close').first();
    }

}

export default class ViewRepository {

    /**
     *
     * @returns {*}
     */
    getLoginView() {
        return cy.get('#login-view');
    }

    /**
     *
     * @returns {*}
     */
    getAddressView() {
        return cy.get('#address-view');
    }

}

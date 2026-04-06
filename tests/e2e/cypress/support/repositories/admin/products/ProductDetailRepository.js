export default class ProductDetailRepository {
    /**
     *
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getSaveButton() {
        return cy.get('.sw-button-process')
    }
}

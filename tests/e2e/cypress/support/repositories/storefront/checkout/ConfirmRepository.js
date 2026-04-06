export default class ConfirmRepository {
    /**
     *
     * @returns {*}
     */
    getShippingMethods() {
        return cy.get(
            '.zeobv-shipping-method-selection .list-group-item-action',
            { timeout: 10000 }
        )
    }

    /**
     *
     * @returns {*}
     */
    getDeliveryMoments() {
        return cy.get('.zeobv-delivery-moment-selector__slide', {
            timeout: 10000
        })
    }

    /**
     *
     * @returns {*}
     */
    getPaymentMethods() {
        return cy.get(
            '.zeobv-payment-method-selection .list-group-item-action',
            { timeout: 10000 }
        )
    }

    /**
     *
     * @returns {*}
     */
    getTerms() {
        return cy.get('[name="tos"]')
    }

    /**
     *
     * @returns {*}
     */
    getShowMorePaymentButtonsLabel() {
        return cy.get('.confirm-checkout-collapse-trigger-label')
    }

    /**
     *
     * @returns {*}
     */
    getTotalSum() {
        return cy.get(
            'body > main > div > div > div > div > div.checkout-aside > div > div.checkout-aside-summary > div > div > dl > dd.col-5.checkout-aside-summary-value.checkout-aside-summary-total'
        )
    }

    /**
     *
     * @returns {*}
     */
    getSubmitButton() {
        return cy.get('#confirmFormSubmit')
    }
}

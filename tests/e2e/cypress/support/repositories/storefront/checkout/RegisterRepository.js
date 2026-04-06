export default class ConfirmRepository {

    /**
     * @returns {*}
     */
    getEmailField() {
        return cy.get('#user-email');
    }

    /**
     * @returns {*}
     */
    getPasswordField() {
        return cy.get('#user-password');
    }

    /**
     * @returns {*}
     */
    getShippingFirstNameField() {
        return cy.get('[name="shipping-address-first-name"]');
    }

    /**
     * @returns {*}
     */
    getShippingLastNameField() {
        return cy.get('[name="shipping-address-last-name"]');
    }

    /**
     * @returns {*}
     */
    getShippingTitleField() {
        return cy.get('[name="shipping-address-title"]');
    }

    /**
     * @returns {*}
     */
    getBirthdayDayField() {
        return cy.get('[name="birthdayDay"]');
    }

    /**
     * @returns {*}
     */
    getBirthdayMonthField() {
        return cy.get('[name="birthdayMonth"]');
    }

    /**
     * @returns {*}
     */
    getBirthdayYearField() {
        return cy.get('[name="birthdayYear"]');
    }

    /**
     * @returns {*}
     */
    getShippingPhoneNumberField() {
        return cy.get('[name="shipping-address-phone-number"]');
    }

    /**
     * @returns {*}
     */
    getShippingStreetField() {
        return cy.get('[name="shipping-address-street"]');
    }

    /**
     * @returns {*}
     */
    getShippingHouseNumberField() {
        return cy.get('[name="shipping-address-house-number"]');
    }

    /**
     * @returns {*}
     */
    getShippingPostalCodeField() {
        return cy.get('[name="shipping-address-zipcode"]');
    }

    /**
     * @returns {*}
     */
    getShippingCityField() {
        return cy.get('[name="shipping-address-city"]');
    }

    /**
     * @returns {*}
     */
    getShippingStateField() {
        return cy.get('[name="shipping-address-state"]');
    }

    /**
     * @returns {*}
     */
    getShippingCountryField() {
        return cy.get('[name="shipping-address-country"]', {timeout: 15000});
    }

    /**
     * @returns {*}
     */
    getBillingAddressCheckbox() {
        return cy.get('#isDifferentBillingAddress');
    }

    /**
     * @returns {*}
     */
    getPrivacyCheckbox() {
        return cy.get('#isPrivacyTermsAccepted');
    }

    /**
     * @returns {*}
     */
    getBillingFirstNameField() {
        return cy.get('[name="billing-address-first-name"]');
    }

    /**
     * @returns {*}
     */
    getBillingLastNameField() {
        return cy.get('[name="billing-address-last-name"]');
    }

    /**
     * @returns {*}
     */
    getBillingTitleField() {
        return cy.get('[name="billing-address-title"]');
    }

    /**
     * @returns {*}
     */
    getBillingPhoneNumberField() {
        return cy.get('[name="billing-address-phone-number"]');
    }

    /**
     * @returns {*}
     */
    getBillingStreetField() {
        return cy.get('[name="billing-address-street"]');
    }

    /**
     * @returns {*}
     */
    getBillingHouseNumberField() {
        return cy.get('[name="billing-address-house-number"]');
    }

    /**
     * @returns {*}
     */
    getBillingPostalCodeField() {
        return cy.get('[name="billing-address-zipcode"]');
    }

    /**
     * @returns {*}
     */
    getBillingCityField() {
        return cy.get('[name="billing-address-city"]');
    }

    /**
     * @returns {*}
     */
    getBillingStateField() {
        return cy.get('[name="billing-address-state"]');
    }

    /**
     * @returns {*}
     */
    getBillingCountryField() {
        return cy.get('[name="billing-address-country"]', {timeout: 15000});
    }

    /**
     * @returns {*}
     */
    getBusinessCheckbox() {
        return cy.get('[name="business"]');
    }

    /**
     * @returns {*}
     */
    getCompanyNameField() {
        return cy.get('[name="user-company"]');
    }

    /**
     * @returns {*}
     */
    getCompanyVatField() {
        return cy.get('[name="user-vat-id"]');
    }

    /**
     * @returns {*}
     */
    getCompanyIdField() {
        return cy.get('[name="user-company-registration-id"]');
    }

    /**
     * @returns {*}
     */
    getRegisterCustomerAction() {
        return cy.get('#registerCustomerAction');
    }

    /**
     * @returns {*}
     */
    getContinueAsGuestButton() {
        return cy.get('#continue-as-guest-button');
    }

}

export default class RegisterRepository {

    /**
     *
     * @returns {*}
     */
    getAccountType() {
        return cy.get('#accountType');
    }

    /**
     *
     * @returns {*}
     */
    getSalutation() {
        return cy.get('#personalSalutation');
    }

    /**
     *
     * @returns {*}
     */
    getFirstname() {
        return cy.get('#personalFirstName');
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
     *
     * @returns {*}
     */
    getLastname() {
        return cy.get('#personalLastName');
    }

    /**
     *
     * @returns {*}
     */
    getEmail() {
        return cy.get('#personalMail');
    }

    /**
     *
     * @returns {*}
     */
    getPassword() {
        return cy.get('#personalPassword');
    }

    /**
     *
     * @returns {*}
     */
    getCompany() {
        return cy.get('#billingAddresscompany');
    }

    /**
     *
     * @returns {*}
     */
    getDepartment() {
        return cy.get('#billingAddressdepartment');
    }

    /**
     *
     * @returns {*}
     */
    getVatId() {
        return cy.get('#vatIds');
    }

    /**
     *
     * @returns {*}
     */
    getStreet() {
        return cy.get('#billingAddressAddressStreet');
    }

    /**
     *
     * @returns {*}
     */
    getZipcode() {
        return cy.get('#billingAddressAddressZipcode');
    }

    /**
     *
     * @returns {*}
     */
    getCity() {
        return cy.get('#billingAddressAddressCity');
    }

    /**
     *
     * @returns {*}
     */
    getCountry() {
        return cy.get('#billingAddressAddressCountry');
    }

    /**
     *
     * @returns {*}
     */
    getPhoneNumber() {
        return cy.get('#billingAddressAddressPhoneNumber');
    }

    /**
     *
     * @returns {*}
     */
    getState() {
        return cy.get('#billingAddressAddressCountryState');
    }

    /**
     *
     * @returns {*}
     */
    getDataProtectionCheckbox() {
        return cy.get('#acceptedDataProtection');
    }

    /**
     *
     * @returns {*}
     */
    getRegisterButton() {
        return cy.get('.register-submit > .btn');
    }

}

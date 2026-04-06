import OffCanvasRepository from 'Repositories/storefront/checkout/OffCanvasRepository'
import ConfirmRepository from 'Repositories/storefront/checkout/ConfirmRepository'
import RegisterRepository from 'Repositories/storefront/checkout/RegisterRepository'
import TopMenuAction from 'Actions/storefront/navigation/TopMenuAction'
import ListingAction from 'Actions/storefront/products/ListingAction'
import PDPAction from 'Actions/storefront/products/PDPAction'
import ViewRepository from 'Repositories/storefront/checkout/ViewRepository'

const repoOffCanvas = new OffCanvasRepository()
const repoConfirm = new ConfirmRepository()
const repoRegister = new RegisterRepository()
const repoView = new ViewRepository()

const topMenu = new TopMenuAction()
const listing = new ListingAction()
const pdp = new PDPAction()

const SHIPPING_TITLE = ''
const SHIPPING_TITLE_UPDATE = 'Dr.'
const SHIPPING_FIRST_NAME = 'John'
const SHIPPING_FIRST_NAME_UPDATE = 'Johnathan'
const SHIPPING_LAST_NAME = 'Doe'
const SHIPPING_LAST_NAME_UPDATE = 'Doering'
const SHIPPING_PHONE = '0032687654321'
const SHIPPING_PHONE_UPDATE = '0612345678'
const SHIPPING_STREET = 'Doe street'
const SHIPPING_STREET_UPDATE = 'Deostreet'
const SHIPPING_HOUSE_NUMBER = '1-A'
const SHIPPING_HOUSE_NUMBER_UPDATE = '123423 Boven'
const SHIPPING_ZIP_CODE = '11111'
const SHIPPING_ZIP_CODE_UPDATE = '1234 AA'
const SHIPPING_CITY = 'Doe Town'
const SHIPPING_CITY_UPDATE = 'Amsterdam'
const SHIPPING_STATE = 'Berlin'
const SHIPPING_STATE_UPDATE = 'Saarland'
const SHIPPING_COUNTRY = 'Germany'
const SHIPPING_COUNTRY_UPDATE = 'Netherlands'

const COMPANY_NAME = 'Doe Inc.'
const COMPANY_NAME_UPDATE = 'Very long company-name B.V.'
const COMPANY_VAT_ID = 'DE123434434'
const COMPANY_VAT_ID_UPDATE = 'DE123456789'
const COMPANY_REGISTRATION_ID = '123456789'
const COMPANY_REGISTRATION_ID_UPDATE = '123456789'

const BILLING_TITLE = 'Phd.'
const BILLING_TITLE_UPDATE = ''
const BILLING_FIRST_NAME = 'Jane'
const BILLING_FIRST_NAME_UPDATE = 'Annabel'
const BILLING_LAST_NAME = 'Fame Doe'
const BILLING_LAST_NAME_UPDATE = 'de Witte Zwaam'
const BILLING_PHONE = '0633452343'
const BILLING_PHONE_UPDATE = '+31633445545'
const BILLING_STREET = 'Example'
const BILLING_STREET_UPDATE = 'Cornerstreet áéíóöa'
const BILLING_HOUSE_NUMBER = '1 60'
const BILLING_HOUSE_NUMBER_UPDATE = '2323 60-Downstairs'
const BILLING_ZIP_CODE = '12345'
const BILLING_ZIP_CODE_UPDATE = '54342'
const BILLING_CITY = 'Janeville'
const BILLING_CITY_UPDATE = 'New-Mexico'
const BILLING_STATE = 'Berlin'
const BILLING_STATE_UPDATE = 'Lower Saxony'
const BILLING_COUNTRY = 'Germany'
const BILLING_COUNTRY_UPDATE = 'Germany'

export default class CheckoutAction {
    constructor(performUpdate = false) {
        this.performUpdate = performUpdate
    }

    /**
     *
     * @param quantity
     */
    prepareDummyCart(quantity) {
        topMenu.clickOnClothing()

        listing.clickOnFirstProduct()

        pdp.addToCart(quantity)

        this.closeOffCanvas()

        topMenu.clickOnClothing()

        listing.clickOnSecondProduct()

        pdp.addToCart(quantity)
    }

    goToCheckoutInOffCanvas() {
        repoOffCanvas.getCheckoutButton().click({ force: true })
    }

    closeOffCanvas() {
        repoOffCanvas.getCloseButton().click({ force: true })
    }

    enterValidEmail() {
        this.mail = Math.random().toString(36) + '@cypress.de'

        repoRegister.getEmailField().type('{selectall}{backspace}' + this.mail)
    }

    enterValidPassword() {
        repoRegister.getPasswordField().type('{selectall}{backspace}' + 'Test123!!')
    }

    enterValidShippingTitle() {
        const value = this.performUpdate ? SHIPPING_TITLE_UPDATE : SHIPPING_TITLE
        repoRegister.getShippingTitleField().type('{selectall}{backspace}' + value)
    }

    enterValidBirthday() {
        if (!repoRegister.getBirthdayDayField()) {
            return
        }

        repoRegister.getBirthdayDayField().select(1)
        repoRegister.getBirthdayMonthField().select(1)
        repoRegister.getBirthdayYearField().select(1)
    }

    enterValidShippingFirstName() {
        const value = this.performUpdate ? SHIPPING_FIRST_NAME_UPDATE : SHIPPING_FIRST_NAME
        repoRegister.getShippingFirstNameField().type('{selectall}{backspace}' + value)
    }

    enterValidShippingLastName() {
        const value = this.performUpdate ? SHIPPING_LAST_NAME_UPDATE : SHIPPING_LAST_NAME
        repoRegister.getShippingLastNameField().type('{selectall}{backspace}' + value)
    }

    enterValidShippingPhoneNumber() {
        const value = this.performUpdate ? SHIPPING_PHONE_UPDATE : SHIPPING_PHONE
        repoRegister.getShippingPhoneNumberField().type('{selectall}{backspace}' + value)
    }

    enterValidShippingStreet() {
        const value = this.performUpdate ? SHIPPING_STREET_UPDATE : SHIPPING_STREET
        repoRegister.getShippingStreetField().type('{selectall}{backspace}' + value)
    }

    enterValidShippingHouseNumber() {
        const value = this.performUpdate ? SHIPPING_HOUSE_NUMBER_UPDATE : SHIPPING_HOUSE_NUMBER
        repoRegister.getShippingHouseNumberField().type('{selectall}{backspace}' + value)
    }

    enterValidShippingPostalCode() {
        const value = this.performUpdate ? SHIPPING_ZIP_CODE_UPDATE : SHIPPING_ZIP_CODE
        repoRegister.getShippingPostalCodeField().type('{selectall}{backspace}' + value)
    }

    enterValidShippingCity() {
        const value = this.performUpdate ? SHIPPING_CITY_UPDATE : SHIPPING_CITY
        repoRegister.getShippingCityField().type('{selectall}{backspace}' + value)
    }

    pickValidShippingState() {
        const value = this.performUpdate ? SHIPPING_STATE_UPDATE : SHIPPING_STATE
        repoRegister.getShippingStateField().select(value, { timeout: 20000 })
    }

    pickValidShippingCountry() {
        const value = this.performUpdate ? SHIPPING_COUNTRY_UPDATE : SHIPPING_COUNTRY
        repoRegister.getShippingCountryField().select(value, { timeout: 20000 })
    }

    enterValidBillingTitle() {
        const value = this.performUpdate ? BILLING_TITLE_UPDATE : BILLING_TITLE
        repoRegister.getShippingTitleField().type('{selectall}{backspace}' + value)
    }

    enterValidBillingFirstName() {
        const value = this.performUpdate ? BILLING_FIRST_NAME_UPDATE : BILLING_FIRST_NAME
        repoRegister.getBillingFirstNameField().type('{selectall}{backspace}' + value)
    }

    enterValidBillingLastName() {
        const value = this.performUpdate ? BILLING_LAST_NAME_UPDATE : BILLING_LAST_NAME
        repoRegister.getBillingLastNameField().type('{selectall}{backspace}' + value)
    }

    enterValidBillingPhoneNumber() {
        const value = this.performUpdate ? BILLING_PHONE_UPDATE : BILLING_PHONE
        repoRegister.getBillingPhoneNumberField().type('{selectall}{backspace}' + value)
    }

    enterValidBillingStreet() {
        const value = this.performUpdate ? BILLING_STREET_UPDATE : BILLING_STREET
        repoRegister.getBillingStreetField().type('{selectall}{backspace}' + value)
    }

    enterValidBillingHouseNumber() {
        const value = this.performUpdate ? BILLING_HOUSE_NUMBER_UPDATE : BILLING_HOUSE_NUMBER
        repoRegister.getBillingHouseNumberField().type('{selectall}{backspace}' + value)
    }

    enterValidBillingPostalCode() {
        const value = this.performUpdate ? BILLING_ZIP_CODE_UPDATE : BILLING_ZIP_CODE
        repoRegister.getBillingPostalCodeField().type('{selectall}{backspace}' + value)
    }

    enterValidBillingCity() {
        const value = this.performUpdate ? BILLING_CITY_UPDATE : BILLING_CITY
        repoRegister.getBillingCityField().type('{selectall}{backspace}' + value)
    }

    pickValidBillingState() {
        const value = this.performUpdate ? BILLING_STATE_UPDATE : BILLING_STATE
        repoRegister.getBillingStateField().select(value)
    }

    enterValidCompanyName() {
        const value = this.performUpdate ? COMPANY_NAME_UPDATE : COMPANY_NAME
        repoRegister.getCompanyNameField().type('{selectall}{backspace}' + value)
    }

    enterValidCompanyVat() {
        const value = this.performUpdate ? COMPANY_VAT_ID_UPDATE : COMPANY_VAT_ID
        repoRegister.getCompanyVatField().type('{selectall}{backspace}' + value)
    }

    enterValidCompanyId() {
        repoRegister.getCompanyIdField().type('{selectall}{backspace}' + COMPANY_REGISTRATION_ID)
    }

    checkBusinessCheckbox() {
        repoRegister.getBusinessCheckbox().check()
    }

    uncheckBusinessCheckbox() {
        repoRegister.getBusinessCheckbox().uncheck()
    }

    checkBillingAddressCheckbox() {
        repoRegister.getBillingAddressCheckbox().check()
    }

    uncheckBillingAddressCheckbox() {
        repoRegister.getBillingAddressCheckbox().uncheck()
    }

    checkPrivacyCheckbox() {
        repoRegister.getPrivacyCheckbox().check()
    }

    uncheckPrivacyCheckbox() {
        repoRegister.getPrivacyCheckbox().uncheck()
    }

    selectShippingMethod() {
        repoConfirm.getShippingMethods().get('[data-test-id="Express"]').click({ force: true })
    }

    selectDeliveryMoment() {
        repoConfirm.getDeliveryMoments().first().click({ force: true })
    }

    selectPaymentMethod() {
        repoConfirm.getPaymentMethods().get('[data-test-id="Cash on delivery"]').click({ force: true })
    }

    performContinueAsGuestAction() {
        repoRegister.getContinueAsGuestButton().should('not.be.disabled')
        repoRegister.getContinueAsGuestButton().click()

        if (!repoRegister.getContinueAsGuestButton().invoke('attr', 'disabled')) {
            cy.wait(1000)

            repoRegister.getContinueAsGuestButton().click()
        }

        repoView.getLoginView().should('have.class', 'zeobv-checkout-view--completed', {
            timeout: 10000,
        })
        repoView.getAddressView().should('have.class', 'zeobv-checkout-view--active', {
            timeout: 10000,
        })
    }

    performGuestRegistrationAction(withBusiness, withBillingAddress) {
        cy.wait(100) // Wait for validation

        if (this.performUpdate) {
            cy.intercept('POST', '**/store-api/account/change-profile**').as('registerRoute')
        } else {
            cy.intercept('POST', '**/store-api/zeobv-bpc/guest/register**').as('registerRoute')
        }

        repoRegister.getRegisterCustomerAction().click()

        cy.wait('@registerRoute')

        repoView.getAddressView().should('have.class', 'zeobv-checkout-view--completed')

        repoRegister
            .getShippingFirstNameField()
            .should('have.value', this.performUpdate ? SHIPPING_FIRST_NAME_UPDATE : SHIPPING_FIRST_NAME)
        repoRegister
            .getShippingLastNameField()
            .should('have.value', this.performUpdate ? SHIPPING_LAST_NAME_UPDATE : SHIPPING_LAST_NAME)
        repoRegister
            .getShippingPhoneNumberField()
            .should('have.value', this.performUpdate ? SHIPPING_PHONE_UPDATE : SHIPPING_PHONE)
        repoRegister
            .getShippingStreetField()
            .should('have.value', this.performUpdate ? SHIPPING_STREET_UPDATE : SHIPPING_STREET)
        repoRegister
            .getShippingHouseNumberField()
            .should('have.value', this.performUpdate ? SHIPPING_HOUSE_NUMBER_UPDATE : SHIPPING_HOUSE_NUMBER)
        repoRegister
            .getShippingPostalCodeField()
            .should('have.value', this.performUpdate ? SHIPPING_ZIP_CODE_UPDATE : SHIPPING_ZIP_CODE)
        repoRegister
            .getShippingCityField()
            .should('have.value', this.performUpdate ? SHIPPING_CITY_UPDATE : SHIPPING_CITY)

        if (withBusiness) {
            repoRegister.getBusinessCheckbox().should('be.checked')
            repoRegister
                .getCompanyNameField()
                .should('have.value', this.performUpdate ? COMPANY_NAME_UPDATE : COMPANY_NAME)
            repoRegister
                .getCompanyVatField()
                .should('have.value', this.performUpdate ? COMPANY_VAT_ID_UPDATE : COMPANY_VAT_ID)
            // repoRegister.getCompanyIdField().should('have.value', COMPANY_REGISTRATION_ID);
        } else {
            repoRegister.getBusinessCheckbox().should('not.be.checked')
        }

        if (withBillingAddress) {
            repoRegister.getBillingAddressCheckbox().should('be.checked')
            repoRegister
                .getBillingFirstNameField()
                .should('have.value', this.performUpdate ? BILLING_FIRST_NAME_UPDATE : BILLING_FIRST_NAME)
            repoRegister
                .getBillingLastNameField()
                .should('have.value', this.performUpdate ? BILLING_LAST_NAME_UPDATE : BILLING_LAST_NAME)
            repoRegister
                .getBillingPhoneNumberField()
                .should('have.value', this.performUpdate ? BILLING_PHONE_UPDATE : BILLING_PHONE)
            repoRegister
                .getBillingStreetField()
                .should('have.value', this.performUpdate ? BILLING_STREET_UPDATE : BILLING_STREET)
            repoRegister
                .getBillingHouseNumberField()
                .should('have.value', this.performUpdate ? BILLING_HOUSE_NUMBER_UPDATE : BILLING_HOUSE_NUMBER)
            repoRegister
                .getBillingPostalCodeField()
                .should('have.value', this.performUpdate ? BILLING_ZIP_CODE_UPDATE : BILLING_ZIP_CODE)
            repoRegister
                .getBillingCityField()
                .should('have.value', this.performUpdate ? BILLING_CITY_UPDATE : BILLING_CITY)
        } else {
            repoRegister.getBillingAddressCheckbox().should('not.be.checked')
        }
    }

    createNewShippingAddress(isBusiness) {
        cy.get('#shippingAddressModalToggle').click()
        cy.get('#createNewAddressButton').click()

        this.enterValidShippingTitle()
        this.enterValidShippingFirstName()
        this.enterValidShippingLastName()
        this.enterValidShippingPhoneNumber()
        this.enterValidShippingStreet()
        this.enterValidShippingHouseNumber()
        this.enterValidShippingPostalCode()
        this.enterValidShippingCity()
        this.pickValidShippingCountry()
        this.pickValidShippingState()

        if (isBusiness) {
            this.checkBusinessCheckbox()
            this.enterValidCompanyName()
            this.enterValidCompanyVat()
        }

        cy.get('[data-testid=saveAddressAction]').click()

        cy.get('.zeobv-address-card.zeobv-list-group-item--active', {
            timeout: 25000,
        })

        cy.get('.zeobv-address-card.zeobv-list-group-item--active .zeobv-address-card__company').should(
            'contain.text',
            COMPANY_NAME
        )
        cy.get('.zeobv-address-card.zeobv-list-group-item--active .zeobv-address-card__name').should(
            'contain.text',
            `${SHIPPING_FIRST_NAME} ${SHIPPING_LAST_NAME}`
        )
        cy.get('.zeobv-address-card.zeobv-list-group-item--active .zeobv-address-card__street').should(
            'contain.text',
            `${SHIPPING_STREET} ${SHIPPING_HOUSE_NUMBER}`
        )
        cy.get('.zeobv-address-card.zeobv-list-group-item--active .zeobv-address-card__place').should(
            'contain.text',
            `${SHIPPING_ZIP_CODE} ${SHIPPING_CITY}`
        )
        cy.get('.zeobv-address-card.zeobv-list-group-item--active .zeobv-address-card__country').should(
            'contain.text',
            `${SHIPPING_COUNTRY}`
        )
    }

    updateShippingAddress(isBusiness) {
        cy.get('#shippingAddressModalToggle').click()
        cy.get('.zeobv-address-card__menu-toggle').first().click()
        cy.get('.zeobv-address-management__edit-action').first().click({ force: true })

        this.enterValidShippingTitle()
        this.enterValidShippingFirstName()
        this.enterValidShippingLastName()
        this.enterValidShippingPhoneNumber()
        this.enterValidShippingStreet()
        this.enterValidShippingHouseNumber()
        this.enterValidShippingPostalCode()
        this.enterValidShippingCity()
        this.pickValidShippingCountry()

        if (isBusiness) {
            this.checkBusinessCheckbox()
            this.enterValidCompanyName()
            this.enterValidCompanyVat()
        }

        cy.get('[data-testid=saveAddressAction]').click()

        cy.get('.zeobv-address-card.zeobv-list-group-item--active', {
            timeout: 25000,
        })

        // Assertions
        cy.get('.zeobv-address-card.zeobv-list-group-item--active .zeobv-address-card__company').should(
            'contain.text',
            this.performUpdate ? COMPANY_NAME_UPDATE : COMPANY_NAME
        )

        cy.get('.zeobv-address-card.zeobv-list-group-item--active .zeobv-address-card__name').should(
            'contain.text',
            this.performUpdate
                ? `${SHIPPING_FIRST_NAME_UPDATE} ${SHIPPING_LAST_NAME_UPDATE}`
                : `${SHIPPING_FIRST_NAME} ${SHIPPING_LAST_NAME}`
        )

        cy.get('.zeobv-address-card.zeobv-list-group-item--active .zeobv-address-card__street').should(
            'contain.text',
            this.performUpdate
                ? `${SHIPPING_STREET_UPDATE} ${SHIPPING_HOUSE_NUMBER_UPDATE}`
                : `${SHIPPING_STREET} ${SHIPPING_HOUSE_NUMBER}`
        )

        cy.get('.zeobv-address-card.zeobv-list-group-item--active .zeobv-address-card__place').should(
            'contain.text',
            this.performUpdate
                ? `${SHIPPING_ZIP_CODE_UPDATE} ${SHIPPING_CITY_UPDATE}`
                : `${SHIPPING_ZIP_CODE} ${SHIPPING_CITY}`
        )

        cy.get('.zeobv-address-card.zeobv-list-group-item--active .zeobv-address-card__country').should(
            'contain.text',
            this.performUpdate ? `${SHIPPING_COUNTRY_UPDATE}` : `${SHIPPING_COUNTRY}`
        )
    }

    selectDifferentShippingAddress() {
        cy.get('.zeobv-address-card:not(.zeobv-list-group-item--active)').first().click({ force: true })
    }

    /**
     * @returns {*}
     */
    getTotalFromConfirm() {
        return repoConfirm
            .getTotalSum()
            .invoke('text')
            .then((total) => {
                total = total.replace('*', '')
                total = total.replace('€', '')

                return total
            })
    }

    placeOrderOnConfirm() {
        repoConfirm.getTerms().click('left')

        repoConfirm.getSubmitButton().should('not.be.disabled', { timeout: 15000 })
        repoConfirm.getSubmitButton().click()

        cy.url({ timeout: 20000 }).should('include', '/checkout/finish')
    }

    placeOrderOnEdit() {
        cy.get('#confirmOrderForm > .btn').click()
    }

    backToShop() {
        cy.get('.header-minimal-back-to-shop > .btn').click()
    }
}

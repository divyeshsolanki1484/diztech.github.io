import RegisterRepository from 'Repositories/storefront/account/RegisterRepository';

const repo = new RegisterRepository();

export default class RegisterAction {

    constructor(isBusiness) {
        this.isBusiness = isBusiness;
    }

    /**
     *
     * @param email
     * @param password
     */
    doRegister(email, password) {

        cy.visit('/account');

        repo.getAccountType().select(this.isBusiness ? 'commercial' : 'private');
        repo.getSalutation().select(1);

        repo.getFirstname().clear().type('John');
        repo.getLastname().clear().type('Doe');

        if (repo.getBirthdayDayField()) {
            repo.getBirthdayDayField().select(1);
            repo.getBirthdayMonthField().select(10);
            repo.getBirthdayYearField().select(10);
        }

        repo.getEmail().clear().type(email);
        repo.getPassword().clear().type(password);

        if (this.isBusiness) {
            repo.getCompany().clear().type('ZEO B.V.');
            repo.getDepartment().clear().type('Development');
            repo.getVatId().clear().type('NL123456789B01');
        }

        repo.getStreet().clear().type('Vleutenseweg 386');
        repo.getZipcode().clear().type('3532HW');
        repo.getCity().clear().type('Utrecht');

        repo.getCountry().select('Germany');
        repo.getPhoneNumber().clear().type('+31612345678');

        if (repo.getState()) {
            repo.getState().select('Berlin');
        }

        if (repo.getDataProtectionCheckbox()) {
            repo.getDataProtectionCheckbox().check({force: true});
        }

        repo.getRegisterButton().click();
    }

}

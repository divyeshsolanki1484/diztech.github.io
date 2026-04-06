import LoginRepository from 'Repositories/storefront/account/LoginRepository';

const repo = new LoginRepository();

export default class LoginAction {

    /**
     *
     * @param email
     * @param password
     */
    doLogin(email, password) {

        cy.visit('/account');

        repo.getEmail().clear().type(email);
        repo.getPassword().clear().type(password);

        cy.intercept('GET', '**/account').as('accountPage');

        repo.getSubmitButton().click();

        cy.wait('@accountPage');
    }
}


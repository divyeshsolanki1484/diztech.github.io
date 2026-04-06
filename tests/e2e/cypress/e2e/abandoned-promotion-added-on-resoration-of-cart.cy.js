describe('default abandoned promotion discount apply on restoration of cart)', () => {

    it('default abandoned promotion discount should apply on restoration of cart from abandoned remainder email', () => {

        //Abandoned cart restoration link
        cy.visit('http://localhost/zeo/abandonedcart/recover/018c24ade1c6714480c0a318c0abb8d7');

        //If cart is restore through abandoned email link then default abandoned promotion discount will be applicable
        cy.get('.line-item-label').should('include.text', 'Abandoned cart promotion');
    })
})



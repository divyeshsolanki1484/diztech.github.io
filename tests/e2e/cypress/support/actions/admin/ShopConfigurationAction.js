import AdminAPIClient from 'Services/shopware/AdminAPIClient'
import Shopware from 'Services/shopware/Shopware'

const shopware = new Shopware()

export default class ShopConfigurationAction {
    constructor() {
        this.apiClient = new AdminAPIClient()
    }

    setupShop() {
        // configure plugin
        this._configurePlugin()
    }

    /**
     * @private
     */
    _configurePlugin() {
        const data = {
            null: {
                // 'core.loginRegistration.showTitleField' : true,
                // 'core.loginRegistration.showAccountTypeSelection' : true,
                // 'core.loginRegistration.showPhoneNumberField' : true,
                // 'core.loginRegistration.phoneNumberFieldRequired' : true,
                // 'core.loginRegistration.requireDataProtectionCheckbox' : true,
                // 'core.loginRegistration.showBirthdayField' : true,
                // 'core.loginRegistration.birthdayFieldRequired' : true,
                // 'core.cart.showCustomerComment' : true,
            },
        }

        this.apiClient.post('/_action/system-config/batch', data)
    }

    /**
     *
     * @returns {*}
     */
    _clearCache() {
        return this.apiClient.delete('/_action/cache').catch((err) => {
            console.log('Cache could not be cleared')
        })
    }
}

import './page/zeo-abandoned-cart-list'

import deDE from './snippet/de-DE.json'
import nlNL from './snippet/nl-NL.json'
import enGB from './snippet/en-GB.json'

Shopware.Module.register('zeo-abandoned-cart', {
    type: 'plugin',
    name: 'Abandoned cart',
    title: 'zeo-abandoned-cart.general.mainMenuItemLabel',
    description: 'zeo-abandoned-cart.general.mainMenuItemDescription',
    color: '#62ff80',
    icon: 'regular-shopping-cart',

    snippets: {
        'de-DE': deDE,
        'nl-NL': nlNL,
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'zeo-abandoned-cart-list',
            path: 'abandoned-cart/overview'
        }
    },

    navigation: [
        {
            label: 'zeo-abandoned-cart.general.mainMenuItemLabel',
            color: '#62ff80',
            path: 'zeo.abandoned.cart.list',
            icon: 'regular-megaphone',
            parent: 'sw-marketing'
        }
    ]
})

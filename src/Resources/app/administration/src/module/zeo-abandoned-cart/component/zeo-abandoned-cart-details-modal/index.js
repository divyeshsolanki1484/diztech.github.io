import template from './zeo-abandoned-cart-details-modal.html.twig';

const {Component, Mixin} = Shopware;

Component.register('zeo-abandoned-cart-details-modal', {
    template,

    inject: ['repositoryFactory'],

    mixins: [Mixin.getByName('notification'), Mixin.getByName('placeholder')],

    props: ['abandonedCart', 'isLoading', 'onClose'],

    computed: {
        currencyFilter() {
            return Shopware.Filter.getByName('currency')
        },

        items() {
            return Object.values(this.abandonedCart.lineItems)
        },

        getLineItemColumns() {
            const columnDefinitions = [
                {
                    property: 'label',
                    dataIndex: 'label',
                    label: 'zeo-abandoned-cart.detailsModal.columnProductName',
                    allowResize: false,
                    primary: true,
                    inlineEdit: true,
                    width: '200px',
                },
                {
                    property: 'quantity',
                    dataIndex: 'quantity',
                    label: 'zeo-abandoned-cart.detailsModal.columnQuantity',
                    allowResize: false,
                    align: 'right',
                    inlineEdit: true,
                    width: '120px',
                },
                {
                    property: 'unitPrice',
                    dataIndex: 'unitPrice',
                    label: 'zeo-abandoned-cart.detailsModal.columnUnitPriceGross',
                    allowResize: false,
                    align: 'right',
                    inlineEdit: true,
                    width: '120px',
                },
                {
                    property: 'totalPrice',
                    dataIndex: 'totalPrice',
                    label: 'zeo-abandoned-cart.detailsModal.columnTotalPriceGross',
                    allowResize: false,
                    align: 'right',
                    inlineEdit: true,
                    width: '120px',
                },
            ]

            return columnDefinitions
        },
    },

    methods: {
        navigateToProduct(productId) {
            // First close the modal
            this.onClose()

            // Wait a little bit with starting the navigation so the modal can close.
            setTimeout(() => {
                this.$router.push({ name: 'sw.product.detail', params: { id: productId } })
            }, 500)
        },
    },
})

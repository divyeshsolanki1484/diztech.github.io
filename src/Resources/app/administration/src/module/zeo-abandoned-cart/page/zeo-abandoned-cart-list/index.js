import template from './zeo-abandoned-cart-list.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('zeo-abandoned-cart-list', {
    template,

    inject: ['repositoryFactory'],

    mixins: [Mixin.getByName('notification'), Mixin.getByName('listing')],

    data() {
        return {
            entities: null,
            currencies: {},
            sortBy: 'created_at',
            sortDirection: 'DESC',
            naturalSorting: true,
            isLoading: false,
            isBulkLoading: false,
            total: 0,
            abandonedCartDetailsModalOpened: false,
            abandonedCartDetailItems: [],
            abandonedCartDetailCart: null,
        }
    },

    metaInfo() {},

    computed: {
        dateFilter() {
            return Shopware.Filter.getByName('date')
        },

        repository() {
            return this.repositoryFactory.create('zeo_abandoned_cart')
        },

        columns() {
            return this.getColumns()
        },
    },

    methods: {
        getList() {
            this.isLoading = true

            const criteria = new Criteria()

            criteria.setTerm(this.term)
            criteria.addSorting(Criteria.sort('createdAt', this.sortDirection, this.naturalSorting))

            return Promise.all([this.repository.search(criteria, Shopware.Context.api)])
                .then((result) => {
                    const entities = result[0]

                    this.total = entities.total
                    this.entities = entities

                    this.isLoading = false
                    this.selection = {}
                })
                .catch(() => {
                    this.isLoading = false
                })
        },

        updateTotal({ total }) {
            this.total = total
        },

        getColumns() {
            return [
                {
                    property: 'email',
                    dataIndex: 'email',
                    label: this.$t('zeo-abandoned-cart.list.column.email'),
                    allowResize: true,
                    primary: true,
                },
                {
                    property: 'lastMailSendAt',
                    dataIndex: 'lastMailSendAt',
                    label: this.$t('zeo-abandoned-cart.list.column.lastMailSendAt'),
                    allowResize: true,
                },
                {
                    property: 'salesChannelDomain',
                    dataIndex: 'salesChannelDomain',
                    label: this.$t('zeo-abandoned-cart.list.column.salesChannelDomain'),
                    allowResize: true,
                },
                {
                    property: 'salesChannel',
                    dataIndex: 'salesChannel',
                    label: this.$t('zeo-abandoned-cart.list.column.salesChannel'),
                    allowResize: true,
                },
                {
                    property: 'createdAt',
                    dataIndex: 'createdAt',
                    label: this.$t('zeo-abandoned-cart.list.column.createdAt'),
                    allowResize: true,
                },
                {
                    property: 'updatedAt',
                    dataIndex: 'updatedAt',
                    label: this.$t('zeo-abandoned-cart.list.column.updatedAt'),
                    allowResize: true,
                },
            ]
        },

        onOpenDetailModel(item) {
            this.abandonedCartDetailsModalOpened = true
            this.abandonedCartDetailCart = item
            this.abandonedCartDetailItems = item.items
        },

        onCloseAbandonedCartDetailsModal() {
            this.abandonedCartDetailsModalOpened = false
        },
    },
})

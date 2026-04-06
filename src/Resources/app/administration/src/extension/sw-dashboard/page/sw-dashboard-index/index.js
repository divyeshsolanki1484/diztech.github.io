import template from './sw-dashboard-index.html.twig'
import './sw-dashboard-index.scss'

const { Component, Context } = Shopware
const { Criteria } = Shopware.Data

Component.override('sw-dashboard-index', {
    template,

    inject: ['systemConfigApiService', 'repositoryFactory', 'stateStyleDataProviderService'],

    data() {
        return {
            zeobvAbandonedCartConversionSortBy: 'orderNumber',
            zeobvAbandonedCartConversionSortDirection: 'DESC',
            zeobvAbandonedCartOrder: [],
            totalOrderCount: null,
            zeobvAbandonedCartMailConfig: null,
            zeobvAbandonedCartRatio: 0,
        }
    },

    computed: {
        dateFilter() {
            return Shopware.Filter.getByName('date')
        },

        zeobvAbandonedCartGridColumns: function () {
            return [
                {
                    property: 'orderNumber',
                    label: 'sw-order.list.columnOrderNumber',
                    routerLink: 'sw.order.detail',
                    allowResize: true,
                    primary: true,
                },
                {
                    property: 'orderDateTime',
                    dataIndex: 'orderDateTime',
                    label: 'sw-dashboard.todayStats.orderTime',
                    allowResize: true,
                    primary: false,
                },
                {
                    property: 'orderCustomer.firstName',
                    dataIndex: 'orderCustomer.firstName,orderCustomer.lastName',
                    label: 'sw-order.list.columnCustomerName',
                    allowResize: true,
                },
                {
                    property: 'stateMachineState.name',
                    label: 'sw-order.list.columnState',
                    allowResize: true,
                },
                {
                    property: 'amountTotal',
                    label: 'sw-order.list.columnAmount',
                    align: 'right',
                    allowResize: true,
                },
            ]
        },

        orderRepository: function () {
            return this.repositoryFactory.create('order')
        },
    },

    created() {
        this.fetchAbandonedCartOrders()
    },

    methods: {
        fetchAbandonedCartOrders() {
            const criteria = new Criteria(1, 10)
            criteria.addFilter(Criteria.equals('customFields.ZeobvAbandonedCartMail', 1))
            criteria.addSorting(Criteria.sort('orderNumber', 'DESC'))
            criteria.addAssociation('stateMachineState')

            this.orderRepository.search(criteria, Context.api).then((response) => {
                this.zeobvAbandonedCartOrder = response
                this.totalOrderCount = response.total
                this.systemConfigApiService.getValues('ZeobvAbandonedCart.config').then((response) => {
                    this.zeobvAbandonedCartRatio = 0

                    if (this.totalOrderCount < 1 || !response) {
                        return
                    }

                    this.zeobvAbandonedCartMailConfig = response['ZeobvAbandonedCart.config.metricMailsSent'] || 0

                    if (this.zeobvAbandonedCartMailConfig < 1) {
                        return
                    }

                    this.zeobvAbandonedCartRatio = (
                        (this.totalOrderCount / this.zeobvAbandonedCartMailConfig) *
                        100
                    ).toFixed(2)
                })
            })
        },

        getVariantFromOrderState(order) {
            return this.stateStyleDataProviderService.getStyle('order.state', order.stateMachineState.technicalName)
                .variant
        },
    },
})

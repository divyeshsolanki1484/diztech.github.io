import template from './zeobv-abandoned-cart-schedule-grid.html.twig'
import './zeobv-abandoned-cart-schedule-grid.scss'

const { Component, Context, Utils } = Shopware
const { Criteria } = Shopware.Data

Component.register('zeobv-abandoned-cart-schedule-grid', {
    template,

    inject: ['repositoryFactory'],

    data() {
        return {
            isLoading: false,
            rows: [],
            selectedItems: {},
            editable: true
        }
    },

    props: {
        value: {
            type: String,
            required: false,
            default: ''
        }
    },

    created() {
        if (this.value) {
            this.rows = JSON.parse(this.value)
        }
    },

    watch: {
        rows: function (rows) {
            this.$emit(
                'change',
                JSON.stringify(
                    rows.filter((item) => {
                        return !!item.templateId
                    })
                )
            )
        }
    },

    computed: {
        mailTemplateRepository() {
            return this.repositoryFactory.create('mail_template')
        },

        criteria() {
            const criteria = new Criteria()
            criteria.addAssociation('mailTemplateType')

            return criteria
        },

        context() {
            return Context.api
        },

        columns() {
            return [
                {
                    property: 'delay',
                    dataIndex: 'delay',
                    label: 'zeo-abandoned-cart.schedule-grid.delayLabel',
                    allowResize: true,
                    primary: false,
                    inlineEdit: true,
                    width: '200px'
                },
                {
                    property: 'template',
                    dataIndex: 'template',
                    label: 'zeo-abandoned-cart.schedule-grid.templateLabel',
                    allowResize: true,
                    align: 'left',
                    inlineEdit: true,
                    width: '370px'
                }
            ]
        }
    },

    methods: {
        onDeleteSelectedItems() {
            Object.values(this.selectedItems).forEach((itemToDelete) => {
                this.rows = this.rows.filter((item) => {
                    return item.id != itemToDelete.id
                })
            })
            this.onInlineEditSave();
        },

        onTemplateSelected(item, value) {
            item.templateId = value

            const criteria = new Criteria()
            criteria.setIds([value])
            criteria.addAssociation('mailTemplateType')

            return this.mailTemplateRepository
                .search(criteria, this.context)
                .then((mailTemplates) => {
                    if (mailTemplates.length > 0) {
                        item.templateName = this.mailTemplateLabelCallback(
                            mailTemplates[0]
                        )
                    }
                })
        },

        onSelectionChanged(selection) {
            this.selectedItems = selection
        },

        onInlineEditSave() {
            this.$emit(
                'update:value',
                JSON.stringify(
                    this.rows.filter((item) => {
                        return !!item.templateId
                    })
                )
            )
        },

        onAddNewRow() {
            const lastItem =
                this.rows.length > 0 ? this.rows[this.rows.length - 1] : null

            this.rows.push({
                id: Utils.createId(),
                delay: lastItem ? lastItem.delay : 1,
                templateId: null,
                templateName: null
            })
        },

        mailTemplateLabelCallback(item) {
            if (!item) {
                return ''
            }

            return item.translated.subject && item.translated.subject.length > 0
                ? item.translated.subject
                : item.mailTemplateType.name
        }
    }
})

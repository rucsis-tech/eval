import Common from "../js/Common.js";

export default {
    methods: {
        onPageChange(val) {
            this.currentPage = val
        },
        onSort(sortObj) {
            if (!sortObj.order) {
                this.sortDesc = false
            } else if (sortObj.order === 'descending') {
                this.sortDesc = true
            } else if (sortObj.order === 'ascending') {
                this.sortDesc = false
            }
            this.sort = !sortObj.prop ? null : sortObj.prop
        },
        //--------------------------------------------------------------------
        // セルフォーマッタ

        numberFormatter(row, column) {
            if (row[column.property] === 0) return 0;
            return row[column.property] ? Common.numberFormat(row[column.property]) : '';
        },
        dateFormatter(row, column) {
            return Common.dateFormat(row[column.property], 'YYYY-MM-DD')
        },
        reasonFormatter(row, column) {
            let category = '';
            if (row.category === 'content_cancel') category = '特別付与:';
            if (row.category === 'give') category = '付与:';
            if (row.category === 'payment_history') category = '購入:';

            if (row.reason) {
                const reason = JSON.parse(row.reason)
                category += reason.reason;
            } else {
                category += '[商品ID ' + row.item_id + '] ' + row.item_name;
            }
            return category
        },
        qrt(src) {
            return ('"' + src + '"');

        }
    },
    computed: {
        listItem: function () {

            if (!this.gridData) {
                return [];
            }

            if (this.onListItemList) {
                this.onListItemList(this.gridData)
            }

            let data = [].concat(this.gridData)
            // let total = this.total
            if (data) {
                const fix = v => {
                    if (v instanceof Object) {
                        return Object.keys(v).map(k => fix(v[k])).join(' ')
                    }
                    return String(v)
                }

                this.filters.forEach(function (filterItem, i) {
                    data = data.filter(function (item) {
                        let judge
                        if (filterItem.stringFilter) {
                            judge = item[filterItem.field].indexOf(filterItem.stringFilter) !== -1;
                        } else {
                            let a = String(item[filterItem.field]);
                            let b = String(filterItem.filterLabel);
                            judge = (a !== b);
                        }
                        if (filterItem.not) {
                            judge = !judge;
                        }
                        return judge
                    })
                })

                if (this.sort) {
                    const field = this.sort
                    const order = this.sortDesc ? 1 : -1
                    let sortType = null
                    if (this.sortTypes[this.sort]) {
                        sortType = this.sortTypes[this.sort].type;
                        if (sortType === 'num') sortType = 'number'
                        if (sortType === 'str') sortType = 'string'
                        if (sortType === 'date') sortType = 'string'
                        if (sortType === 'datetime') sortType = 'string'
                    }

                    data = data.sort((adata, bdata) => {
                        let a = adata[field] ?? 0
                        let b = bdata[field] ?? 0
                        if (!sortType) {
                            sortType = typeof a
                        }
                        if (sortType === 'number') {

                            if (a === undefined) {
                                a = -0.001
                            } else {
                                a = !a ? 0 : +(String(a).replace(/,/g, ''))
                            }
                            if (b === undefined) {
                                b = -0.001
                            } else {
                                b = !b ? 0 : +(String(b).replace(/,/g, ''))
                            }

                            return (a === b ? 0 : a < b ? 1 : -1) * order
                        } else if (sortType === 'string') {
                            if (!a) a = ''
                            if (!b) b = ''
                            const r = a.localeCompare(b)
                            return r * order
                        } else {
                            return sortType(adata, bdata) * order;
                        }
                    })
                }
                this.total = data.length
                // Apply pagination
                if (this.perPage) {
                    data = data.slice((this.currentPage - 1) * this.perPage, this.currentPage * this.perPage)
                }
            }
            if (this.onListItemListed) {
                this.onListItemListed(data)
            }
            return data
        },


    },
    data() {
        return {
            currentPage: 1,
            perPage: 5,
            total: 0,
            sort: null,
            sortDesc: true,
            onListItemList: null,
            onListItemListed: null,
            filters: [],
        }
    },

}
Ext.define('frigate.model.Log', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'ip',  type: 'string'},
        {name: 'browser',  type: 'string'},
        {name: 'os',  type: 'string'},
        {name: 'url_from',  type: 'string'},
        {name: 'url_to',  type: 'string'},
        {name: 'count',   type: 'int'}
    ]
});

// create the Data Store
var store = Ext.create('Ext.data.Store', {
    model: 'frigate.model.Log',
    autoLoad: true,
    pageSize: 7,
    alias: 'store.logs',

    proxy: {
        type: 'ajax',
        url: 'server/logs-load.php',
        reader: {
            type: 'json',
            rootProperty: 'logs',
            totalProperty: 'total'
        },
        filterParam: 'query'
    }
});

/**
 * This view is an example list of people.
 */
Ext.define('frigate.view.main.List', {
    extend: 'Ext.grid.Panel',
    xtype: 'mainlist',

    requires: [
        'Ext.ux.form.SearchField'
    ],

    store: store,

    title: 'Логи приложения',

    columns: [
        { text: 'IP',  dataIndex: 'ip', sortable: false, width: 120 },
        { text: 'Браузер', dataIndex: 'browser', flex: 1 },
        { text: 'OS', dataIndex: 'os', flex: 1 },
        { text: 'Первый URL', dataIndex: 'url_from', flex: 1, sortable: false },
        { text: 'Последний URL', dataIndex: 'url_to', flex: 1, sortable: false },
        { text: 'Кол-во URL', dataIndex: 'count', sortable: false }
    ],
    viewConfig: {
         emptyText: 'Нет логов для отображения'
    },
    loadMask: true,
    dockedItems: [{
        xtype: 'pagingtoolbar',
        store: store,   // same store GridPanel is using
        dock: 'bottom',
        displayInfo: true,
        emptyMsg:'',
        beforePageText: 'Страница',
        afterPageText: 'из {0}',
        displayMsg: 'Логи {0} - {1} из {2}'
    }, {
            dock: 'top',
            xtype: 'toolbar',
            items: [{
                width: 400,
                fieldLabel: 'IP',
                labelWidth: 50,
                xtype: 'searchfield',
                store: store
            }, '->', {
                xtype: 'component',
                itemId: 'status',
                tpl: 'Matching threads: {count}',
                style: 'margin-right:5px'
            },{
                dock: 'bottom',
                xtype: 'button',
                text : 'Очистить логи',
                listeners: {
                    click: function() {
                        this.disable();
                        var _self = this;
                        Ext.Ajax.request({
                        url: 'server/logs-parser.php?clearOnly=1',
                        params: {
                        },
                        callback: function(response){
                            _self.enable();
                            store.reload();
                        }
                    });
                    }}
            },{
                dock: 'bottom',
                xtype: 'button',
                text : 'Загрузить логи из файлов',
                listeners: {
                    click: function() {
                        this.disable();
                        var _self = this;
                        Ext.Ajax.request({
                        url: 'server/logs-parser.php',
                        params: {
                        },
                        callback: function(response){
                            _self.enable();
                            store.reload();
                        }
                    });
                }}
            }
        ]}
    ]
});

const sensors = window['sensorsDataAnalytic201505'];
sensors.init({
    server_url: 'https://analysis.mdc.ai/sa.gif',//'http://192.168.8.3:5082/sa.gif', // ng地址
    // server_url: 'http://192.168.8.3:5082/sa.gif',//'http://192.168.8.3:5082/sa.gif', // ng地址
    is_track_single_page: false, // 单页面配置，默认开启，若页面中有锚点设计，需要将该配置删除，否则触发锚点会多触发 $pageview 事件
    use_client_time: false,
    show_log: false,
    send_type: 'image',
    max_string_length: 7000, //字符串最大长度
    heatmap: {
        //是否开启点击图，default 表示开启，自动采集 $WebClick 事件，可以设置 'not_collect' 表示关闭。
        clickmap: 'not_collect',
        //是否开启触达图，not_collect 表示关闭，不会自动采集 $WebStay 事件，可以设置 'default' 表示开启。
        scroll_notice_map: 'not_collect'
    },
    preset_properties: {
        //是否采集 $latest_utm 最近一次广告系列相关参数，默认值 true。
        latest_utm:false,
        //是否采集 $latest_traffic_source_type 最近一次流量来源类型，默认值 true。
        latest_traffic_source_type:false,
        //是否采集 $latest_search_keyword 最近一次搜索引擎关键字，默认值 true。
        latest_search_keyword:false,
        //是否采集 $latest_referrer 最近一次前向地址，默认值 true。
        latest_referrer:false,
        //是否采集 $latest_referrer_host 最近一次前向地址，1.14.8 以下版本默认是true，1.14.8 及以上版本默认是 false，需要手动设置为 true 开启。
        latest_referrer_host:false,
        //是否采集 $latest_landing_page 最近一次落地页地址，默认值 false。
        latest_landing_page:false,
        // //是否采集 $url 页面地址作为公共属性，1.16.5 以下版本默认是 false，1.16.5 及以上版本默认是 true。
        // url: true,
        // //是否采集 $title 页面标题作为公共属性，1.16.5 以下版本默认是 false，1.16.5 及以上版本默认是 true。
        // title: true
    }
});
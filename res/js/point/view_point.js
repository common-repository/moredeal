window.onload = function (e) {
    let scrollTop=document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop
    window.addEventListener('scroll',(e)=>{
        scrollTop = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop
    })
    const topTemplate = document.getElementsByClassName('top-template');//样式一
    const offerTemplate = document.getElementsByClassName('offer-template');//样式二
    const itemTemplate = document.getElementsByClassName('item-template');//样式三
    const featureTemplate = document.getElementsByClassName('feature-template');//样式四
    const overallRow = document.getElementsByClassName('overall-row');//样式五
    const compWrapper = document.getElementsByClassName('comp_wrapper');//需要单独处理的样式
    let contentList = [topTemplate,offerTemplate,itemTemplate,featureTemplate,overallRow]
    let blockList = [];let blockLength = [];let dataGlobal = {};let num = 0
    function showRun(dataList){
        let saveData = []
        for(let el = 0;el<dataList.length;el++){
            if (dataList[el]) {
                let dataGlobal = JSON.parse(dataList[el].getAttribute('data-global'))
                if (dataList[el].children) {
                    let topTemplateList = {}
                    topTemplateList.card = {
                        tem: dataGlobal.template,
                        pos: scrollTop+dataList[el].getBoundingClientRect().top
                    },
                        topTemplateList.product = []
                    for (let i = 0; i < dataList[el].children.length; i++) {
                        let dataProduct = JSON.parse(dataList[el].children[i].getAttribute('data-product'))
                        topTemplateList.product.push({
                            cid: dataProduct.category_id,
                            spm: dataProduct.product_code,
                            sid: dataProduct.s_id,
                            tid: dataProduct.trace_id,
                            v_idx: dataProduct.view_id,
                        })
                        dataList[el].children[i].addEventListener("click",(e)=>{
                            if(e.target.nodeName=='A'||e.target.nodeName=='IMG'||(e.target.nodeName=='SPAN'&&e.target.innerHTML=='See Deal')) {
                                let article = {
                                    article: {
                                        pid: dataProduct.post_id,
                                        tit: dataGlobal.post_title,
                                        ty: "query"
                                    },
                                    sqm: dataGlobal.auth_code,
                                    card: {
                                        tem: dataGlobal.template,
                                    },
                                    product: {
                                        cid: dataProduct.category_id,
                                        spm: dataProduct.product_code,
                                        sid: dataProduct.s_id,
                                        tid: dataProduct.trace_id,
                                        v_idx: dataProduct.view_id,
                                        n_name: e.target.nodeName
                                    },
                                    tags: ["click"],
                                    wp_dz: dataGlobal.wp_addr
                                }
                                sensors.track('click_article_product', {
                                    custom: JSON.stringify(article)
                                });
                            }
                        })
                    }
                    saveData.push(topTemplateList)
                }
            }
        }
        return saveData
    }
    //单独处理的样式
    for(let it = 0;it<compWrapper.length;it++){
        if(compWrapper[it]){
            let dataGlobal = JSON.parse(compWrapper[it].getAttribute('data-global'))
            if (compWrapper[it].children){
                for(let dt=0;dt<compWrapper[it].children.length;dt++){
                    compWrapper[it].children[dt].addEventListener("click",(e)=>{
                        if(e.target.nodeName=='A'||e.target.nodeName=='IMG') {
                            let dataProduct
                            if(e.target.nodeName == 'A'){
                                dataProduct = JSON.parse(e.target.getAttribute('data-product'))
                            }else{
                                dataProduct = JSON.parse(e.target.parentNode.getAttribute('data-product'))
                            }
                            let article = {
                                article: {
                                    pid: dataProduct.post_id,
                                    tit: dataGlobal.post_title,
                                    ty: "query"
                                },
                                sqm: dataGlobal.auth_code,
                                card: {
                                    tem: dataGlobal.template,
                                },
                                product: {
                                    cid: dataProduct.category_id,
                                    spm: dataProduct.product_code,
                                    sid: dataProduct.s_id,
                                    tid: dataProduct.trace_id,
                                    v_idx: dataProduct.view_id,
                                    n_name: e.target.nodeName
                                },
                                tags: ["click"],
                                wp_dz: dataGlobal.wp_addr
                            }
                            sensors.track('click_article_product', {
                                custom: JSON.stringify(article)
                            });
                        }
                    })
                }
                let compWrapperList = {}
                compWrapperList.card = {
                    tem: dataGlobal.template,
                    pos: scrollTop+compWrapper[it].getBoundingClientRect().top
                }
                compWrapperList.product = []
                for(let n=1;n<compWrapper[it].children[0].children.length;n++){
                    let dataProduct = JSON.parse(compWrapper[it].children[0].children[n].getAttribute('data-product'))
                    compWrapperList.product.push({
                        cid: dataProduct.category_id,
                        spm: dataProduct.product_code,
                        sid: dataProduct.s_id,
                        tid: dataProduct.trace_id,
                        v_idx: dataProduct.view_id,
                    })
                }
                blockList.push(compWrapperList)
                blockLength.push(compWrapperList.product.length)
            }
        }
    }

    contentList.map(item => {
        const pointData = showRun(item);
        if (pointData.length > 0) {
            blockList.push(...pointData)
            blockLength.push(pointData[0].product.length)
        }

        if (item[0] && item[0].children) {
            dataGlobal = JSON.parse(item[0].getAttribute('data-global'))
        }
    })
    blockLength.map(item => {
        num += item
    })
    let article = {
        article: {
            pid: dataGlobal.post_id,
            tit: dataGlobal.post_title,
        },
        sqm: dataGlobal.auth_code,
        tag:["view"],
        wp_dz: dataGlobal.wp_addr,
        block:blockList,
        card_size:num
    }
    sensors.track('view_article', {
        custom:JSON.stringify(article)
    });
}

//获取筛选项
function selectionOptions(data) {
    return service({
        url: '/product/conditions',
        method: 'post',
        data
    })
}
var productUrl = '/product/search'
//获取表格数据
function productSearch(data) {
    return service({
        url:productUrl,
        method: 'post',
        data
    })
}
//获取表格数据

function shortCode(data) {
    return service({
        url: '/shortcode',
        method: 'post',
        data
    })
}
//获取筛选项
function sortChange(data) {
    return service({
        url: '/product/orderByMeta',
        method: 'post',
        data
    })
}
//获取筛选项
function pageSelectionStrategy(data) {
    return service({
        url: '/product/pageSelectionStrategy',
        method: 'post',
        data
    })
}

//获取类目
function getCategoryList(data) {
    return service({
        url: '/product/categoryList',
        method: 'post',
        data
    })
}

//热搜关键词
function hotSearchKeywordList(data) {
    return service({
        url: '/product/hotSearchKeywordList',
        method: 'post',
        data
    })
}
//热搜关键词
function templateSearch(data) {
    return service({
        url: '/product/template/templateSearch',
        method: 'post',
        data
    })
}
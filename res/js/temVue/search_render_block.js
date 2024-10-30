Vue.prototype.$message = ELEMENT.Message;
const iptContent = Vue.extend({
    template:`
        <div>
          <el-input-number v-model="val1" :controls="false" size="small" :placeholder="min" style="width:100px"></el-input-number>
         -<el-input-number v-model="val2" :controls="false" size="small" :placeholder="max" style="width:100px"></el-input-number>
        </div>`,
    props: {
        value: {
            type: [String,Number],
        },
        max:{
            type:String
        },
        min:{
            type:String
        },
        data: {}
    },
    data() {
        return {
            val1: undefined,
            val2: undefined,
        };
    },
    watch: {
        value(o) {
            if (this.returnBoloer(o)) {
                this.$nextTick(()=>{
                    this.val1 = undefined
                    this.val2 = undefined
                })
            }else{
                if(this.value){
                    let arr =this.value.split(',')
                    this.val1 = this.transi(arr[0])
                    this.val2 = this.transi(arr[1])
                }else{
                    this.$nextTick(()=>{
                        this.val1 = undefined
                        this.val2 = undefined
                    })
                }
            }
        },
        val1(o) {
            if (this.val2!==undefined||this.val1!==undefined) {
                this.$emit("input", `${o==0?o:o?o:''},${this.val2==0?this.val2:this.val2?this.val2:''}`);
            }else{
                if(!this.returnBoloer(this.value)){
                    this.$emit("input", undefined);
                }
            }

        },
        val2(o) {
            if (this.val2!==undefined||this.val1!==undefined) {
                this.$emit("input", `${this.val1==0?this.val1:this.val1?this.val1:''},${o==0?o:o?o:''}`);
            }else{
                if(!this.returnBoloer(this.value)){
                    this.$emit("input", undefined);
                }
            }

        }
    },
    methods: {
        returnBoloer(d) {
            let arr = [];
            this.data.map((item) => {
                arr = [item.value, ...arr];
            });
            return arr.includes(d)
        },
        transi(val){
            if(val == ''){
                return undefined
            }else{
                return JSON.parse(val)
            }
        }
    },
});
const tips = Vue.extend({
    template:`
            <el-tooltip effect="dark" :manual="false" placement="top">
                <div slot="content" v-html="messages">
                    <div>{{messages}}</div>
                </div>
                <span>
                  <i class="el-icon-question" id="tips-icon"></i>
                </span>
            </el-tooltip>
        `,
    props: ['messages']
})
const imagePreview = Vue.extend({
    template:`
            <el-image
                :src="src"
                fit="cover"
                style="width:64px;height:60px"
                :preview-src-list="[maxSrc]"
            >
            <div slot="error" class="image-slot">
                <i class="el-icon-picture-outline"></i>
            </div>
           </el-image>
        `,
    props: {
        src: {
            type: String,
            required: true
        },
        maxSrc: {
            type: String,
        },
        width: {
            type: [Number, String],
            default: ""
        },
        height: {
            type: [Number, String],
            default: ""
        }
    },
})
new Vue({
    el: "#search",
        template: `<div class="templates">
                      <el-input style="width: 400px" v-model="queryParams.title" size="mini" :placeholder="international['title.tips']" />
                      <div class="temSelect">
                      <treeselect :placeholder="international['title.category']" max-height="600" :closeOnSelect="false" :show-count="true" :options="sortingList" v-model="queryParams.categoryIds" :normalizer="normalizer">
                        <div slot="value-label" slot-scope="{ node }">{{ node.raw.nnn }}</div>
                      </treeselect>
                      </div>
                      <el-button size="mini" type="primary" icon="el-icon-search" @click="handleQuery">{{international['title.search']}}</el-button>
                      <el-button size="mini" icon="el-icon-refresh" @click="resetQuery">{{international['title.reset']}}</el-button><br/>
                      <div class="clearfix" style="margin: 5px 0" v-if="flagList[0]=='hotKeywords'||flagList[1]=='hotKeywords'||flagList[2]=='hotKeywords'">
                        <span class="labelStyle">
                            {{international['selection.hotSearchKeyword']}}
                            <el-tooltip effect="dark" :content="international['selection.hotSearchKeyword.tip']" placement="top">
                               <i class="el-icon-question" style="color:#1890ff"></i>
                            </el-tooltip>
                        </span>
                        <el-radio-group size="small" class="radioStyle" v-model="keysWords" @input="keysWordsChange">
                            <el-radio border v-for="(item,index) in keysWordsList" :key="item.keyword" :label="item.keyword">{{item.label}}</el-radio>
                        </el-radio-group>        
                      </div>
                      <div class="clearfix" v-if="flagList[0]=='selectionStrategy'||flagList[1]=='selectionStrategy'||flagList[2]=='selectionStrategy'">
                        <span class="labelStyle">
                            {{international['selection.strategy']}}
                            <el-tooltip effect="dark" :content="international['selection.strategy.tip']" placement="top">
                                <i class="el-icon-question" style="color:#1890ff"></i>
                            </el-tooltip>
                        </span>
                            <el-radio-group size="small" class="radioStyle" v-model="strategy" @input="selStrategy">
                                <el-tooltip v-for="(item,index) in strategyList" :key="index" :disabled="item.des?false:true" placement="top">
                                    <div style="max-width:300px" slot="content">{{item.des}}</div>
                                    <el-radio border :label="item.id">{{item.name}}</el-radio>
                                </el-tooltip>
                            </el-radio-group>       
                      </div> 
                      <el-form
                          :model="queryParams"
                          label-width="auto"
                          ref="queryForm"
                          size="small"
                        >
                        <div style="position: relative" v-if="flagList[0]=='selectionConditions'||flagList[1]=='selectionConditions'||flagList[2]=='selectionConditions'">
                        <el-tabs type="card" @tab-click="btnTabs">
                          <el-tab-pane v-for="i in searchList" :key="i.code">
                            <div slot="label">
                              <span>{{i.name}}</span>
                              <div style="width:21px;display: inline-block;"><el-badge :hidden="i.checked.length==0?true:false" :value="i.checked.length"></el-badge></div>
                            </div>
                            <el-form-item :label-width="align" v-for="item in i.conditions" :key="item.field">
                            <label slot="label">
                              <div>{{item.name}}
                                <el-tooltip effect="dark" :content="item.tip" placement="top">
                                    <i class="el-icon-question" style="color:#1890ff"></i>
                                </el-tooltip>
                              </div>
                            </label>
                            <el-checkbox-group class="radioList" v-model="queryParams[item.field]" v-if="item.type==='SELECT'">
                                <div class="groupList" v-for="(el,index) in item.selections" :key="index">
                                    <el-checkbox @change="(flag)=>checkboxChange(item,el,flag)" border :label="el.value">{{el.label}}</el-checkbox>
                                </div>
                            </el-checkbox-group>
                            <el-radio-group class="radioList" v-model="queryParams[item.field]" v-else>
                              <div class="groupList" v-for="(el,index) in item.selections" :key="index">
                                <el-radio border :label="el.value">{{el.label}}</el-radio>
                              </div>
                            </el-radio-group>
                            <div class="numIpt" v-if="item.type == 'NUMBER_RANG'">
                              <ipt-content v-model="queryParams[item.field]" :data="item.selections" :max="international['other.max']" :min="international['other.min']">{{item.unit}}</ipt-content>
                            </div>
                          </el-form-item>
                          <el-form-item>
                            <div style="display: flex;justify-content: center;margin-top: 20px;">
                              <el-button
                              style="width:105px;height: 28px;color: #ffffff;font-size: 12px;font-weight: 500"
                              type="primary"
                              size="mini"
                              icon="el-icon-search"
                              @click="handleQuery"
                              >
                              {{international['title.search']}}
                              </el-button>
                            <el-button style="width:105px;height: 28px;font-size: 12px;font-weight: 500;" icon="el-icon-refresh" size="mini" @click="resetQuery"
                              >
                              {{international['title.reset']}}
                              </el-button
                            >
                          </div>
                        </el-form-item>
                          </el-tab-pane>                         
                        </el-tabs>
                        </div>
                        </el-form>
                        <div v-if="searchTemplate == 'default' || searchTemplate == 'block_default'" style="margin-top: 30px">
                            <el-table :data="dataList" @sort-change="sortChange" ref="multipleTable">
                            <el-table-column :label="international['table.product']" width="270">
                                <template slot-scope="scope">
                                  <div style="display:flex;justify-content: space-between;font-size:12px">
                                    <div>
                                        <image-preview :src="scope.row.picUrl" :maxSrc="scope.row.img"/>
                                      <div style="margin:32px 0 0 0;width: 81px;"><span style="font-weight: 500;">{{international['other.variant']}}:</span>{{scope.row.subAsinCount||'-'}}</div>
                                    </div>
                                    <div style="text-align:left;font-size:12px">
                                      <el-tooltip effect="dark" placement="top">
                                      <div style="max-width:300px" slot="content">{{scope.row.title}}</div>
                                       <div class="produce" @click.stop="toUrl(scope.row)">{{scope.row.title||'-'}}</div>
                                      </el-tooltip>
                                      <div style="font-size:13px;width:150px;color:#313233"><span>ASIN:</span><span style="cursor:pointer;" @click.stop="toUrl(scope.row)">{{scope.row.code||'-'}}</span><i class="el-icon-document-copy" style="margin-left:7px;cursor:pointer;" @click.stop="copys(scope.row.code)"></i></div>
                                      <div style="font-size:13px;width:150px;color:#87888c;overflow:hidden;white-space:nowrap;text-overflow:ellipsis"><span>{{international['other.parentAsin']}}:</span> <el-tooltip class="item" effect="dark" :content="scope.row.parentAsin" placement="top"><span style="cursor:pointer;" @click.stop="toUrl(scope.row)">{{scope.row.parentAsin||'-'}}</span></el-tooltip></div>
                                      <div style="font-size:13px;width:150px;color:#313233"><span>{{international['other.brand']}}:</span>{{scope.row.brandName||'-'}}</div>
                                    </div>
                                  </div>
                                </template>
                             </el-table-column>
                             <!--<el-table-column :label="international['table.globalScore']" :render-header="(h,obj) => renderLastHeader(h,obj,international['tip.globalScore'])" min-width="160" align="center" prop="score" sortable="custom">
                                 <template slot-scope="scope">
                                   <span v-if="scope.row.globalScore">{{scope.row.globalScore}}</span>
                                   <span v-else>-</span>
                                 </template>
                             </el-table-column>-->
                            <el-table-column :label="international['table.price']" align="center" prop="price" min-width="110" :render-header="(h,obj) => renderLastHeader(h,obj,international['tip.price'])" sortable="custom">
                                <template slot-scope="scope"><i v-if="scope.row.price">$</i>{{scope.row.price||'-'}}</template>
                            </el-table-column>
                            <el-table-column :label="international['table.salesCount']" align="center" prop="salesCount"  min-width="145" :render-header="(h,obj) => renderLastHeader(h,obj,international['tip.salesCount'])" sortable="custom">
                                <template slot-scope="scope">{{scope.row.salesCount||'-'}}</template>
                            </el-table-column>
                            <!--<el-table-column :label="international['table.currentBsr']" width="220" align="center" prop="currentBsr" :render-header="(h,obj) => renderLastHeader(h,obj,international['tip.currentBsr'])"  sortable="custom">
                                  <template slot-scope="scope">
                                    <el-tooltip  effect="dark"  placement="top-start">
                                      <div slot="content">
                                        <div>{{international['other.categoryRank']}}：</div>
                                        <div>#{{scope.row.firstRank||'-'}}&nbsp;in&nbsp;{{scope.row.firstRankName||'-'}}</div>
                                        <div>{{international['other.subcategoryRank']}}：</div>
                                        <div>#{{scope.row.otherRank||'-'}}&nbsp;in&nbsp;{{scope.row.otherRankName||'-'}}</div>
                                      </div>
                                      <div>
                                        <div class="weignCen">#{{scope.row.firstRank||'-'}}&nbsp;in</div>
                                        <div class="text">{{scope.row.firstRankName||'-'}}</div>
                                        <div class="weignCen">#{{scope.row.otherRank||'-'}}&nbsp;in</div>
                                      <div class="text">{{scope.row.otherRankName||'-'}}</div>
                                      </div>
                                    </el-tooltip>
                                  </template>
                           </el-table-column>-->
                            <el-table-column :label="international['table.commentCount']+'&'" align="center" prop="commentCount" min-width="185" :render-header="(h,obj) => renderLastHeader(h,obj,international['tip.commentCount'],true,international['table.star'])" sortable="custom">
                                  <template slot-scope="scope">
                                    <div class="text">{{scope.row.commentCount||'-'}}</div>
                                    <div class="text">{{scope.row.star||'-'}}</div>
                                  </template>
                           </el-table-column>  
                           <!--<el-table-column :label="international['table.firstDate']" align="center" prop="firstDate" width="170" sortable="custom">
                                  <template slot-scope="scope">{{scope.row.firstDate||'-'}}</template>
                           </el-table-column>-->
                        </el-table>
                        </div>
                        <div v-else style="margin-top: 30px" v-html="goodsTemplate"></div>
                   </div>`,
        data(){
            return{
                //判断热搜词选品策略选品是否显示
                flagList:[],
                align:'',
                sizetypes:[],
                international:{},
                expandedKeys:[],
                timmer:null,
                //选品是否显示
                categoryShow:false,
                //选品输入框
                text:'',
                dataname:[],
                sortingList:[],
                //提交给后端的数据
                seastarEggData:{},
                //选中的数据
                activeName:'amaz',
                //模板的数据
                form:{},
                //样式模板下拉
                templateList:[],
                //tabs名字
                tabsName:[],
                sorting:undefined,
                //排序列表
                sortList:[],
                //热搜关键词
                keysWords:undefined,
                //热搜关键词数据
                keysWordsList:[],
                //选品策略
                strategy:undefined,
                strategyList:[],
                //搜索数据
                searchList:[],
                // 总数
                total:0,
                //活跃商品
                paramMap:0,
                queryParams:{
                    title:'',
                    page:{
                        page:1,
                        pageSize: parseInt(localeSearchData.searchLimit)
                    },
                },
                //表格
                dataList:[],
                seastarEggData:'',
                goodsTemplate: '',
                //搜索的模板
                searchTemplate:'',
                // 搜索数量限制
                searchLimit: null,
            }
        },
        watch:{
            queryParams:{
                handler(newVal){
                    this.searchList.map(item=>{
                        item.checked =[]
                        item.conditions.map(i=>{
                            if(i.type == 'SELECT'){
                                if(newVal[i.field]&&newVal[i.field].indexOf(undefined)==-1){
                                    item.checked.push(i.field)
                                }
                            }else{
                                if(newVal[i.field]){
                                    item.checked.push(i.field)
                                }
                            }
                        })
                    })
                },
                deep:true
            },
        },
        components:{
            iptContent,
            treeselect:VueTreeselect.Treeselect,
            imagePreview,
        },
        created(){
            let url = window.location.href;
            let result = {};
            if (url.indexOf('?') > -1) {
                let str = url.split('?')[1];
                let temp = str.split('&');
                for (let i = 0; i < temp.length; i++) {
                    let temp2 = temp[i].split('=');
                    result[temp2[0]] = temp2[1]
                }
            }
            this.flagList = localeSearchData.conditionType
            // result.searchTemplate 如果在renderAble赋值，否则不赋值
            this.searchTemplate = result.searchTemplate|| localeSearchData.searchTemplate
            this.queryParams.title = result.keyword || localeSearchData.hotKeywords
            this.queryParams.categoryIds = result.categoryIds || localeSearchData.categoryIds[0]
            // searchLimit
            if (result.searchLimit !== '' && result.searchLimit != null ) {
                this.searchLimit = parseInt(result.searchLimit);
            }
            this.queryParams.page = {page: 1, pageSize: this.searchLimit ? this.searchLimit : parseInt(localeSearchData.searchLimit)}
            if (this.queryParams.title || this.queryParams.categoryIds) {
                this.initData()
            }
            this.international = localeMessage.message
            this.getSelectionOptions();
            //获取选品策略
            this.getStrategyList()
            //获取热搜词
            this.getKeysWordsList()
            this.getCategoryLists()
        },
        methods:{
            //点击热搜词
            keysWordsChange(e){
                this.$nextTick(()=>{
                    this.forWidth()
                    this.forLabelWidth()
                })
                let cateList = this.keysWordsList.filter(item=>{
                    return item.keyword == e
                })
                this.strategy = undefined
                this.queryParams = {
                    title:e,
                    categoryIds:cateList[0].categoryIds&&cateList[0].categoryIds.length!=0?cateList[0].categoryIds.toString():undefined,
                    page:{
                        page:1,
                        pageSize: this.searchLimit ? this.searchLimit : parseInt(localeSearchData.searchLimit)

                    },
                    orderByGroup:{
                        orders:[]
                    }
                }
                let apiData ={
                    api: {
                        rq_cid: this.queryParams.categoryIds||null,
                        rq_kw: this.queryParams.title||null,
                        rq_ob: this.queryParams.orderByGroup.orders.length!=0?this.queryParams.orderByGroup.orders[0].column:null,
                        rq_sid: this.strategy||null,
                    },
                    sqm: point.auth_code,
                    tags: ["click"],
                    wp_dz: point.wp_addr
                }
                sensors.track('click_keyword',{
                    custom:JSON.stringify(apiData)
                })
                this.searchList.map(item=>{
                    item.checked = []
                    item.conditions.map(i=>{
                        if(i.type=="SELECT"){
                            this.$set(this.queryParams,i.field,[undefined])
                        }
                        i.selections[0].value = undefined
                    })
                })
                if(this.searchTemplate == 'default' || this.searchTemplate == 'block_default'){
                    this.$refs.multipleTable.sort('price','');
                }else{
                    this.initData()
                }
            },
            //选择选品策略
            selStrategy(id){
                this.$nextTick(()=>{
                    this.forWidth()
                    this.forLabelWidth()
                })
                this.keysWords = undefined
                this.queryParams = {
                    title:'',
                    page:{
                        page:1,
                        pageSize: this.searchLimit ? this.searchLimit : parseInt(localeSearchData.searchLimit)

                    },
                    orderByGroup:{
                        orders:[]
                    }
                }
                let data={}
                this.strategyList.map(item=>{
                    if(item.id == id){
                        data = item.condition
                    }
                })
                if(data){
                    let params = {}
                    data.condition.conditions.map(item=>{
                        params[item.field] = item.value
                    })
                    params.page = data.page
                    params.orderByGroup = data.orderByGroup
                    params.title = data.productSearch.title
                    if(data.productSearch.categoryIds){
                        params.categoryIds = data.productSearch.categoryIds.toString()
                    }else{
                        params.categoryIds = undefined
                    }
                    this.searchList.map(item=>{
                        item.checked = []
                        item.conditions.map(i=>{
                            if(i.type=="SELECT"&&params[i.field]){
                                params[i.field] = params[i.field].split(',')
                            }else if(i.type=="SELECT"){
                                params[i.field] = [undefined]
                            }
                        })
                    })
                    this.queryParams = params
                    if(this.queryParams.orderByGroup.orders.length!=0){
                        if(this.searchTemplate == 'default' || this.searchTemplate == 'block_default'){
                            this.$refs.multipleTable.sort(this.queryParams.orderByGroup.orders[0].column.split('_desc')[0],this.queryParams.orderByGroup.orders[0].asc?'ascending':'descending');
                        }else{
                            this.initData()
                        }
                    }else{
                        this.initData()
                    }
                }else{
                    this.searchList.map(item=>{
                        item.checked = []
                        item.conditions.map(i=>{
                            if(i.type=="SELECT"){
                                this.$set(this.queryParams,i.field,[undefined])
                            }
                            i.selections[0].value = undefined
                        })
                    })
                    if(this.searchTemplate == 'default' || this.searchTemplate == 'block_default'){
                        this.$refs.multipleTable.sort('price','');
                    }else{
                        this.initData()
                    }
                }
            },
            //关键词设置props
            normalizer(node){
                return {
                    label: node.name,
                }
            },
            initData(){
                this.activeName = 'search'
                if (this.searchTemplate === '' || this.searchTemplate == null) {
                    this.searchTemplate = localeSearchData.searchTemplate
                }
                let params = {
                    request: this.getSearchCon(),
                    template: this.searchTemplate,
                    attributes: localeSearchData.attributes
                }
                templateSearch(params).then(res=>{
                    if (this.searchTemplate === 'default' || this.searchTemplate === 'block_default') {
                        this.dataList = res;
                    } else {
                        this.goodsTemplate=res;
                    }
                })
            },
            //抽离的遍历结构
            getSearchCon(){
                let params = {}
                let conditions=[]
                this.searchList.map(item=>{
                    item.conditions.map(i=>{
                        for(let key in this.queryParams){
                            if(i.field == key&&this.queryParams[key]){
                                if(i.type=='SELECT'&&this.queryParams[key][0]!=null){
                                    conditions.push({field:i.field,value:this.queryParams[key].toString()})
                                }else if(i.type!='SELECT'){
                                    conditions.push({field:i.field,value:this.queryParams[key]})
                                }
                            }
                        }
                    })
                })
                params.condition={conditions}
                if(this.sorting){
                    params.orderByGroup= {orders:[{column:this.sorting.value,asc:this.sorting.asc}]}
                }else{
                    params.orderByGroup= {orders:[]}
                }
                params.page=this.queryParams.page
                params.productSearch = {categoryIds:this.queryParams.categoryIds?[this.queryParams.categoryIds*1]:undefined,title:this.queryParams.title||undefined}
                return params
            },
            //获取类目
            getCategoryLists(){
                getCategoryList().then(res=>{
                    this.sortingList = res.data
                    this.nnn(this.sortingList)
                })
            },
            nnn(data,n){
                data.map(item=>{
                    if(item.parentId == 0){
                        item.nnn = item.name
                        if(item.children){
                            this.nnn(item.children,item.name)
                        }
                    }else{
                        item.nnn = n+'>'+item.name
                        if(item.children){
                            this.nnn(item.children,item.nnn)
                        }
                    }
                })
            },
            //获取筛选项
            getSelectionOptions(){
                selectionOptions().then(res=>{
                    this.searchList = res.data
                    this.searchList.map(item=>{
                        item.checked = []
                        item.conditions.map(i=>{
                            if(i.field=='sizeType'){
                                this.sizetypes = i.selections
                            }
                            if(i.type=="SELECT"){
                                this.$set(this.queryParams,i.field,[undefined])
                            }
                            i.selections[0].value = undefined
                        })
                    })
                    this.$nextTick(()=>{
                        this.btnTabs()
                    })
                })
            },
            btnTabs(){
                this.$nextTick(()=>{
                    this.forWidth()
                    this.align = ''
                    this.align = 'auto'
                    this.$nextTick(()=>{
                        this.forLabelWidth()
                    })
                })
            },
            //循环出宽度
            forWidth(){
                let arr = []
                let list = document.getElementsByClassName('groupList')
                for(let i=0;i<list.length;i++){
                    arr.push(list[i].getBoundingClientRect().width)
                }
                let result = arr.filter(item=>{
                    return item!=0
                })
                result.sort((a,b)=>{
                    return a-b
                })
                for(let i=0;i<list.length;i++){
                    if(list[i].getBoundingClientRect().width!=0){
                        list[i].style.width = result[result.length-1]+'px'
                    }
                }
            },
            //循环出label
            forLabelWidth(){
                this.$nextTick(()=>{
                    let arr1 = []
                    let domList = document.getElementsByClassName('el-form-item--small')
                    for(let i=1;i<domList.length;i++){
                        arr1.push(domList[i].childNodes[0].offsetWidth+domList[i].childNodes[0].offsetLeft)
                    }
                    let result1 = arr1.filter(item=>{
                        return item!=0
                    })
                    result1.sort((a,b)=>{
                        return a-b
                    })
                    for(let i=0;i<domList.length;i++){
                        if(domList[i].getBoundingClientRect().width!=0){
                            domList[i].childNodes[1].style.marginLeft = domList[i].childNodes[0].offsetWidth+domList[i].childNodes[0].offsetLeft+'px'
                        }
                    }
                })
            },
            //点击多选
            checkboxChange(obj,data,flag){
                if(this.queryParams[obj.field].length==0){
                    this.queryParams[obj.field] = [undefined]
                }
                if(data.value==undefined&&flag == true){
                    this.queryParams[obj.field] = [undefined]
                }else if(data.value!==undefined&&flag == true){
                    this.queryParams[obj.field]=this.queryParams[obj.field].filter(item=>{
                        return item != undefined
                    })
                }
            },
            //条件搜索
            handleQuery() {
                this.initData()
                this.btnTabs()
            },
            //重置搜索条件
            resetQuery() {
                this.queryParams = {
                    title:'',
                    page:{
                        page:1,
                        pageSize: this.searchLimit ? this.searchLimit : parseInt(localeSearchData.searchLimit)
                    },
                    orderByGroup:{
                        orders:[]
                    }
                },
                    this.strategy = undefined
                this.keysWords = undefined
                this.searchList.map(item=>{
                    item.checked = []
                    item.conditions.map(i=>{
                        if(i.type=="SELECT"){
                            this.$set(this.queryParams,i.field,[undefined])
                        }
                        i.selections[0].value = undefined
                    })
                })
                if(this.searchTemplate == 'default' || this.searchTemplate == 'block_default'){
                    this.$refs.multipleTable.sort('price','');
                }else{
                    this.initData()
                }
            },
            toUrl(row){
                window.open(row.url)
            },
            copys(row){
                let oInput = document.createElement('input');
                oInput.value = row;
                document.body.appendChild(oInput);
                oInput.select();
                document.execCommand("Copy");
                oInput.remove()
                this.$message.success(this.international['other.copySuccess']);
            },
            //选品策略
            getStrategyList(){
                pageSelectionStrategy().then(res=>{
                    this.strategyList = res.records
                    this.strategyList.unshift({name: this.international.unlimited, id:undefined})
                })
            },
            getKeysWordsList(){
                hotSearchKeywordList().then(res=>{
                    this.keysWordsList = res.data
                    this.keysWordsList.unshift({label: this.international.unlimited, keyword:undefined})
                })
            },
            //tips
            renderLastHeader(h, {column},data,flag,content) {
                return h(
                    "div",
                    {
                        style: "display:inline-block",
                    },
                    [
                        h("div",{
                                style:"display:flex;margin:auto;justify-content: center;align-items:center",
                            },
                            [
                                h("div",[
                                    h("div",{style:{display:'block'}},column.label),
                                    h("div", {style:{display:flag?'inline':'none',color:"rgb(135, 136, 140)",fontWeight:'normal'}},content?content:null),
                                ]),
                                // 直接用组件就完事了
                                h(tips, {
                                    props: { messages: data },
                                }),
                            ]
                        ),
                    ]
                );
            },
            //排序
            sortChange(e) {
                if(e.order){
                    this.queryParams.orderByGroup.orders[0]={column:e.order=='ascending'?e.column.property+'_asc':e.column.property+'_desc',asc:e.order=='ascending'?true:false}
                }else{
                    this.queryParams.orderByGroup.orders = []
                }
                this.initData()
            },
        }
})
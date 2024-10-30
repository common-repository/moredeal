( function() {
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
    window.onload=()=>{
        //是否打开
        let btn = document.getElementById('moredeal_meta_box')
        //开关按钮
        let btnSwitch = btn.getElementsByClassName('handlediv')[0]
        //获取表头
        let title = document.getElementsByClassName('edit-post-visual-editor__post-title-wrapper')[0].getElementsByTagName('h1')[0]
        let isShow = title.getElementsByTagName('span')[0]
        //页面第一次进入并且商品池里边没有商品
        if(btn.className.indexOf('closed')==-1&&searchVue.editData.length==0){
            searchVue.initData()
        }
        //页面第一次进入插件是打开时发送埋点
        let plug = document.getElementById('normal-sortables').children
        let pos;
        //获取位置
        for(let i= 0;i<plug.length;i++){
            if(plug[i].id == 'moredeal_meta_box'){
                pos = i+1
            }
        }
            let article= {
                article: {
                    pid:seastarMetaData.postId,
                    tit:isShow?null:title.innerHTML,
                    is:btn.className.indexOf('closed')==-1?true:false,
                    ty: isShow?'add':'update'
                },
                card: {
                    pos
                },
                tags: ["view"],
                sqm: point.auth_code,
                wp_dz: point.wp_addr
            }
            sensors.track('view_plugin',{custom:JSON.stringify(article)})

        //插件打开关闭
        const goodSwitch = (e)=>{
            e.stopPropagation();
            if(btn.className.indexOf('closed')==-1){
                //打开
                searchVue.btnTabs()

                searchVue.editData.length==0&&searchVue.dataList.length == 0?searchVue.initData():''
                let clickView = {
                    article: {
                        pid: seastarMetaData.postId,
                        tit: isShow?null:title.innerHTML,
                        is:true,
                        ty: isShow?'add':'update'
                    },
                    tags: ["click"],
                    sqm: point.auth_code,
                    wp_dz: point.wp_addr
                }
                sensors.track('use_plugin',{custom:JSON.stringify(clickView)})
            }else{
                //关闭
                let clickView = {
                    article: {
                        pid: seastarMetaData.postId,
                        tit: isShow?null:title.innerHTML,
                        is:false,
                        ty: isShow?'add':'update'
                    },
                    tags: ["click"],
                    sqm: point.auth_code,
                    wp_dz: point.wp_addr
                }
                sensors.track('use_plugin',{custom:JSON.stringify(clickView)})
            }
        }
        btnSwitch.addEventListener("click",goodSwitch)
        //获取保存
        let releaseBtn = document.getElementsByClassName('editor-post-publish-panel__toggle')[0]//发布
        let updateBtn = document.getElementsByClassName('editor-post-publish-button')[0]//更新
        const submist = (e)=>{
            let save = {
                article: {
                    pid: seastarMetaData.postId,
                    tit: isShow?null:title.innerHTML,
                    ty: e
                },
                card: {
                    tem: searchVue.form.shortcode
                },
                products: searchVue.editData.map((item,index)=>{return {spm:item.code,tid:item.trace_id,v_idx:index+1}}),
                p_sz: searchVue.editData.length,
                tags: ["click"],
                sqm: point.auth_code,
                wp_dz: point.wp_addr
            }
            sensors.track('save_article', {
                custom: JSON.stringify(save),
            });
        }
        const releaseFn = ()=>{
            setTimeout(()=>{
                let release = document.getElementsByClassName('editor-post-publish-panel')[0].getElementsByClassName('editor-post-publish-panel__header-publish-button')[0]
                release.addEventListener("click",()=>{
                    submist('add')
                })
            },50)
        }
        if(releaseBtn){
            releaseBtn.addEventListener("click",releaseFn)
        }
        const updateFn = ()=>{
            submist('update')
        }
        if(updateBtn){
            updateBtn.addEventListener("click",updateFn)
        }
    }
    var searchVue = new Vue({
        el: "#app",
        template: `<div>
                      <el-input style="width: 300px" v-model="queryParams.title" size="mini" :placeholder="international['title.tips']" />
                      <div style="display: inline-block;width:500px;vertical-align: middle">
                      <treeselect :placeholder="international['title.category']" max-height="600" :closeOnSelect="false" :show-count="true" :options="sortingList" v-model="queryParams.categoryIds" :normalizer="normalizer">
                        <div slot="value-label" slot-scope="{ node }">{{ node.raw.nnn }}</div>
                      </treeselect>
                      </div>
                      <el-button size="mini" type="primary" icon="el-icon-search" @click="handleQuery">{{international['title.search']}}</el-button>
                      <el-button size="mini" icon="el-icon-refresh" @click="resetQuery">{{international['title.reset']}}</el-button><br/>
                      <div class="clearfix" style="margin: 5px 0">
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
                      <div class="clearfix">
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
                      
                      <div style="margin: 5px 0 -5px 0">
                        <el-input size = "mini" :disabled="true" v-model="form.shortcode" style="display: inline-block;width: 400px;margin-top: 5px" @click.native="copyContent"/>
                        <el-select v-model="form.template" size="mini" class="select" @change="setChange">
                         <el-option v-for="item in templateList" :key="item.code" :label="item.label" :value="item.code"></el-option>
                        </el-select>
                      </div>
                      
                      <!--  提交的数据需要统一放到 seastarEggData，最后以 json 字符串形式 ， seastar_data 这个名字不能改变 -->
                      <el-input type="hidden" name="seastar_data" v-model="JSON.stringify(seastarEggData)"></el-input>
                      
                      <div v-for="item in tabsName" :key="item.module" style="position: relative">
                        <el-tabs type="border-card" v-model="activeName" @tab-click="searchBtn">
                        <el-tab-pane name="amaz">
                            <div slot="label"><span>{{item.module}}</span>
                                <div style="width:21px;display: inline-block;"><el-badge :hidden="editData.length==0" :value="editData.length"></el-badge></div>
                            </div>
                            <div v-loading="goodsLoading" @mouseenter="mouseEnter" @mouseleave="mouseLeave">
                                <div style="height: 100px;font-size:20px;text-align:center;line-height:100px;color:rgb(64, 64, 64);opacity: 0.333" v-if="editData.length==0">
                                    {{international['other.pasteOperation']}}
                                </div>
                                <vuedraggable class="wrapper" v-model="editData" handle=".mover" animation="1000">
                                <transition-group>
                                <div class="mover" style="display: flex;justify-content: space-around;align-items: center;padding: 10px 0;cursor:move" v-for="(item,index) in editData" :key="index">
                                <image-preview class="stops" :src="item.picUrl" :maxSrc="item.mainImage"/>
                                <div style="width: 80%">
                                    <div style="display: flex">
                                    <el-input :placeholder="international['goods.title']" size="mini" style="width: 40%" v-model="item.title"/>
                                    <el-input :placeholder="international['goods.name']" size="mini" style="width: 20%" v-model="item.sellerNameLow"/>
                                    <el-input :placeholder="international['goods.source']" size="mini" style="width: 20%" v-model="item.source"/>
                                    <el-input :placeholder="international['goods.price']" size="mini" style="width: 10%" v-model="item.price"/>
                                    <el-input :placeholder="international['goods.unit']" size="mini" style="width: 10%" v-model="item.unit"/>
                                </div>
                                    <el-input type="textarea" :row="3" :placeholder="international['goods.mark']" size="mini" v-model="item.description" style="margin-top:5px;"/>
                                </div>
                                <div>
                                    <el-button style="color:#1d2327" size="mini" type="text" @click="toUrl(item)">{{item.code}}<i class="el-icon-s-promotion" style="color: #0693e3;font-size: 15px"></i></el-button>
                                    <div class="stops" style="cursor:default">{{item.changeTime}}</div>
                                    <el-button style="color: #ff0000" type="text" size="mini" icon="el-icon-delete" @click="removes(item,index)">Removes</el-button>
                                </div>
                            </div>
                                </transition-group>
                              </vuedraggable> 
                            </div>
                              
                        </el-tab-pane>
                        <el-tab-pane label="Search" name="search">
                           <div style="padding: 5px">
                            <el-form
                          :model="queryParams"
                          label-width="auto"
                          ref="queryForm"
                          v-show="showSearch"
                          size="small"
                        >
                        <div style="position: relative">
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
                              type="primary"
                              icon="el-icon-search"
                              @click="handleQuery"
                              >{{international['title.search']}}</el-button
                            >
                            <el-button icon="el-icon-refresh" @click="resetQuery"
                              >{{international['title.reset']}}</el-button
                            >
                          </div>
                        </el-form-item>
                          </el-tab-pane>                         
                        </el-tabs>
                        </div>
                        </el-form>
                        <el-row :gutter="10" class="subtitle">
                          <el-col style="min-width:310px ;">
                            {{international['other.lineTip'].replace('%s1',paramMap).replace('%s2',total)}}
                          </el-col>
                        </el-row>
                        <div style="position: relative">
                             <el-table  v-loading="loading" :data="dataList" ref="multipleTable" @row-click="rowClick" @select="selectionChange" @select-all = "selectAllChange" @sort-change="sortChange">
                            <el-table-column type="selection" width="45"/>
                            <el-table-column :label="international['table.product']" width="270">
                                <template slot-scope="scope">
                                  <div style="display:flex;justify-content: space-between;font-size:12px">
                                    <div>
                                        <image-preview :src="scope.row.picUrl" :maxSrc="scope.row.mainImage"/>
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
                             <el-table-column :label="international['table.globalScore']" :render-header="(h,obj) => renderLastHeader(h,obj,international['tip.globalScore'])" min-width="160" align="center" prop="score" sortable="custom">
                                 <template slot-scope="scope">
                                   <span v-if="scope.row.globalScore">{{scope.row.globalScore}}</span>
                                   <span v-else>-</span>
                                 </template>
                             </el-table-column>
                            <el-table-column :label="international['table.price']" align="center" prop="price" min-width="110" :render-header="(h,obj) => renderLastHeader(h,obj,international['tip.price'])" sortable="custom">
                                <template slot-scope="scope"><i v-if="scope.row.price">$</i>{{scope.row.price||'-'}}</template>
                            </el-table-column>
                            <el-table-column :label="international['table.salesCount']" align="center" prop="salesCount"  min-width="145" :render-header="(h,obj) => renderLastHeader(h,obj,international['tip.salesCount'])" sortable="custom">
                                <template slot-scope="scope">{{scope.row.salesCount||'-'}}</template>
                            </el-table-column>
                            <el-table-column :label="international['table.currentBsr']" width="220" align="center" prop="currentBsr" :render-header="(h,obj) => renderLastHeader(h,obj,international['tip.currentBsr'])"  sortable="custom">
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
                           </el-table-column>
                            <el-table-column :label="international['table.commentCount']+'&'" align="center" prop="commentCount" min-width="185" :render-header="(h,obj) => renderLastHeader(h,obj,international['tip.commentCount'],true,international['table.star'])" sortable="custom">
                                  <template slot-scope="scope">
                                    <div class="text">{{scope.row.commentCount||'-'}}</div>
                                    <div class="text">{{scope.row.star||'-'}}</div>
                                  </template>
                           </el-table-column>  
                            <el-table-column :label="international['table.firstDate']" align="center" prop="firstDate" width="170" sortable="custom">
                                  <template slot-scope="scope">{{scope.row.firstDate||'-'}}</template>
                           </el-table-column>
                        </el-table>    
                             <el-pagination
                            v-if="total>0"
                            style="margin: 20px auto"
                            @size-change="handleSizeChange"
                            @current-change="handleCurrentChange"
                            :current-page="queryParams.page.page"
                            :page-sizes="[10, 20, 30, 50]"
                            :page-size="queryParams.page.pageSize"
                            layout="total, sizes, prev, pager, next, jumper"
                            :total="total">
                            </el-pagination>     
                        </div>
                           </div>       
                        </el-tab-pane>
                      </el-tabs>
                            <div class="hiddenUp" @click="btniShow">
                                 <span v-show="showSearch" class="upText">{{international['other.hiddenCondition']}}</span>
                                 <span v-show="!showSearch" class="upText">{{international['other.showCondition']}}</span>
                                 <i class="el-icon-arrow-up upIcon hide" :style="{transform:!showSearch?' rotate(180deg)':''}" ref="icons"></i>
                            </div>
                      </div>
                   </div>`,
        data(){
            return{
                //鼠标是否在商品池里边
                isShowmouse:false,
                tableHeaderWidth:'',
                align:'',
                sizetypes:[],
                international:{},
                // 显示搜索条件
                loading:false,
                //显示商品池
                goodsLoading:false,
                showSearch: true,
                timmer:null,
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
                //列表展示的数据
                editData:[],
                //选品策略
                strategy:undefined,
                strategyList:[],
                //热搜关键词
                keysWords:undefined,
                //热搜关键词数据
                keysWordsList:[],
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
                        pageSize:10
                    },
                    orderByGroup:{orders:[]}
                },
                //表格
                dataList:[],
                seastarEggData:'',
            }
        },
        watch:{
            activeName(newVal){
                if(newVal == 'search'){
                    //添加监听滚动条事件
                    window.addEventListener('scroll', this.handleScroll, true)
                    //添加监听页面窗口发生变化的事件
                    window.addEventListener('resize', this.handleResize, true)
                }else{
                    //移除（跳转tabs后移除）
                    window.removeEventListener('scroll', this.handleScroll, true)
                    window.removeEventListener('resize', this.handleResize, true)
                }
            },
            form:{
                handler(){
                    let params = this.seastarEggData
                    params.template = this.form.template
                    params.shortcode = this.form.shortcode
                    params.modules[0].products = this.editData
                    this.seastarMetaData = params
                },
                deep:true
            },
            editData:{
                handler(){
                    let params = this.seastarEggData
                    params.template = this.form.template
                    params.shortcode = this.form.shortcode
                    params.modules[0].products = this.editData
                    this.seastarMetaData = params
                },
                deep:true
            },
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
            imagePreview,
            vuedraggable: window.vuedraggable,
            treeselect:VueTreeselect.Treeselect
        },
        created(){
            console.log(point);
            console.log(localeMessage);
            console.log(seastarMetaData)
            this.international = localeMessage.message
            this.seastarEggData = seastarMetaData.metaBox
            this.editData = seastarMetaData.metaBox.modules[0].products||[]
            if(this.editData.length==0) this.activeName = 'search'
            this.templateList = seastarMetaData.templateList
            //页面进入选中模板
            this.form.template = seastarMetaData.templateList[0].code
            this.setChange(this.form.template)
            this.tabsName = seastarMetaData.metaBox.modules
            this.getSelectionOptions();
            //获取选品策略
            this.getStrategyList()
            //获取关键词
            this.getKeysWordsList()
            this.getCategoryLists()
            this.$nextTick(()=>{
                document.addEventListener('keydown', this.keyDown)
            })
        },
        methods:{
            //滚动吸顶
            handleScroll(e) {
                let scrollTop = e.target.scrollTop || 0
                let heightTop = this.$refs['multipleTable'][0].$el.getBoundingClientRect().top+e.target.scrollTop
                let tops
                if(scrollTop == 0&&scrollTop >= heightTop){
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.position = 'absolute'
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.zIndex = '900'
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.top = scrollTop - heightTop + 60+'px'
                }else if (scrollTop >= heightTop-60) { //表头到达页面顶部固定表头
                    if(scrollTop >= heightTop-60&&scrollTop-heightTop<3) {
                        document.getElementsByClassName('el-table__body-wrapper')[0].style.top = 71 + 'px'
                    }else{
                        tops = document.getElementsByClassName('el-table__body-wrapper')[0].style.top.split('px')[0]
                        if(tops>0){
                            document.getElementsByClassName('el-table__body-wrapper')[0].style.top = tops-5 +'px'
                        }else{
                            document.getElementsByClassName('el-table__body-wrapper')[0].style.top = 0 +'px'
                        }
                    }
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.position = 'fixed'
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.zIndex = '900'
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.top = 60+'px'
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.width = this.tableHeaderWidth+'px'
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.overflowX = 'auto'
                }else{
                    this.tableHeaderWidth= document.getElementsByClassName('el-table__body-wrapper')[0].getBoundingClientRect().width
                    document.getElementsByClassName('el-table__body-wrapper')[0].style.top = ''
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.position = ''
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.top = ''
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.zIndex = ''
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.width = '100%'
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.overflowX = ''
                }
                this.newHeightTop = heightTop.top
            },
            handleResize(){
                if(document.getElementsByClassName('el-table__header-wrapper')[0].style.position=='fixed'){
                    this.tableHeaderWidth= document.getElementsByClassName('el-table__body-wrapper')[0].getBoundingClientRect().width
                    document.getElementsByClassName('el-table__header-wrapper')[0].style.width = this.tableHeaderWidth+'px'
                }
            },
            //商品粘贴到商品池
            keyDown(e){
                if((e.ctrlKey&&e.keyCode == 86&&this.isShowmouse)||(e.metaKey&&e.keyCode == 86&&this.isShowmouse)){
                    if(e.target.nodeName!='INPUT'&&e.target.nodeName!='TEXTAREA'){
                        navigator.clipboard.readText()
                            .then((v) => {
                                let params = {}
                                params.productSearch = {title:v}
                                params.page = {page:1,pageSize:10}
                                params.condition = {condition:[]}
                                params.orderByGroup = {orders:[]}
                                this.goodsLoading = true
                                productSearch(params).then(res=>{
                                    if(res.success&&res.records.length!=0){
                                        let result = this.editData.filter(item=>{
                                            return item.code == res.records[0].code
                                        })
                                        if(result.length==0){
                                            this.editData.unshift(res.records[0])
                                            this.goodsLoading = false
                                            this.$message.success(this.international['other.insertSuccessful'])
                                        }else{
                                            this.goodsLoading = false
                                            this.$message.error(this.international['other.theProductAlreadyExists'])
                                        }
                                        this.dataList.map(item=>{
                                            if(item.code == res.records[0].code){
                                                this.$refs.multipleTable[0].toggleRowSelection(item);
                                            }
                                        })
                                    }else{
                                        this.goodsLoading = false
                                        this.$message.error(this.international['other.insertFailed'])
                                    }
                                }).catch(err=>{
                                    this.goodsLoading = false
                                    this.$message.error(this.international['other.systemError'])
                                })
                            })
                            .catch((v) => {
                                this.$message.error('获取剪贴板内容失败');
                            })
                    }
                }
            },
            //点击选品策略
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
                        pageSize:10

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
                    let apiData ={
                        api: {
                            rq_cid: data.productSearch.categoryIds?data.productSearch.categoryIds.toString():null,
                            rq_kw: data.productSearch.title||null,
                            rq_ob: data.orderByGroup.orders.length!=0?data.orderByGroup.orders[0].column:null,
                            rq_sid: this.strategy||null,
                            rq_size:data.condition.conditions.length,
                        },
                        sqm: point.auth_code,
                        tags: ["click"],
                        wp_dz: point.wp_addr
                    }
                    sensors.track('click_sid',{
                        custom:JSON.stringify(apiData)
                    })
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
                    console.log(this.queryParams)
                    if(this.queryParams.orderByGroup.orders.length!=0){
                        this.$refs.multipleTable[0].sort(this.queryParams.orderByGroup.orders[0].column.split('_desc')[0],this.queryParams.orderByGroup.orders[0].asc?'ascending':'descending');
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
                    this.$refs.multipleTable[0].sort('price','');
                }
            },
            //关键词设置props
            normalizer(node){
                return {
                    label: node.name,
                }
            },
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
                        pageSize:10

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
                this.$refs.multipleTable[0].sort('price','');
            },
            mouseEnter(){
                this.isShowmouse = true
            },
            mouseLeave(){
                this.isShowmouse = false
            },
            copyContent(){
                let article= {
                    article: {
                        tem:this.form.shortcode
                    },
                    tags: ["click"],
                    sqm: point.auth_code,
                    wp_dz: point.wp_addr
                }
                sensors.track('click_card',{custom:JSON.stringify(article)})
                let oInput = document.createElement('input');
                oInput.value = this.form.shortcode;
                document.body.appendChild(oInput);
                oInput.select();
                document.execCommand("Copy");
                oInput.remove()
                this.$message.success(this.international['other.copySuccess']);
            },
            setChange(e){
                shortCode({template:e}).then(res=>{
                    this.$set(this.form,"shortcode",res)
                })
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
            removes(data,index){
                let productData = {
                    product: {
                        cid: data.categoryId,
                        jg: data.price*1,
                        spm: data.code,
                        sid: data.sid||null,
                        v_idx: data.search_location,
                        tid: data.trace_id
                    },
                    sqm: point.auth_code,
                    tags: ["click"],
                    wp_dz: point.wp_addr
                }
                sensors.track('del_product', {
                    custom: JSON.stringify(productData),
                });
                this.editData.splice(index,1)
                this.dataList.map(item=>{
                    if(item.code == data.code){
                        this.$refs.multipleTable[0].toggleRowSelection(item);
                    }
                })
            },
            initData(){
                this.loading = true
                this.activeName = 'search'
                let params = this.getSearchCon()
                let newDate = new Date().getTime()
                productSearch(params).then(res=>{
                    let endDate = new Date().getTime()
                    let apiData ={
                        api: {
                            api: productUrl,
                            rq_cid: params.productSearch.categoryIds?params.productSearch.categoryIds.toString():null,
                            rq_kw: params.productSearch.title||null,
                            rq_ob: params.orderByGroup.orders.length!=0?params.orderByGroup.orders[0].column:null,
                            rq_sid: this.strategy||null,
                            rq_size:params.condition.conditions.length,
                            rp_num: res.total,
                            rp_rt: endDate*1 - newDate*1,
                            rp_succ: res.success,
                            tid: res.traceId
                        },
                        sqm: point.auth_code,
                        tags: ["click"],
                        wp_dz: point.wp_addr
                    }
                    sensors.track('req_api',{
                        custom:JSON.stringify(apiData)
                    })
                    if (res && res.success ) {
                        if(res.code == 200){
                            this.loading = false
                            this.dataList = res.records
                            this.total = res.total;
                            this.paramMap = res.total;
                        }else if(res.code == 201){
                            this.loading = false
                            this.dataList = res.records
                            this.total = res.total;
                            this.paramMap = res.paramMap.total;
                        }
                        this.dataList.map(item=>{
                            item.trace_id = res.traceId
                        })
                        for(let item=0;item<this.editData.length;item++){
                            for(let i=0;i<this.dataList.length;i++){
                                if(this.editData[item].code==this.dataList[i].code){
                                    this.$nextTick(()=>{
                                        this.$refs.multipleTable[0].toggleRowSelection(this.dataList[i])
                                    })
                                }
                            }
                        }
                    }else if(res&&res.code == 500){
                        this.loading = false
                        this.$message.error(res.msg);
                    }else {
                        this.loading = false
                        this.$message.error(this.international['other.systemError']);
                    }
                }).catch(err=>{
                    let endDate = new Date().getTime()
                    let apiData ={
                        api: {
                            api: productUrl,
                            rq_cid: params.productSearch.categoryIds||null,
                            rq_kw: params.productSearch.title||null,
                            rq_ob: params.orderByGroup.orders.length!=0?params.orderByGroup.orders[0].column:null,
                            rq_sid: this.strategy||null,
                            rq_size:params.condition.conditions.length,
                            rp_num: null,
                            rp_rt: endDate*1 - newDate*1,
                            rp_succ: null,
                            tid: null
                        },
                        sqm: point.auth_code,
                        tags: ["click"],
                        wp_dz: point.wp_addr
                    }
                    sensors.track('req_api',{
                        custom:JSON.stringify(apiData)
                    })
                    this.loading = false
                    this.$message.error(this.international['other.systemError']);
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
                params.orderByGroup= this.queryParams.orderByGroup
                params.page=this.queryParams.page
                params.productSearch = {categoryIds:this.queryParams.categoryIds?[this.queryParams.categoryIds]:undefined,title:this.queryParams.title||undefined}
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
            searchBtn(tab){
                if(tab.name=='search'){
                    this.$nextTick(()=>{
                        this.forWidth()
                        this.forLabelWidth()
                    })
                }
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
            btniShow(){
                this.showSearch = !this.showSearch;
                this.$nextTick(()=>{this.forLabelWidth()})
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
                let apiData ={
                    api: {
                        rq_cid: this.queryParams.categoryIds||null,
                        rq_kw: this.queryParams.title||null,
                        rq_ob: this.queryParams.orderByGroup.orders.length!=0?this.queryParams.orderByGroup.orders[0].column:null,
                        rq_sid: this.strategy||null,
                        rq_size:this.getSearchCon().condition.conditions.length,
                    },
                    sqm: point.auth_code,
                    tags: ["click"],
                    wp_dz: point.wp_addr
                }
                sensors.track('click_search',{
                    custom:JSON.stringify(apiData)
                })
                this.initData()
                this.btnTabs()
            },
            //重置搜索条件
            resetQuery() {
                this.queryParams = {
                    title:'',
                    page:{
                        page:1,
                        pageSize:10
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
                this.$refs.multipleTable[0].sort('price','');
            },
            //选择排序
            getSorting(){
                this.initData()
            },
            //点击行就选中
            rowClick(row){
                this.$refs.multipleTable[0].toggleRowSelection(row)
                let search_location = this.dataList.findIndex(item=>{return item.code == row.code})
                if(this.editData.every(item=>item.code != row.code)){
                    let search_idx = (this.queryParams.page.page-1)*(this.queryParams.page.pageSize)+search_location+1
                    let productData = {
                        product: {
                            cid: row.categoryId,
                            jg: row.price*1,
                            spm: row.code,
                            sid: this.strategy||null,
                            s_idx: search_idx,
                            tid: row.trace_id
                        },
                        sqm: point.auth_code,
                        tags: ["click"],
                        wp_dz: point.wp_addr
                    }
                    sensors.track('opt_product', {
                        custom: JSON.stringify(productData),
                    });
                    row.search_location = search_idx
                    row.sid = this.strategy||null
                    this.editData.push(row)
                }else{
                    let search_location = this.dataList.findIndex(item=>{return item.code == row.code})
                    this.editData.map((item,index)=>{
                        if(item.code == row.code){
                            let productData = {
                                product: {
                                    cid: row.categoryId,
                                    jg: row.price*1,
                                    spm: row.code,
                                    sid: this.strategy||null,
                                    v_idx: (this.queryParams.page.page-1)*(this.queryParams.page.pageSize)+search_location+1,
                                    tid: row.trace_id
                                },
                                sqm: point.auth_code,
                                tags: ["click"],
                                wp_dz: point.wp_addr
                            }
                            sensors.track('del_product', {
                                custom: JSON.stringify(productData),
                            });
                            this.editData.splice(index,1)
                        }
                    })
                }

            },
            //点击复选框
            selectionChange(selection,row){
                let search_location = this.dataList.findIndex(item=>{return item.code == row.code})
                if(selection.length && selection.indexOf(row) !== -1){
                    let search_idx = (this.queryParams.page.page-1)*(this.queryParams.page.pageSize)+search_location+1
                    let productData = {
                        product: {
                            cid: row.categoryId,
                            jg: row.price*1,
                            spm: row.code,
                            sid: this.strategy||null,
                            s_idx: search_idx,
                            tid: row.trace_id
                        },
                        sqm: point.auth_code,
                        tags: ["click"],
                        wp_dz: point.wp_addr
                    }
                    sensors.track('opt_product', {
                        custom: JSON.stringify(productData),
                    });
                    row.search_location = search_idx
                    row.sid = this.strategy||null
                    this.editData.push(row)
                }else{
                    let search_location = this.dataList.findIndex(item=>{return item.code == row.code})
                    this.editData.map((item,index)=>{
                        if(item.code == row.code){
                            let productData = {
                                product: {
                                    cid: row.categoryId,
                                    jg: row.price*1,
                                    spm: row.code,
                                    sid: this.strategy||null,
                                    v_idx: (this.queryParams.page.page-1)*(this.queryParams.page.pageSize)+search_location+1,
                                    tid: row.trace_id
                                },
                                sqm: point.auth_code,
                                tags: ["click"],
                                wp_dz: point.wp_addr
                            }
                            sensors.track('del_product', {
                                custom: JSON.stringify(productData),
                            });
                            this.editData.splice(index,1)
                        }
                    })
                }
            },
            //点击全选
            selectAllChange(selection){
                if(selection.length != 0){
                    let filterArr = []
                    selection.map(item=>{
                        filterArr.push(item.code)
                    })
                    let newArr = this.editData.filter(val=>{
                        return !filterArr.includes(val.code)
                    })
                    this.editData = [...newArr,...selection]
                }else{
                    let filterArr = []
                    this.dataList.map(item=>{
                        filterArr.push(item.code)
                    })
                    let newArr = this.editData.filter(val=>{
                        return !filterArr.includes(val.code)
                    })
                    this.editData = [...newArr]
                }
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
            getStrategyList(){
                pageSelectionStrategy().then(res=>{
                    this.strategyList = res.records
                    this.strategyList.unshift({name:this.international.unlimited,id:undefined})
                })
            },
            getKeysWordsList(){
                hotSearchKeywordList().then(res=>{
                    this.keysWordsList = res.data
                    this.keysWordsList.unshift({label:this.international.unlimited,keyword:undefined})
                })
            },
            handleSizeChange(val) {
                this.queryParams.page.pageSize = val
                this.initData()
            },
            handleCurrentChange(val) {
                this.queryParams.page.page = val
                this.initData()
            }
        }
    });
})();

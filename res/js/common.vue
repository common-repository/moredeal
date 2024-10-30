<template>
  <div>
    <el-input-number v-model="val1" :controls="false" size="small" placeholder="最小值" style="width:100px"></el-input-number>
    -
    <el-input-number v-model="val2" :controls="false" size="small" placeholder="最大值" style="width:100px"></el-input-number>
    &nbsp;&nbsp;<slot></slot>
  </div>
</template>

<script>

export default {
  name: "demo",
  props: {
    value: {
      type: [String,Number],
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
};
</script>

<style>
</style>

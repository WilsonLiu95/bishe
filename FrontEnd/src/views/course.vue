<template>
  <div class="tab-page-container">
    <mt-search v-model="search" placeholder="按导师姓名，或课题查询课程" :result.sync="course" :show=true @input="inputSearch">
      <!--start 上一页-->
      <div class="nav tab-line">
        <mt-button @click.native="jumpPage(-1)" size="normal">
          <img src="../assets/previous.svg" height="20" width="20" slot="icon">
        </mt-button>
        <mt-button @click.native="isPopupVisible = true" size="normal" v-if="isInit">第{{current_page}}页/共{{course.last_page}}页</mt-button>
        <mt-button @click.native="jumpPage(+1)" size="normal">
          <img src="../assets/next.svg" height="20" width="20" slot="icon">
        </mt-button>
      </div>
      <!--end 下一页-->

      <!--start 课表清单-->
      <div class="page-tab-container" v-if="isInit">
        <mt-cell v-for="n in course.per_page" v-if="course.data[n-1]" :title="course.data[n-1].title" :label="'已有' + course.data[n-1].num + '人选择'"
          :to="userType + '/details/' + course.data[n-1].id" is-link>
          {{ course.data[n-1].teacher_name}}
        </mt-cell>
      </div>
      <!--end 课表清单-->
    </mt-search>
    <!--start 选页面弹窗-->
    <mt-popup v-model="isPopupVisible" position="bottom" class="mint-popup" v-if="isInit">
      <!--这里v-if="isInit"的功能在于首次进入页面时，禁止调用pickerPage函数，以防止其将current_page重置为1-->
      <mt-picker :slots="slots" @change="pickerPage" :visible-item-count="5"></mt-picker>
    </mt-popup>
    <!--end 选页面弹窗-->
  </div>
</template>
<script>
  import { Toast } from 'mint-ui';

  export default {
    name: "course-tab",
    data() {
      return {
        userType: window._const.userType,
        isInit: false, // 用来说明组件是否初始化
        isPopupVisible: false,
        search: "",
        current_page: "0",
        course: {
        },
        slots: [
          {
            flex: 1,
            values: [],
            textAlign: 'center'
          }
        ],
      }
    },
    created() {
      var that = this
      // 为了达到记忆用户在哪个页面的功能，将page保存在全局变量中
      if (_const.page) {
        this.current_page = _const.page
      } else {
        this.current_page = "1"
      }
    },
    watch: {
      // 如果 current_page 发生改变，就向后端发送请求。因为并非通过URL方式记录页面，所以无法通过链接保留页面参数
      current_page: function (page) {
        var that = this
        this.$http.get("/course?page=" + page +"&search="+this.search).then(function (res) {
          that.slots[0].values = getArray(res.data.last_page)
          that.course = res.data
          that.isInit = true
        })
      },
      search: function (search) {
        this.$http.get("/course?page=" + this.current_page +"&search="+this.search).then(function (res) {
          that.slots[0].values = getArray(res.data.last_page)
          that.course = res.data
          that.isInit = true
        })
      }
    },
    methods: {
      pickerPage(picker, n) {
        _const.page = String(n)
        this.current_page = String(n)
      },
      inputSearch() {

      },
      jumpPage(n) {
        n = Number(n)
        var current_page = Number(this.current_page)
        if (n == 1 && current_page == this.course.last_page) {
          Toast({
            message: '已到最后一页',
          });
          return
        }
        if (n == -1 && current_page == 1) {
          Toast({
            message: '已到第一页',
          });
          return
        }
        _const.page = String(current_page + n)
        this.current_page = String(current_page + n)
      }
    },

  };
  function getArray(n) {
    // 用来制造picker组件所需的数组
    var arr = []
    for (var i = 1; i <= n; i++) {
      arr.push(i)
    }
    return arr;
  }
</script>
<style>
  .tab-line {
    display: flex;
    flex-direction:row;
    justify-content: center;
    background-color: #f6f8fa;

    padding: 20px 0 5px 0;
  }
  .mint-popup {
    width: 100%;
  }

</style>

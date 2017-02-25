<template>
  <div class="tab-page-container">
    <mt-search v-model="search" placeholder="按导师姓名，或课题查询课程" :result.sync="course.data" :show=true>
      <!--start 上一页-->
      <div class="nav tab-line">
        <mt-button @click.native="jumpPage(-1)" size="normal">
          <img :src="assets.previous" height="20" width="20" slot="icon">
        </mt-button>
        <mt-button @click.native="isPopupVisible = true" size="normal" v-if="isInit">第{{current_page}}页/共{{course.last_page}}页</mt-button>
        <mt-button @click.native="jumpPage(+1)" size="normal">
          <img :src="assets.next" height="20" width="20" slot="icon">
        </mt-button>
      </div>
      <!--end 下一页-->

      <!--start 课表清单-->
      <div class="page-tab-container" v-if="isInit">
        <mt-cell v-for="item in course.data" :title="item.title" :label="getLabel(item) " :to="'/'+ userType + '/details/' + item.id"
          is-link>
          {{ item.teacher_name}}
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
  export default {
    name: "course-tab",
    data() {
      return {
        assets: {
          previous: require("assets/previous.svg"),
          next: require("assets/next.svg"),
        },
        userType: util.getUserType(),
        isInit: false, // 用来说明组件是否初始化
        isPopupVisible: false,
        search: window._const.search ? window._const.search : "",
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
      if (window._const.search && this.search != window._const.search) {
        this.search = window._const.search
      }
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
        this.$http.get("/course?page=" + page + "&search=" + this.search).then((res) => {
          this.slots[0].values = this.getArray(res.data.data.last_page)
          this.course = res.data.data
          this.isInit = true
        })
      },
      search: function (search) {
        window._const.search = this.search
        this.$http.get("/course?page=" + this.current_page + "&search=" + this.search).then((res) => {
          if (res.data.data.last_page < this.current_page) {
            // 有数据,才重置页面,可减少重复请求
            if (res.data.data.last_page != 0) {
              this.current_page = res.data.data.last_page;
            }
          }
          this.slots[0].values = this.getArray(res.data.data.last_page)
          this.course = res.data.data
          this.isInit = true
        })
      }
    },
    methods: {
      getArray(n) {
        // 用来制造picker组件所需的数组
        var arr = []
        for (var i = 1; i <= n; i++) {
          arr.push(i)
        }
        return arr;
      },
      pickerPage(picker, n) {
        _const.page = String(n)
        this.current_page = String(n)
      },
      jumpPage(n) {
        n = Number(n)
        var current_page = Number(this.current_page)
        if (n == 1 && current_page == this.course.last_page) {
          util.toast({
            message: '已到最后一页',
          });
          return
        }
        if (n == -1 && current_page == 1) {
          util.toast({
            message: '已到第一页',
          });
          return
        }
        _const.page = String(current_page + n)
        this.current_page = String(current_page + n)
      },
      getLabel(item) {
        var stateArr = [
          "已删除", "审核中", '互选中，' + item.student_num + '人选择', '完成互选:' + item.student_name
        ]
        var checkStatusArr = [
          "待审核", "未通过", "已通过"
        ]
        if (item.status == 1) { // 审核中的状态
          stateArr[1] = checkStatusArr[item.check_status]
        }

        return stateArr[item.status]
      }
    },

  };

</script>
<style>
  .tab-line {
    display: flex;
    flex-direction: row;
    justify-content: center;
    background-color: #f6f8fa;
    padding: 20px 0 5px 0;
  }
  .tab-line button {
    margin: 0 2px;
  }

  .mint-popup {
    width: 100%;
  }
</style>

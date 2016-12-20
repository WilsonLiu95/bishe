<template>
  <div class="tab-page-container">
    <mt-search v-model="value" placeholder="按导师姓名，或课题查询课程" :show=true @input="inputSearch">
      <div class="nav tab-line">
        <mt-button @click.native="jumpPage(-1)" size="normal">
          <img src="../assets/previous.svg" height="20" width="20" slot="icon">
        </mt-button>
        <mt-button @click.native="isPopupVisible = true" size="normal">第{{activePage}}页/共{{totalPage}}页</mt-button>
        <mt-button @click.native="jumpPage(+1)" size="normal">
          <img src="../assets/next.svg" height="20" width="20" slot="icon">
        </mt-button>

        <mt-popup v-model="isPopupVisible" position="bottom" class="mint-popup">
          <mt-picker :slots="slots" @change="pickerPage" :visible-item-count="5"></mt-picker>
        </mt-popup>
      </div>
      <div class="page-tab-container">
        <mt-tab-container class="page-tabbar-tab-container" v-model="active" :swipeable=true>
          <mt-tab-container-item v-for="m in totalPage " :id="'tab-container' + m">
            <mt-cell v-for="n in 10" v-if="course.courseArr[10 * (m-1) + n-1]" :title="course.courseArr[10 * (m-1) + n-1].title" :label="course.courseArr[10 * (m-1) + n-1].person"
              :to="userType + '/details/' + course.courseArr[10 * (m-1) + n-1].id" is-link>
              {{m + "页第" + n + "个" + course.courseArr[10 * (m-1) + n-1].teacher}}
            </mt-cell>
          </mt-tab-container-item>
        </mt-tab-container>
      </div>
    </mt-search>



</template>
<script>
  import { Toast } from 'mint-ui';

  export default {
    name: "course-tab",
    data() {
      return {
        userType: window._const.userType,
        isPopupVisible: false,
        active: "tab-container1",
        isSearch: false,
        search: "",
        totalPage: 4,
        activePage: 1,
        course: {
          totalCourse: 31,
          courseArr: []

        },
        slots: [
          {
            flex: 1,
            values: [],
            textAlign: 'center'
          }
        ],
        value: '',
      }
    },
    created() {
      this.slots[0].values = getArray(this.totalPage)
      this.course.courseArr = window.student.course;
      // this.$http.get("/CI/index.php/pages/view/").then(function (res) {
      //   console.log(res)
      // })
    },
    methods: {
      pickerPage(picker, n) {
        this.active = "tab-container" + n[0]
        this.activePage = + n[0]
      },
      inputSearch() {

      },
      jumpPage(n) {
        n = Number(n)
        if (n == 1 && this.activePage == this.totalPage) {
          Toast({
            message: '已到最后一页',
            iconClass: 'icon icon-success'
          });
          return
        }
        if (n == -1 && this.activePage == 1) {
          Toast({
            message: '已到第一页',
            iconClass: 'icon icon-success'
          });
          return
        }

        this.activePage = + this.activePage + n
        console.log(this.activePage)
        this.active = "tab-container" + this.activePage
      }
    },

  };
  function getArray(n) {
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
    padding: 5px 0 5px 0;
  }
  .mint-popup {
    width: 100%;
  }

</style>

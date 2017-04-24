<template>
  <div class="tab-page-container">
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <el-select v-model="current_grade" placeholder="请选择">
              <el-option-group
      v-for="group in gradeList"
      :label="group.label">
      <el-option
        v-for="item in group.options"
        :label="item.label"
        :value="item.value">
      </el-option>
    </el-option-group>
        </el-select>
        <el-input placeholder="搜索课程(支持老师姓名搜索)"
                  icon="search"
                  v-model="option.search.rule"
                  style="width:300px;"
                  class="search-input"
                  :on-icon-click="()=>{init()}">
        </el-input>
      </div>
      <el-table :data="course_list.data"
                border
                @sort-change="res=>{option.orderBy ={
                                key: res.prop,
                                order:res.order == 'descending' ? 'desc':'asc'
                              } }"
                style="width: 100%">
        <el-table-column type="selection"
                         width="55">
        </el-table-column>
    <el-table-column type="expand">
      <template scope="props">
        <el-form label-position="left" inline class="demo-table-expand">
          <el-form-item label="姓名">
            <span>{{ props.row.name }}</span>
          </el-form-item>
          <el-form-item label="学号">
            <span>{{ props.row.job_num }}</span>
          </el-form-item>
          <br>
          <el-form-item label="电话号码">
            <span>{{ props.row.phone }}</span>
          </el-form-item>
          <br>
          <el-form-item label="QQ">
            <span>{{ props.row.qq }}</span>
          </el-form-item>
          <el-form-item label="邮箱">
            <span>{{ props.row.email }}</span>
          </el-form-item>
          <br>
          <el-form-item label="个人介绍">
            <span>{{ props.row.intro }}</span>
          </el-form-item>
        </el-form>
      </template>
    </el-table-column>
        <el-table-column prop="title"
                         label="姓名"
                         width="180">
        </el-table-column>
        <el-table-column prop="teacher_id"
                         label="老师"
                         width="180">
        </el-table-column>
        <el-table-column prop="major_id"
                         label="专业"
                         sortable
                         width="180">
        </el-table-column>
        <el-table-column prop="status"
                         label="课程状态"
                         width="180">
        </el-table-column>
      </el-table>
      <div class="block">
        <el-pagination @current-change="newPage=>{option.page = newPage}"
                       @size-change="newPageSize=>{option.size = newPageSize}"
                       :current-page="option.page"
                       :page-size="option.size"
                       layout="total,sizes,prev, pager, next, jumper"
                       :total="course_list.total">
        </el-pagination>
      </div>
    </el-card>
  </div>  
</template>


<script>
  export default {
    name: "home-page",
    data() {
      return {
        gradeList: [],
        current_grade: 0,
        course_list: {},
        option: {
          search: {
            key: ['name', 'job_num'],
            rule: ""
          }, // 搜索框内容
          size: 20,
          page: 1,
          orderBy: {
            key: 'id',
            order: 'desc'
          },
          filter: {
            grade_id: []
          },
        },
      }
    },
    created() {
      this.getGrade()
    },
  mounted() {
    document.querySelector('.search-input input').addEventListener('keypress', (e) => {
      if (e.keyCode === 13) { // 绑定回车事件
        this.init()
      }
    })
  },
  computed: {
    isRenewData() { // 声明依赖
      var option = this.option
      var tmp = []
      for (var key in this.option) {
        tmp.push(this.option[key])
      }
      tmp.push(this.option.filter.grade_id)
      return tmp
    },
    isCurrentGrade(){
      if(this.gradeList[0]){
        return this.current_grade == this.gradeList[0]['options'][0]['value']
      }else{
        return false
      }

    }
  },
  watch: {
    isRenewData() { // 监听相关依赖，如果有变化，则触发更新
      this.init()
    },
    current_grade(grade){
      if(grade){
        this.option.filter.grade_id = [grade]
      }
    }
  },
  methods: {
  init() {
    // 统一接口
    this.$http.get('course/course-init', {
      noLoading: true,
      params:{
        option: JSON.stringify(this.option)
      }
    }).then(res => {
      this.course_list = res.data.course_list
    })
  },
  getGrade(){
    this.$http.get('home/grade').then(res=>{
      this.gradeList = res.data.data
        this.current_grade = this.gradeList[0]['options'][0]['value']
    })
  },
  }
}
</script>
<style>
 .home-table-title{
   margin: 0 auto;
 }
 .box-card {
   margin-top: 10px;
 }
</style>

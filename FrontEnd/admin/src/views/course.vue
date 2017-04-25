<template>
  <div class="tab-page-container">
    <h2 style="text-align:center">管理课题</h2>
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
        <el-input placeholder="搜索课程(支持课题名称搜索)"
                  icon="search"
                  v-model="option.search.rule"
                  style="width:300px;"
                  class="search-input"
                  :on-icon-click="()=>{init()}">
        </el-input>
      </div>
      <el-table :data="course_list.data"
                border
                @filter-change="filterChange"
                @sort-change="res=>{option.orderBy ={
                                key: res.prop,
                                order:res.order == 'descending' ? 'desc':'asc'
                              } }"
                style="width: 100%">
    <el-table-column type="expand">
      <!--详情-->
      <template scope="props">
        <el-form label-position="left" inline class="demo-table-expand">
          <el-form-item label="课题名称">
            <span>{{ props.row.title }}</span>
          </el-form-item>
          <el-form-item label="老师">
            <span>{{ props.row.teacher_name }}</span>
          </el-form-item>
          <el-form-item label="专业">
            <span>{{ major_map[props.row.major_id] }}</span>
          </el-form-item>
          <br>
          <el-form-item label="审核状态">
            <span>{{ check_map[props.row.check_status] }}</span>
          </el-form-item>
          <el-form-item label="审核意见">
            <span>{{ props.row.check_advice }}</span>
          </el-form-item>
          <br>

          <el-form-item label="课程状态">
            <span>{{ status_map[props.row.status] }}</span>
          </el-form-item>
          <el-form-item label="选中该课的学生">
            <span>{{ props.row.student_list.join(',') }}</span>
          </el-form-item>
          <br>
          <el-form-item label="课程详情">
            <span>{{ props.row.details }}</span>
          </el-form-item>
        </el-form>
      </template>
    </el-table-column>
        <el-table-column prop="title"
                         label="课题名称"
                         width="180">
        </el-table-column>
        <el-table-column prop="teacher_name"
                         label="老师"
                         width="180">
        </el-table-column>
        <el-table-column prop="major"
                         label="专业"
                         column-key="major_id"
                         :filters="major_filters"
                         :formatter="(row,col)=>{return major_map[row.major_id]}"
                         width="180">
        </el-table-column>
        <el-table-column prop="status_text"
                         label="状态"
                         column-key="status"
                         :filters="status_filters"
                         :formatter="(row,col)=>{return status_map[row.status]}"
                         width="180">
        </el-table-column>
        <el-table-column prop="student_name"
                         label="选择该课的学生"
                         :formatter="(row,col)=>{
                           return row.student_list.join(',')
                           }"
                         >
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
        current_grade: '',
        course_list: {},
        major_map: {},
        major_filters: [],
        status_map:{
          0: '已删除',
          1: '审核中',
          2: '互选中',
          3: '完成互选'
        },
        check_map: {
          0: '待审核',
          1: '未通过审核',
          2: '通过审核'
        },
        status_filters: [],
        option: {
          search: {
            key: ['title'],
            rule: ""
          }, // 搜索框内容
          size: 20,
          page: 1,
          orderBy: {
            key: 'id',
            order: 'asc'
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
      tmp.push(this.option.filter.status)
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
      this.major_map = res.data.major_map
      this.makeFilters('major_filters', this.major_map)
      this.makeFilters('status_filters', this.status_map)
    })
  },
  getGrade(){
    this.$http.get('home/grade').then(res=>{
      const gradeList = res.data.data
      if(gradeList[0]['options'].length){
          this.gradeList = gradeList
      }else{
        this.gradeList = gradeList.slice(1)
      }
      this.current_grade = this.gradeList[0]['options'][0]['value']
    })
  },
  filterChange(item) {
    var tmp = {}
    for (var key in this.option.filter) {
      tmp[key] = this.option.filter[key]
    }
    for (var key in item) {
      tmp[key] = item[key]
    }

    this.option.filter = tmp // 重新复制触发更新
  },
  makeFilters(key, data) {
    var tmp = []
    for (var index in data) {
      tmp.push({
        text: data[index],
        value: Number(index)
      })
    }
    this[key] = tmp
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

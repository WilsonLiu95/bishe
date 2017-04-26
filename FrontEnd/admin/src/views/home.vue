<template>
  <div class="tab-page-container">
  <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <el-select v-model="current_grade" placeholder="请选择">
            <el-option-group v-for="group in gradeList" :label="group.label">
              <el-option
                v-for="item in group.options"
                :label="item.label"
                :value="item.value">
              </el-option>
            </el-option-group>
        </el-select>
        <el-button v-if="current_grade" type="primary">导出结果</el-button>
        <el-button v-if="canCreateGrade" type="primary" @click="createGrade">新建学年</el-button>
        <el-button v-if="isCurrentGrade" type="danger" @click="finishGrade">结束当前学年</el-button>
      </div>
        <el-form :inline="false" :model="config" v-if="isCurrentGrade">
          <el-form-item label="单个老师最大创建课程数">
            <el-input-number v-model="config.max_create_class"  :min="1"></el-input-number>
          </el-form-item>
          <el-form-item label="单个学生最大同时选课数">
            <el-input-number v-model="config.max_select_class"  :min="1"></el-input-number>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="submitModify">确定修改</el-button>
          </el-form-item>
        </el-form>
    <el-table :data="reg_table" class="reg_table" border>
      <el-table-column label="注册统计">
        <el-table-column prop="tea_register" label="导师">
        </el-table-column>
        <el-table-column prop="stu_register" label="学生">
        </el-table-column>
      </el-table-column>
      <el-table-column label="课题统计">
        <el-table-column prop="course_total" label="课题总数">
        </el-table-column>
        <el-table-column prop="course_review" label="待审核">
        </el-table-column>
        <el-table-column prop="course_confirm" label="已确认">
        </el-table-column>
      </el-table-column>
    </el-table>
  </el-card>
  </div>  
</template>

<script>
  export default {
    name: "home-page",
    data() {
      return {
        canCreateGrade: false,
        reg_table: [],
        gradeList: [],
        current_grade: '',
        nowGradeId: 0,
        config: {
          max_create_class: 0,
          max_select_class: 0
        }
      }
    },
    created() {
      this.getGrade()
    },
    computed:{
      isCurrentGrade(){
        if(this.gradeList[0]){
          return this.current_grade == this.nowGradeId
        }else{
          return false
        }
      }
    },
    watch:{
      current_grade(newGrade){
        this.getRegTable()
      }
    },
    methods: {
      getRegTable() {
        this.$http.get('home?grade_id='+this.current_grade,{
          noLoading:true
        }).then(res => {
            this.reg_table = [res.data.data]
        })
      },
      getGrade(){
        this.$http.get('home/grade').then(res=>{
          const gradeList = res.data.data.gradeList
          if(gradeList[0]['options'].length===0 && gradeList[1]['options'].length===0){
            // 当前没有创建过任何年份
            this.canCreateGrade = true
            return false
          }
          if(gradeList[0]['options'].length){
              this.gradeList = gradeList // 当前有正在运行的系统
              this.nowGradeId = gradeList[0]['options'][0].value // 当前运行年份的id
              this.config = res.data.data.config
          }else if(gradeList[1]['options'].length){
            // 没有当前学年
            this.canCreateGrade = true
            this.gradeList = gradeList.slice(1)
          }
          this.current_grade = this.gradeList[0]['options'][0]['value']
        })
      },
      submitModify(){
        this.$http.post('home/modify',this.config) // 修改配置
      },
      finishGrade(){
        this.$http.post('home/finish-grade').then(res=>{
          window.location.reload()
        })
      },
      createGrade(){
        this.$prompt('请输入新的学年名称', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          inputPattern: /.+/,
          inputErrorMessage: '年份名称不为空'
        }).then(({value})=>{
          if(value){
            this.$http.post('home/create-grade',{gradeName: value})
          }
        })
      }
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

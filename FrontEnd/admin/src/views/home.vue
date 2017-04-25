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
        <el-button type="primary">导出结果</el-button>
        <el-button v-if="isCurrentGrade" type="danger">结束当前学年</el-button>
      </div>
        <el-form :inline="false" :model="config" v-if="isCurrentGrade">
          <el-form-item label="当前系统状态">
            <el-switch
              v-model="config.status"
              on-text="开"
              off-text="关">
            </el-switch>
          </el-form-item>
          <el-form-item label="单个老师最大创建课程数">
            <el-input-number v-model="config.max_create_class"  :min="1"></el-input-number>
          </el-form-item>
          <el-form-item label="单个学生最大同时选课数">
            <el-input-number v-model="config.max_select_class"  :min="1"></el-input-number>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" >确定</el-button>
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
        isExitGrade: true,
        reg_table: [],
        gradeList: [],
        current_grade: '',
        config: {
          max_create_class: 0,
          max_select_class: 0,
          status: false
        }
      }
    },
    created() {
      this.getGrade()
    },
    computed:{
      isCurrentGrade(){
        if(this.gradeList[0]){
          return this.current_grade == this.gradeList[0]['options'][0]['value']
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
        this.$http.get('home?grade_id='+this.current_grade).then(res => {
          if(res.data.state == 0){
            // 尚未有系统
            this.isExitGrade = false
          }else{
            this.reg_table = [res.data.data]
          }
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

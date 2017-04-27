<template>
  <div class="tab-page-container">
    <h2 style="text-align:center">管理学生</h2>
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <el-select v-model="current_grade" placeholder="请选择">
              <el-option-group
      v-for="group in gradeList"
      :label="group.label">
      <el-option
        v-if="gradeList.length"
        v-for="item in group.options"
        :label="item.label"
        :value="item.value">
      </el-option>
    </el-option-group>
        </el-select>
        <el-input placeholder="搜索学生(支持姓名与工号搜索)"
                  icon="search"
                  v-model="option.search.rule"
                  style="width:300px;"
                  class="search-input"
                  :on-icon-click="()=>{init()}">
        </el-input>
        <el-button @click="addOne()"
                  v-if="isCurrentGrade"
                   type="primary">新增学生</el-button>
        <el-button @click="deleteStudent(multipleSelection)"
                   v-if="isCurrentGrade"
                   type="danger">删除选定学生</el-button>
        <el-upload :action="$http.defaults.baseURL + 'student/file'"
                   name="excel"
                   v-if="isCurrentGrade"
                   :on-success='fileUpload'
                   :on-error='err=>{$message({ message: "导入失败", type: "error" })}'
                   style="width:300px;float: right;">
          <el-button size="small"
                     type="primary">点击上传</el-button>
          <div slot="tip"
               class="el-upload__tip">上传学生的EXCEL，用于导入学生数据</div>
        </el-upload>
      </div>
      <el-table :data="student_list.data"
                border
                @sort-change="res=>{option.orderBy ={
                                key: res.prop,
                                order:res.order == 'descending' ? 'desc':'asc'
                              } }"
                @selection-change="handleSelectionChange"
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
        <el-table-column prop="name"
                         label="姓名"
                         width="180">
        </el-table-column>
        <el-table-column prop="job_num"
                         label="学号"
                         sortable
                         width="180">
        </el-table-column>
        <el-table-column prop="phone"
                         label="电话"
                         width="180">
        </el-table-column>
        <el-table-column prop="email"
                         label="邮箱"
                         width="180">
        </el-table-column>
        <el-table-column prop="openid"
                         label="微信识别id(代表是否已注册绑定)">
        </el-table-column>

        <el-table-column label="操作" 
          v-if="isCurrentGrade">
          <template scope="scope">
            <el-button size="small"
                       type="primary"
                       @click="handleEdit(scope.$index, scope.row, 'classes')">修改</el-button>
            <el-button size="small"
                       type="danger"
                       @click="deleteStudent([scope.row.id])">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="block">
        <el-pagination @current-change="newPage=>{option.page = newPage}"
                       @size-change="newPageSize=>{option.size = newPageSize}"
                       :current-page="option.page"
                       :page-size="option.size"
                       layout="total,sizes,prev, pager, next, jumper"
                       :total="student_list.total">
        </el-pagination>
      </div>
    </el-card>
    <!--=========================================================dialog start===================================================================-->
    <el-dialog title="学生"
               v-model="dialog">
      <!--班级对话框-->
      <el-form :model="newStudent">
        <el-form-item label="学生名">
          <el-input placeholder="请输入学生名"
                    v-model="newStudent.name"></el-input>
        </el-form-item>
        <el-form-item label="学号">
          <el-input placeholder="请输入学号"
                    v-model="newStudent.job_num"></el-input>
        </el-form-item>

      </el-form>
      <div slot="footer"
           class="dialog-footer">
        <el-button @click="dialog = false">取 消</el-button>
        <el-button type="primary"
                   @click="submitEdit">确 定</el-button>
      </div>
    </el-dialog>
  </div>  
</template>

<script>
  export default {
    name: "home-page",
    data() {
      return {
        gradeList: [],
        current_grade: '',
        student_list: {},
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
      multipleSelection: [], // 多选列
      newStudent: {
        name: '',
        job_num: ''
      },
      dialog: false,
      dialogId: 0
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
    this.$http.get('student/student-init', {
      noLoading: true,
      params:{
        option: JSON.stringify(this.option)
      }
    }).then(res => {
      this.student_list = res.data.student_list
    })
  },
  getGrade(){
    this.$http.get('home/grade').then(res=>{
      const gradeList = res.data.data.gradeList
      if(gradeList[0]['options'].length){
          this.gradeList = gradeList
      }else{
        this.gradeList = gradeList.slice(1)
      }
      this.current_grade = this.gradeList[0]['options'][0]['value']
    })
  },
  addOne() {
    this.dialog = true
    this.dialogId = 0
    this.newStudent = {
      name: '',
      job_num: ''
    }
  },
  submitEdit() {
    this.$confirm('确认提交该操作？请仔细核对数据。', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      this.$http.post('student/student-submit', this.newStudent, {
        params: {
          id: this.dialogId,
        }
      }).then(res => {
        this.init()
        this.dialog = false
      })
    }).catch(() => {
      this.$message({
        type: 'info',
        message: '已取消操作'
      });
    })

  },
  handleEdit(index, item, type) {
    this.dialog = true
    this.dialogId = item.id
    for (var key in this.newStudent) {
      this.newStudent[key] = item[key]
    }

  },
  deleteStudent(student_list) {
    this.$confirm('确认删除选中的学生？请仔细确认。删除后，系统将自动将该学生所选课程退选', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      this.$http.post('student/delete', { student_list: student_list }).then(res => {
        this.init(true)
      })
    }).catch(() => {
      this.$message({
        message: '已取消操作',
        type: 'info',
      })
    })
  },
  handleSelectionChange(list) {
    var tmp = []
    list.forEach(item => {
      tmp.push(item.id)
    })
    this.multipleSelection = tmp
  },
    fileUpload(res) {      
      this.$message({ message: res.msg, type: res.status? 'success':"error" })
      this.init(true)
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

<template>
  <div class="tab-page-container">
    <!--注册及课题统计信息表-->
    <h2 style="text-align:center">毕设信息统计</h2>
<el-card class="box-card">
    <el-table v-if="isExitGrade" :data="reg_table" class="reg_table" border>
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
    <div v-else>
      <h3>当前尚未创建新的年份</h3>
    </div>
</el-card>
<h2 style="text-align:center">管理老师</h2>
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <el-input placeholder="搜索老师(支持姓名与工号搜索)"
                  icon="search"
                  v-model="option.search.rule"
                  style="width:300px;"
                  class="search-input"
                  :on-icon-click="()=>{init(false)}">
        </el-input>
        <el-button @click="addOne()"
                   type="primary">新增老师</el-button>
        <el-button @click="deleteTeacher(multipleSelection)"
                   type="danger">删除选定老师</el-button>
        <el-upload :action="$http.defaults.baseURL + 'teacher/file'"
                   name="excel"
                   :on-success='fileUpload'
                   :on-error='err=>{$message({ message: "导入失败", type: "error" })}'
                   style="width:300px;float: right;">
          <el-button size="small"
                     type="primary">点击上传</el-button>
          <div slot="tip"
               class="el-upload__tip">上传老师的EXCEL，用于导入老师数据</div>
        </el-upload>
      </div>
      <el-table :data="teacher_list.data"
                border
                @filter-change="filterChange"
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
                         label="工号"
                         sortable
                         width="180">
        </el-table-column>
        <el-table-column prop="openid"
                         label="微信识别id(代表是否已注册绑定)"
                         width="260">
        </el-table-column>
        <el-table-column prop="major"
                         label="专业"
                         column-key="major_id"
                         :filters="major_filters"
                         :formatter="(row,col)=>{return major_map[row.major_id]}"
                         width="180">
        </el-table-column>
        <el-table-column prop="is_admin"
                         label="是否为管理员"
                         column-key="is_admin"
                         sortable
                         :formatter="(row,col)=>{return row.is_admin ? '专业课审核员':'否'}"
                         width="180">
        </el-table-column>
        <el-table-column label="操作">
          <template scope="scope">
  
            <el-button size="small"
                       type="primary"
                       @click="handleEdit(scope.$index, scope.row, 'classes')">修改</el-button>
            <el-button size="small"
                       type="danger"
                       @click="deleteTeacher([scope.row.id])">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="block">
        <el-pagination @current-change="newPage=>{option.page = newPage}"
                       @size-change="newPageSize=>{option.size = newPageSize}"
                       :current-page="option.page"
                       :page-size="option.size"
                       layout="total,sizes,prev, pager, next, jumper"
                       :total="teacher_list.total">
        </el-pagination>
      </div>
    </el-card>
    <!--=========================================================dialog start===================================================================-->
    <el-dialog title="老师"
               v-model="dialog">
      <!--班级对话框-->
      <el-form :model="newTeacher">
        <el-form-item label="老师名">
          <el-input placeholder="请输入老师名"
                    v-model="newTeacher.name"></el-input>
        </el-form-item>
        <el-form-item label="学号">
          <el-input placeholder="请输入学号"
                    v-model="newTeacher.job_num"></el-input>
        </el-form-item>
  <el-form-item label="是否为专业课审核员">
            <el-switch
          v-model="newTeacher.is_admin"
          on-text="是"
          @change="state=>{
            
          }"
          off-text="否">
        </el-switch>
  </el-form-item>
<el-form-item label="所属专业">
  <el-select v-model="newTeacher.major_id" placeholder="请选择专业">
    <el-option
      v-for="item in major_filters"
      :label="item.text"
      :value="item.value">
    </el-option>
  </el-select>
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
        isExitGrade: true,
        reg_table: [],
        teacher_list: {},
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
          filter: {},
        },
      major_map: [],
      major_filters: [],
      multipleSelection: [], // 多选列
      newTeacher: {
        name: '',
        job_num: '',
        is_admin: false,
        major_id: 0
      },
      dialog: false,
      dialogId: 0
      }
    },
    created() {
      this.init(false)
    },
  mounted() {
    document.querySelector('.search-input input').addEventListener('keypress', (e) => {
      if (e.keyCode === 13) { // 绑定回车事件
        this.init(false)
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
      // tmp.push(this.option.search.rule)
      return tmp
    }
  },
  watch: {
    isRenewData() { // 监听相关依赖，如果有变化，则触发更新
      this.init(false)
    }
  },
    methods: {
      getRegTable() {
        this.$http.get('home').then(res => {
          if(res.data.state == 0){
            // 尚未有系统
            this.isExitGrade = false
          }else{
            this.reg_table = [res.data.data]
          }
        })
      },
    fileUpload(res) {      
      this.$message({ message: res.msg, type: res.status? 'success':"error" })
      this.init(true)
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
  init(noLoading) {
    // 统一接口
    this.getRegTable()
    this.$http.get('teacher/teacher-init',  {
      noLoading: noLoading,
      params:{
        option: JSON.stringify(this.option)
      }
    }).then(res => {
      this.major_map = res.data.major_map
      this.makeFilters('major_filters', this.major_map)
      this.teacher_list = res.data.teacher_list
      this.newTeacher.major_id = this.major_filters[0].value
    })
  },
  addOne() {
    this.dialog = true
    this.dialogId = 0
    this.newTeacher = {
      name: '',
      job_num: '',
      is_admin: false,
      major_id: this.major_filters[0].value,
    }
  },
  submitEdit() {
    this.$confirm('确认提交该操作？请仔细核对数据。', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      this.$http.post('teacher/teacher-submit', this.newTeacher, {
        params: {
          id: this.dialogId,
        }
      }).then(res => {
        this.init(false)
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
    for (var key in this.newTeacher) {
      this.newTeacher[key] = item[key]
    }

  },
  deleteTeacher(teacher_list) {
    this.$confirm('确认删除选中的老师？请仔细确认', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      this.$http.post('teacher/delete', { teacher_list: teacher_list }).then(res => {
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

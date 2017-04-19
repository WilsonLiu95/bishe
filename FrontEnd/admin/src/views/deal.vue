<template>
  <div class="container">
    <!--注册及课题统计信息表-->
    <h2 class="title">毕设信息统计</h2>
    <hr>
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
        <el-table-column prop="course_confirm" label="已确认">
        </el-table-column>
        <el-table-column prop="course_review" label="待审核">
        </el-table-column>
      </el-table-column>
    </el-table>

    <div class="mid_container">
      <!--添加信息按钮-->
      <el-button type="primary" @click="dialogOpen(1)" class="teaAdd">添加</el-button>
      <!--对话框-->
      <el-dialog v-model="dialogFormVisible" @close="dialogClose">
        <h3 v-if="dialogType=='change'">导师信息修改</h3>
        <h3 v-if="dialogType=='add'">导师信息添加</h3>
        <el-form :model="dialogForm">
          <el-form-item label="姓名" :label-width="formLabelWidth">
            <el-input v-model="dialogForm.name" :disabled="dialogType=='change'" auto-complete="off">
            </el-input>
          </el-form-item>
          <el-form-item label="教工号" :label-width="formLabelWidth">
            <el-input v-model="dialogForm.job_num" :disabled="dialogType=='change'" auto-complete="off">
            </el-input>
          </el-form-item>
          <el-form-item label="专业方向" :label-width="formLabelWidth">
            <el-input v-model="dialogForm.major_id" :disabled="dialogType=='change'" auto-complete="off">
            </el-input>
          </el-form-item>
          <el-form-item label="课题数" :label-width="formLabelWidth" v-if="this.dialogType=='change'">
            <el-input v-model="dialogForm.course_num" :disabled="dialogType=='change'" auto-complete="off">
            </el-input>
          </el-form-item>
          <el-form-item label="是否为组长" :label-width="formLabelWidth">
            <el-input v-model="dialogForm.is_admin" auto-complete="off"></el-input>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="teaChange">确 定</el-button>
        </div>
      </el-dialog>
      <!--文件导入-->
      <el-upload class="upload" action="" accept="application/vnd.ms-excel" :on-preview="handlePreview" :on-remove="handleRemove"
        :file-list="fileList" v-if="hasImportTeacher == false">
        <el-button type="primary">导入文件</el-button>
        </el-upload>
        <!--信息搜索-->
        <el-input placeholder="搜索（请输入“姓名”或“教工号”）" icon="search" v-model="searchInput" :on-icon-click="teaSearch" class="search">
        </el-input>
    </div>

    <!--导师信息表-->
    <el-table :data="teacher_table" border class="tc_table" style="width: 100%">
      <el-table-column prop="name" label="导师">
      </el-table-column>
      <el-table-column prop="job_num" label="教工号">
      </el-table-column>
      <el-table-column prop="major_id" label="专业方向" :filters="majorFilter" :filtered-value="filterValue" :filter-method="filterTag">
        <template scope="scope">
          <el-tag :type="scope.row.major_id =='1' ? 'primary' : 'success'" close-transition>
            {{scope.row.major_id}}
          </el-tag>
        </template>
        </el-table-column>
        <el-table-column prop="course_num" label="课题数">
        </el-table-column>
        <el-table-column prop="is_admin" label="是否为组长">
        </el-table-column>
        <el-table-column label="操作" width="150" align="center">
          <template scope="scope">
            <el-button size="small" @click="dialogOpen(2,scope.row.id)">编辑</el-button>
            <el-button size="small" type="danger" @click="teaDelete(scope.row.id)">移除</el-button>
          </template>
        </el-table-column>
    </el-table>

    <!--分页组件-->
    <div class="block">
      <el-pagination :current-page="currentPage" @current-change="changePage" layout="total, prev, pager, next, jumper" :total="teaTotal">
      </el-pagination>
    </div>
  </div>
</template>

<!--====================================================-->

<script>
  export default {
    name: "admin-page",
    data() {
      return {
        searchInput: '',
        currentPage: 1,
        teaTotal: 100,
        dialogFormVisible: false,
        hasImportTeacher: true,
        dialogType: 'add',
        formLabelWidth: '120px',
        dialogForm: {
          name: '',
          job_num: '',
          major_id: '',
          course_num: '',
          is_admin: '',
        },
        current_teacher_id: '',
        reg_table: [],
        teacher_table: [],
        major_map: [],
        majorFilter: [{
          text: '',
          value: ''
        }],
        filterValue:[],
      }
    },
    created() {
      this.getRegTable()
      this.getTeacherTable()
    },
    methods: {
      getRegTable() {
        this.$http.get('deal').then(res => {
          this.reg_table.push(res.data.data)
        })
      },
      getTeacherTable() {
        this.$http.get('deal/teacher?page=' + this.currentPage).then(res => {
          this.teacher_table = res.data.data.teacher.data
          this.teaTotal = res.data.data.teacher.total
          this.major_map = res.data.data.tran
          for(var i=0;i<this.teacher_table.length;i++) {
            for(var key in this.major_map) {
              if(this.teacher_table[i].major_id == key) {
                this.teacher_table[i].major_id = this.major_map[key]
              }  
            }
          }
          var majorFilter = []
          for (var key in res.data.data.tran) {
            majorFilter.push({
              value: key,
              text: res.data.data.tran[key]
            })
          }
          this.majorFilter = majorFilter
        })
      },
      dialogOpen(i, teacher_id) {
        this.current_teacher_id = teacher_id;
        this.dialogFormVisible = true;
        if (i == 1) {
          this.dialogType = 'add';
        }
        if (i == 2) {
          this.dialogType = 'change';
          this.$http.get('deal/tea-dialog?id=' + teacher_id).then(res => {
            this.dialogForm = res.data.data
          })
        }
      },
      dialogClose() {
        this.dialogFormVisible = false;
        this.dialogType = '';
        this.dialogForm = {};
      },
      teaChange() {
        this.dialogFormVisible = false;
        if (this.dialogType == 'change') {
          this.$http.post('deal/tea-edit', {
            id: this.current_teacher_id,
            is_admin: this.dialogForm.is_admin
          }).then(res => {
            this.getTeacherTable()
          })
        }
        if (this.dialogType == 'add') {
          this.$http.post('deal/tea-add', {
            name: this.dialogForm.name,
            job_num: this.dialogForm.job_num,
            major_id: this.dialogForm.major_id,
            is_admin: this.dialogForm.is_admin
          }).then(res => {
            this.getTeacherTable()
          })
        }
      },
      teaDelete(teacher_id) {
        this.current_teacher_id = teacher_id;
        this.$http.post('deal/tea-delete?id=' + this.current_teacher_id).then(res => {
          this.getTeacherTable()
        })
      },
      teaSearch() {
        if (this.searchInput != '') {
          this.$http.get('deal/tea-search?searcher=' + this.searchInput + '&page='+this.currentPage).then(res => {
            this.teacher_table = res.data.data.data
            this.teaTotal = res.data.data.total
          })
        }
        if (this.searchInput == '') {
          this.getTeacherTable()
        }
      },
      changePage(page) {
        this.currentPage = page;
        if(this.filterValue.length == 0){
          this.getTeacherTable()
        }
        if(this.filterValue.length != 0){
          this.filterTag()
        }
      },


      handleRemove() {
        //文件导入的
      },
      handlePreview() {
        //文件导入的
      },

      //专业筛选
      filterTag() {
        this.$http.post('deal/major-filter',{
          value: this.filterValue,
          page: this.currentPage
        }).then(res=>{
          this.teacher_table = res.data.data.teacher.data
          this.teaTotal = res.data.data.teacher.total
          for(var i=0;i<this.teacher_table.length;i++) {
            for(var key in this.major_map) {
              if(this.teacher_table[i].major_id == key) {
                this.teacher_table[i].major_id = this.major_map[key]
              }  
            }
          }
        })
      },
    }
  }

</script>

<!--====================================================-->

<style>
  .container {
    width: 80%;
    margin: 0 auto;
  }
  
  .reg_table {
    margin-bottom: 20px;
  }
  
  .search {
    width: 30%;
    float: right;
    margin-bottom: 5px;
  }
  
  .block {
    margin-top: 20px;
    text-align: center;
  }
  
  .upload,
  .teaAdd,
  .upload_tip {
    float: left;
  }
  
  .upload {
    margin-left: 10px;
  }
</style>
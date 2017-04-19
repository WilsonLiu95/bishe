<template>
  <div class="container">
    <h2 class="title">当前开放选题的学年：{{top_year}}</h2>
    <hr>
  
    <div class="select">
      <!--系统学年设置-->

      <div class="year-select" >
        <el-select v-model="select_year"
                   placeholder="选择年份"
                   class="option"
                   @change="yearChange">
          <el-option v-for="item in year_options"
                     :label="item.label"
                     :value="item.value">
          </el-option>
        </el-select>  
        <el-button type="primary"
                    size="large"
                   :disabled="this.current_year != null"
                   @click="dialogOpen(1)">新增学年</el-button>
        <el-button type="primary" size="large">导出本学年信息</el-button>


      </div>
  
      <!--系统状态设置-->
      <el-select v-model="status_value"
                 placeholder="系统状态选择"
                 class="option">
        <el-option v-for="item in status_options"
                   :label="item.label"
                   :value="item.value">
        </el-option>
      </el-select>
      <el-button type="primary"
                 :disabled="status_value == ''"
                 @click="statusDialog = true">确定</el-button>
      <!--系统状态改变提示对话框-->
      <el-dialog title="提示"
                 v-model="statusDialog"
                 size="tiny">
        <h3>确定要{{status_value === '1'? '开放':"关闭"}}毕设选题吗？</h3>
        <span slot="footer"
              class="dialog-footer">
              <el-button @click="dialogVisible = false">取 消</el-button>
              <el-button type="primary" @click="statusChange">确 定</el-button>     
            </span>
      </el-dialog>
    </div>
    <hr>
  
    <!--对话框-->
    <el-dialog v-model="dialogFormVisible"
               @close="dialogClose">
      <h3 v-if="dialogType == 'stuAdd'">学生信息添加</h3>
      <h3 v-if="dialogType == 'stuEdit'">学年信息修改</h3>
      <h3 v-if="dialogType == 'yearAdd'">学年信息添加</h3>
      <br>
      <el-form :model="dialogForm">
        <!--新增学生-->              
        <!--当输入为空时，点确定按钮的处理！！！！！！！！！！！！！！！！！-->
        <el-form-item label="姓名"
                      :label-width="formLabelWidth"
                      v-if="dialogType == 'stuAdd' || dialogType == 'stuEdit'">
          <el-input v-model="dialogForm.name"
                    auto-complete="off">
          </el-input>
        </el-form-item>
        <el-form-item label="学号"
                      :label-width="formLabelWidth"          
                      v-if="dialogType == 'stuAdd' || dialogType == 'stuEdit'">
          <el-input v-model="dialogForm.job_num"
                    auto-complete="off">
          </el-input>
        </el-form-item>
        <!--新增学年-->
        <el-form-item label="学年"
                      :label-width="formLabelWidth"
                      v-if="dialogType == 'yearAdd'">
          <el-input v-model="dialogForm.new_year"
                    auto-complete="off">
          </el-input>
        </el-form-item>
        <el-form-item label="每个导师最多创建课题数"
                      :label-width="formLabelWidth"
                      v-if="dialogType == 'yearAdd'">
          <el-input v-model="dialogForm.max_create_class"
                    auto-complete="off">
          </el-input>
        </el-form-item>
        <el-form-item label="每个学生最多选择课题数"
                      :label-width="formLabelWidth"
                      v-if="dialogType == 'yearAdd'">
          <el-input v-model="dialogForm.max_select_class"
                    auto-complete="off">
          </el-input>
        </el-form-item>
      </el-form>
      <div slot="footer"
           class="dialog-footer">
        <el-button @click="dialogFormVisible = false">取 消</el-button>
        <el-button type="primary"
                   @click="infoChange">确 定</el-button>
      </div>
    </el-dialog>
    

    <!--标签页-->
    <el-tabs v-model="activeTab"
             type="card"
             @tab-click="handleTabClick"
             class="activeTab">
      <!--课题信息-->
      <el-tab-pane label="课题信息"
                   name="first">
        <!--搜索框-->
        <el-input placeholder="搜索"
                  icon="search"
                  v-model="courSearchInput"
                  :on-icon-click="courSearch"
                  class="search">
        </el-input>
        <!--课题表-->
        <el-table :data="courTable"
                  border
                  style="width: 100%"
                  class="cour-table">
          <el-table-column type="expand">
            <template scope="props">
              <el-form label-position="left"
                       inline
                       class="table-expand">
                <el-form-item label="详情：">
                  <span>{{props.row.details}}</span>
                </el-form-item>
              </el-form>
            </template>
          </el-table-column>
          <el-table-column prop="title"
                           label="课题">
          </el-table-column>
          <el-table-column prop="major_id"
                           label="专业方向">
          </el-table-column>
          <el-table-column prop="teacher_id"
                           label="导师">
          </el-table-column>
          <el-table-column prop="status"
                           label="状态">
          </el-table-column>
        </el-table>
      </el-tab-pane>
  
      <!--学生信息-->
      <el-tab-pane label="学生信息"
                   name="second">
        <el-button type="primary"
                   @click="dialogOpen(2)">添加</el-button>
        <!--文件导入-->
        <el-upload class="upload"
                   action=""
                   accept="application/vnd.ms-excel"
                   :on-preview="handlePreview"
                   :on-remove="handleRemove"
                   :file-list="fileList"
                   v-if="hasImportTeacher == false">
          <el-button type="primary">导入文件</el-button>
        </el-upload>
        <!--搜索框-->
        <el-input placeholder="搜索"
                  icon="search"
                  v-model="stuSearchInput"
                  :on-icon-click="stuSearch"
                  class="search">
        </el-input>
        <!--学生表-->
        <el-table :data="stuTable"
                  border
                  style="width: 100%"
                  class="stu-table">
          <el-table-column prop="name"
                           label="姓名">
          </el-table-column>
          <el-table-column prop="job_num"
                           label="学号">
          </el-table-column>
          <el-table-column prop="determine"
                           label="选定的课题">
          </el-table-column>
          <el-table-column label="操作"
                           width="150"
                           align="center">
            <template scope="scope">
              <el-button size="small"
                         @click="dialogOpen(3, scope.row.id)">编辑</el-button>
              <el-button size="small"
                         type="danger"
                         @click="stuDelete(scope.row.id)">移除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>
    </el-tabs>
  
    <!--分页组件-->
    <div class="block">
      <el-pagination :current-page="currentPage"
                     @current-change="pageChange"
                     layout="total,prev, pager, next, jumper"
                     :total="total">
      </el-pagination>
    </div>
  </div>
</template>

<!--====================================================-->

<script>
export default {
  name: 'manage',
  data() {
    return {
      currentPage: 1,
      activeTab: 'first',
      courSearchInput: '',
      stuSearchInput: '',
      current_year: '',
      top_year: '',
      select_year: '',
      status_value: '',
      dialogFormVisible: false,
      statusDialog: false,
      hasImportTeacher: true,
      dialogType: '',
      total: 100,
      dialogForm: {},
      formLabelWidth: '120px',
      current_student_id: '',
      year_map: {},
      year_options: [],
      status_options: [{
        value: '1',
        label: '开放'
      }, {
        value: '0',
        label: '关闭'
      }],
      courTable: [],
      stuTable: []
    };
  },
  created() {
    this.getCourTable();

  },
  methods: {
    getCourTable() {
      this.$http.get('manage?page=' + this.currentPage + '&year=' + this.select_year).then(res => {
        this.courTable = res.data.data.course.data
        this.total = res.data.data.course.total
        this.current_year = res.data.data.current_year
        this.year_map = res.data.data.select
        var year_options = []
        for (var key in res.data.data.select) {
          year_options.push({
            value: key,
            label: res.data.data.select[key]
          })
        }
        this.year_options = year_options
        if (this.current_year == null) {
          this.top_year = '无'
        }
        else {
          this.top_year = this.year_map[this.current_year]
        }

        if(this.select_year == ''){
          // 初次加载select显示当前开放年份~
        }
        
      })

    },
    getStuTable() {
      this.$http.post('manage/stu-table',{
        page:this.currentPage,
        select_year:this.select_year,
        current_year:this.current_year
      }).then(res => {
        this.stuTable = res.data.data.student.data
        this.total = res.data.data.student.total
      })
    },

    stuDelete(student_id) {
      this.current_student_id = student_id;
      this.$http.get('manage/stu-delete?id=' + this.current_student_id).then(res => {
        this.getStuTable()
      })
    },

    courSearch() {
      if (this.courSearchInput != '') {
        this.$http.get('manage/cour-search?searcher=' + this.courSearchInput + '&page=' + this.currentPage).then(res => {
          this.courTable = res.data.data.data
          this.total = res.data.data.total
        })
      }
      if (this.courSearchInput == '') {
        this.getCourTable()
      }
    },

    stuSearch() {
      if (this.stuSearchInput != '') {
        this.$http.get('manage/stu-search?searcher=' + this.stuSearchInput + '&page=' + this.currentPage).then(res => {
          this.stuTable = res.data.data.data
          this.total = res.data.data.total
        })
      }
      if (this.stuSearchInput == '') {
        this.getStuTable()
      }
    },

    infoChange() {
      if (this.dialogType == 'stuAdd') {
        this.dialogFormVisible = false;
        this.$http.post('manage/stu-add', {
          name: this.dialogForm.name,
          job_num: this.dialogForm.job_num,
          year: this.select_year
        }).then(res => {
          this.getStuTable()
        })
      }
      if (this.dialogType == 'stuEdit') {
        this.dialogFormVisible = false;
        this.$http.post('manage/stu-edit', {
          id: this.current_student_id,
          name: this.dialogForm.name,
          job_num: this.dialogForm.job_num
        }).then(res => {
          this.getStuTable()
        })
      }
      if (this.dialogType == 'yearAdd') {
        this.dialogFormVisible = false;
        this.$http.post('manage/year-add', {
          new_year: this.dialogForm.new_year,
          max_create_class: this.dialogForm.max_create_class,
          max_select_class: this.dialogForm.max_select_class
        }).then(res => {
          this.getCourTable()
        })
      }
    },

    pageChange(page) {
      this.currentPage = page;
      if (this.activeTab == 'first') {
        this.getCourTable()
      }
      if (this.activeTab == 'second') {
        this.getStuTable()
      }
      if (this.dialogType == 'yearAdd') {
        this.yearAdd()
      }
    },


    dialogOpen(i, student_id) {
      if (i == 1) {
        this.dialogType = 'yearAdd';
      }
      if (i == 2) {
        this.dialogType = 'stuAdd';
      }
      if (i == 3) {
        this.dialogType = 'stuEdit';
        this.current_student_id = student_id;
      }
      this.dialogFormVisible = true;
    },

    dialogClose() {
      this.dialogFormVisible = false;
      this.dialogType = '';
      this.dialogForm = {};
    },

    handleTabClick() {
      if (this.activeTab == 'first') {
        this.getCourTable()
      }
      if (this.activeTab == 'second') {
        this.getStuTable()
      }
    },

    statusChange() {     
      this.statusDialog = false;
      if (this.status_value == '1') {
        if (this.current_year == null && this.select_year != '') {
          this.$http.post('manage/system-status', {
            status: this.status_value,
            year: this.select_year
          }).then(res => {
            this.$message({ message: '恭喜你，已成功开放当前学年的选题!', type: 'success' });
            this.getCourTable()
          });
        }
        else if (this.current_year != null) {
          this.$message.error('出错了，请先把已开放选题的学年关闭！');
        }
        else {
          this.$message.error('出错了，请选择要开放选题的学年！');
        }
      }
      else {
        if (this.current_year != null && this.current_year == this.select_year) {
          this.$http.post('manage/system-status', {
            status: this.status_value,
            year: this.select_year
          }).then(res => {
            this.$message({ message: '恭喜你，已成功关闭当前学年的选题!', type: 'success' });
            this.getCourTable()
          });
        }
        else if (this.current_year != null && this.current_year != this.select_year) {
          this.$message.error('出错了，请正确选择要关闭选题的学年！');
        }
        else {
          this.$message.error('出错了，当前没有需要关闭选题的学年！');
        }
      }
    },
    yearChange() {
      if (this.activeTab == 'first') {
        this.getCourTable()
      }
      else {
        this.getStuTable()
      }
    }
  }
};

</script>

<!--====================================================-->

<style>
.title {
  text-align: center;
}

.container {
  width: 80%;
  margin: 0 auto;
}

.year-select {
  float: right;
}

.search {
  width: 30%;
  float: right;
  margin-bottom: 5px;
}

.activeTab {
  margin-top: 30px;
}

.table-expand label {
  width: 90px;
  color: #99a9bf;
}

.table-expand .el-form-item {
  margin-right: 0;
  margin-bottom: 0;
}
</style>
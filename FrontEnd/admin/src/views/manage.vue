<template>
<div class="container">

<h2 class="title">{{year}}毕设信息查看</h2>

<hr>

<!--系统学年设置-->
<div class="select">
  <div class="time_select">
    <el-date-picker v-model="time_value" type="year" placeholder="选择年份">
    </el-date-picker>
    <el-button type="primary">导出本学年信息</el-button>
    <el-button type="primary" v-model="yearBtn" @click="dialogOpen(1)" >新增学年</el-button>

  </div>
  <!--系统状态设置-->
  <i class="el-icon-setting"></i>
  <el-select v-model="options_value" placeholder="系统状态选择" class="option">
  <el-option v-for="item in options" :label="item.label" :value="item.value">
  </el-option>
  </el-select>
  <el-button type="primary" @click="dialogVisible = true">确定</el-button>
  <el-dialog title="提示" v-model="dialogVisible" size="tiny">
  <h3 v-if = "options_value === ''">请选择系统状态！</h3>
  <h3 v-else>确定要{{options_value === 'option1'? '开放':"关闭"}}毕设选题吗？</h3>
  <span slot="footer" class="dialog-footer">
    <el-button @click="dialogVisible = false">取 消</el-button>
    <el-button type="primary" @click="tipOpen">确 定</el-button>     
  </span>
  </el-dialog>
</div>

<hr>

<!--课程和学生信息表-->
<el-tabs v-model="activeTab" type="card" @tab-click="handleClick" class="activeTab">
  <el-tab-pane label="课程信息" name="first">
     <el-button type="primary" v-model="addBtn1" @click="dialogOpen(2)"  class="add">添加</el-button>
       <el-input placeholder="搜索" icon="search" v-model="input" :on-icon-click="handleIconClick" class="search"></el-input>
       <el-table :data="tableData2" border style="width: 100%" class="cs_table">
       <el-table-column prop="course" label="课题">
       </el-table-column>
       <el-table-column prop="teacher" label="导师">
       </el-table-column>
       <el-table-column prop="subject" label="专业方向">
       </el-table-column>
       <el-table-column prop="status" label="状态">
       </el-table-column>
       <el-table-column  label="操作">
         <template scope="scope">
           <el-button size="small" @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
           <el-button size="small" @click="handleEdit(scope.$index, scope.row)">详情</el-button>
         </template>
       </el-table-column>
       </el-table>
  </el-tab-pane>
  <el-tab-pane  label="学生信息" name="second">
       <el-button type="primary" v-model="addBtn2" @click="dialogOpen(3)" class="add" >添加</el-button> 
       <el-upload class="upload" action="" accept="application/vnd.ms-excel"
         :on-preview="handlePreview"
         :on-remove="handleRemove"
         :file-list="fileList"
         v-if="hasImportTeacher == false">
       <el-button type="primary">导入文件</el-button>
       </el-upload> 

       <el-input placeholder="搜索" icon="search" v-model="input" :on-icon-click="handleIconClick" class="search"></el-input>
       <el-table :data="tableData3" border style="width: 100%" class="st_table">
       <el-table-column prop="name" label="姓名">
       </el-table-column>
       <el-table-column prop="number" label="学号">
       </el-table-column>
       <el-table-column prop="determine" label="选定的课题">
       </el-table-column>
       <el-table-column label="操作">
          <template scope="scope">
          <el-button size="small" @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
          <el-button size="small" type="danger" @click="handleDelete(scope.$index, scope.row)">移除</el-button>
          </template>
       </el-table-column>
       </el-table>
  </el-tab-pane> 
</el-tabs>

<!--添加信息对话框-->
<el-dialog v-model="dialogFormVisible" @close="dialogClose">
  <h3>{{dialog_top}}</h3><br>
  <el-form :model="form" v-if="addBtn1 == true">
     <el-form-item label="课题" :label-width="formLabelWidth">
     <el-input v-model="form.course" auto-complete="off"></el-input>
     </el-form-item>
     <el-form-item label="导师" :label-width="formLabelWidth">
     <el-input v-model="form.teacher" auto-complete="off"></el-input>
     </el-form-item>
     <el-form-item label="专业方向" :label-width="formLabelWidth">
     <el-input v-model="form.subject" auto-complete="off"></el-input>
     </el-form-item>
     <el-form-item label="状态" :label-width="formLabelWidth">
     <el-input v-model="form.status" auto-complete="off"></el-input>
     </el-form-item>
  </el-form>
  <el-form :model="form" v-if="addBtn2 == true">
     <el-form-item label="姓名" :label-width="formLabelWidth">
     <el-input v-model="form.name" auto-complete="off"></el-input>
     </el-form-item>
     <el-form-item label="学号" :label-width="formLabelWidth">
     <el-input v-model="form.number" auto-complete="off"></el-input>
     </el-form-item>
     <el-form-item label="选定的课题" :label-width="formLabelWidth">
     <el-input v-model="form.determine" auto-complete="off"></el-input>
   </el-form>

   <el-form :model="form" v-if="yearBtn == true">  
     <el-form-item label="年份" :label-width="formLabelWidth">
       <el-input v-model="form.year" auto-complete="off"></el-input>
     </el-form-item>
   </el-form>

   <div slot="footer" class="dialog-footer">
      <el-button @click="dialogFormVisible = false">取 消</el-button>
      <el-button type="primary" @click="dialogFormVisible = false">确 定</el-button>
   </div>
 </el-dialog>

<!--分页组件-->
<div class="block">
  <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" 
    :current-page="currentPage"
    :page-size="15"
    layout="prev, pager, next, jumper"
    :total="1000">
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
        input:'',
        time_value: '',
        year: 2017,
        currentPage: 1,
        activeTab: 'first',
        options_value: '',
        dialog_top: '',
        dialogFormVisible: false,
        dialogVisible: false,
        hasImportTeacher: true,
        yearBtn: false,
        addBtn1: false,
        addBtn2: false,
        form: {
          course: '',
          teacher: '',
          subject: '',
          status: '',
          name: '',
          number: '',
          determine: '',
          year: '',
          
          delivery: false,
          type: [],
          resource: '',
          desc: ''
        },
        formLabelWidth: '120px',
        options: [{
          value: 'option1',
          label: '开放'
        },{
          value: 'option2',
          label: '关闭'
        }],
        tableData2: [{
          course: '微信选课系统开发',
          teacher: '李军',
          subject: '网络技术应用',
          status: '0/2'
        }, {
          course: '微信选课系统开发',
          teacher: '李军',
          subject: '网络技术应用',
          status: '1/1'
        }, {
          course: '微信选课系统开发',
          teacher: '李军',
          subject: '网络技术应用',
          status: '0/2'
        }, {
          course: '微信选课系统开发',
          teacher: '李军',
          subject: '网络技术应用',
          status: '2/2'
        },  {
          course: '微信选课系统开发',
          teacher: '李军',
          subject: '网络技术应用',
          status: '0/1'
        }],
        tableData3: [{
          name: '李军',
          number: 'U201313760',
          subject: '通信工程',
          class: '1305班',
          determine: '微信选课系统开发'
        }, {
          name: '李军',
          number: 'U201313760',
          subject: '通信工程',
          class: '1305班',
          determine: '微信选课系统开发'
        }, {
          name: '李军',
          number: 'U201313760',
          subject: '通信工程',
          class: '1305班',
          determine: '微信选课系统开发'
        }, {
          name: '李军',
          number: 'U201313760',
          subject: '通信工程',
          class: '1305班',
          determine: '微信选课系统开发'
        },{
          name: '李军',
          number: 'U201313760',
          subject: '通信工程',
          class: '1305班',
          determine: '微信选课系统开发'
        }]
      };
    },
    methods: {
     
      handleClick(tab, event) {
        console.log(tab, event);
      },
      handleClick() {
        console.log(1);
      },
      handleSizeChange(val) {
        console.log(`每页 ${val} 条`);
      },
      handleCurrentChange(val) {
        this.currentPage = val;
        console.log(`当前页: ${val}`);
      },
      handleIconClick(ev) {
        console.log(ev);
      },
      handleEdit(index, row) {
        console.log(index, row);
      },
      handleDelete(index, row) {
        console.log(index, row);
      },
      tipOpen() {
        this.dialogVisible = false;
        if(this.options_value === 'option1'){
          this.$notify({
          title: '成功',
          message: '您已开放系统！',
          type: 'success'
          });
        }
        if(this.options_value === 'option2'){
          this.$notify({
          title: '成功',
          message: '您已关闭系统！',
          type: 'success'
          });
        }
      },
      dialogOpen(i) {
        if(i==1) {
          this.yearBtn = true;
          this.dialog_top = '学年新增';
        }
     
        if(i==2) {
          this.addBtn1 = true;
          this.dialog_top = '课题信息添加';
        }
        if(i==3) {
          this.addBtn2 = true;
          this.dialog_top = '学生信息添加';
        }
        this.dialogFormVisible = true;
      },
      dialogClose() {
        this.dialogFormVisible = false;
        this.yearBtn = false;
        this.addBtn1 = false;
        this.addBtn2 = false;
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
  .select {
    padding-bottom:20px;
  }
  .time_select {
    float: right;
  }
  .search {
  width: 30%;
  float: right;
  margin-bottom: 5px;
}
  .el-icon-setting {
    width: 25px;
  }
  .activeTab {
    margin-top: 30px;
  }
</style>

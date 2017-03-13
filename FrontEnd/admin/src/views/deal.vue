<template>
<div class="container">

<!--注册及课题信息表-->
<h2 class="title">毕设信息统计</h2>

<hr>

<el-table :data="reg_table" class="reg_table" border>
<el-table-column label="注册统计">
  <el-table-column prop="teacher_register" label="导师" >
  </el-table-column>
  <el-table-column prop="student_register" label="学生" >
  </el-table-column>
</el-table-column>
<el-table-column label="课题统计">
  <el-table-column prop="course_total" label="课题总数">
  </el-table-column>
  <el-table-column prop="student_deal" label="已确认">
  </el-table-column>
  <el-table-column prop="shenhe" label="待审核">
  </el-table-column>
</el-table-column>
</el-table> 

<div class="mid_container">
  <!--添加信息对话框-->
  <el-button type="primary" @click="dialogFormVisible = true" class="add">添加</el-button>
  <el-dialog title="毕设导师信息添加" v-model="dialogFormVisible">
  <el-form :model="form">
    <el-form-item label="姓名" :label-width="formLabelWidth">
      <el-input v-model="form.name" auto-complete="off"></el-input>
    </el-form-item>
    <el-form-item label="教工号" :label-width="formLabelWidth">
      <el-input v-model="form.tc_number" auto-complete="off"></el-input>
    </el-form-item>
    <el-form-item label="专业方向" :label-width="formLabelWidth">
      <el-input v-model="form.subject" auto-complete="off"></el-input>
    </el-form-item>
    <el-form-item label="课题" :label-width="formLabelWidth">
      <el-input v-model="form.project" auto-complete="off"></el-input>
    </el-form-item>
  </el-form>
  <div slot="footer" class="dialog-footer">
    <el-button @click="dialogFormVisible = false">取 消</el-button>
    <el-button type="primary" @click="dialogFormVisible = false">确 定</el-button>
  </div>
  </el-dialog>

  <!--文件导入-->
  <el-upload class="upload" action="" accept="application/vnd.ms-excel"
    :on-preview="handlePreview"
    :on-remove="handleRemove"
    :file-list="fileList"
    v-if="hasImportTeacher == false">
  <el-button type="primary">导入文件</el-button>
  </el-upload>
  <!--<div slot="tip" class="upload_tip">只能上传excel文件，且不超过500kb</div>-->

  <!--信息搜索-->
  <el-input placeholder="搜索" icon="search" v-model="input" :on-icon-click="handleIconClick" class="search">
  </el-input>
  
</div>

<!--导师信息表-->
<el-table :data="tableData1" border class="tc_table" style="width: 100%">
<el-table-column prop="teacher" label="导师">
</el-table-column>
<el-table-column prop="tc_number" label="教工号">
</el-table-column>
<el-table-column
   prop="subject"
   label="专业方向"
   :filters="[{ text: '计算机应用', value: '计算机应用' }, { text: '大数据挖掘', value: '大数据挖掘' }, { text: '图像处理', value: '图像处理' }]"
   :filter-method="filterTag">
   <template scope="scope">
     <el-tag
       :type="scope.row.tag === '计算机应用' ? 'primary' : 'success'"
       close-transition>
       {{scope.row.subject}}
     </el-tag>
   </template>
</el-table-column>
<el-table-column prop="pj_total" label="课题数">
</el-table-column>
<el-table-column prop="leader" label="是否为组长">
</el-table-column>
<el-table-column  label="操作">
  <template scope="scope">
    <el-button size="small" @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
    <el-button size="small" type="danger" @click="handleDelete(scope.$index, scope.row)">移除</el-button>
  </template>
</el-table-column>
</el-table>

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
    name: "admin-page",
    data() {
      return {
        input: '',
        currentPage: 1,
        dialogFormVisible: false,
        hasImportTeacher: true,
        form: {
          name: '',
          tc_number: '',
          subject: '',
          project: '',
          
          delivery: false,
          type: [],
          resource: '',
          desc: ''
        },
        formLabelWidth: '120px',
        reg_table: [
          {
            student_register: "210/240",
            teacher_register: "23/120",
            teacher_deal: "123/232",
            student_deal: "188",
            course_total: 200,
            shenhe: 12
          }
        ],
        tableData1: [
          {
          teacher: '李军',
          tc_number: 'T201313760',
          subject: "计算机应用",
          pj_total: "2",
          leader:"是"
        }, {
          teacher: '李军',
          tc_number: 'T201313760',
          subject: "大数据挖掘",
          pj_total: "1",
          leader:"否"
        },{
          teacher: '李军',
          tc_number: 'T201313760',
          subject: "计算机应用",
          pj_total: "2",
          leader:"否"
        },{
          teacher: '李军',
          tc_number: 'T201313760',
          subject: "图像处理",
          pj_total: "3",
          leader:"否"
        }]
      }
    },

    methods: {
      handleEdit(index, row) {
        console.log(index, row);
      },
      handleDelete(index, row) {
        console.log(index, row);
      },
      filterTag(value, row) {
        // return row.status === value;
      },
      handleSizeChange(val) {
        console.log(`每页 ${val} 条`);
      },
      handleCurrentChange(val) {
        this.currentPage = val;
        console.log(`当前页: ${val}`);
      },
      handleRemove(file, fileList) {
        console.log(file, fileList);
      },
      handlePreview(file) {
        console.log(file);
      },
      formatter(row, column) {
        return row.address;
      },
      filterTag(value, row) {
        return row.subject === value;
      },
      handleIconClick(ev) {
      console.log(ev);
      }
      // deleteRow(index, rows) {
      //   rows.splice(index, 1);
      // }
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
  margin-bottom:20px;
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
.upload, .add, .upload_tip {
  float: left;
}
.upload {
  margin-left: 10px;
}
</style>

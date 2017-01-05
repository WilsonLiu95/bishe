<template>
  <div class="tab-page-container">

    <el-table :data="statistics" style="width: 100%" class="statistics" border>
<el-table-column label="注册统计">
  <el-table-column prop="teacher_register" label="老师" width="180">
  </el-table-column>
  <el-table-column prop="student_register" label="学生" width="180">
  </el-table-column>
</el-table-column>

<el-table-column label="课题统计">
  <el-table-column prop="course_total" label="课题总数" width="180">
  </el-table-column>
  <el-table-column prop="student_deal" label="已确认课题">
  </el-table-column>
  <el-table-column prop="shenhe" label="待审核">
  </el-table-column>
</el-table-column>

</el-table>

<el-table :data="tableData" border style="width: 100%" class="course-table">
<el-table-column prop="title" label="课题名">
</el-table-column>
<el-table-column prop="teacher" label="导师">
</el-table-column>
<el-table-column prop="student" label="学生">
</el-table-column>

<el-table-column label="日期" width="180" sortable prop="date">
  <template scope="scope">
    <el-icon name="time"></el-icon>
    <span style="margin-left: 10px">{{ scope.row.date }}</span>
</template>
</el-table-column>

<el-table-column label="状态" width="180" :filters="[{ text: '已发布', value: '已发布' }, { text: '待审核', value: '待审核' }]" :filter-method="filterTag"
  prop="status">
</el-table-column>
<el-table-column label="操作">
  <template scope="scope">
    <el-button size="small" @click="handleEdit(scope.$index, scope.row)">详情</el-button>
    <el-button size="small">审核</el-button>
    <el-button size="small" type="danger" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
  </template>
</el-table-column>
</el-table>

<div class="block">
  <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page="currentPage4" :page-sizes="[2,15, 20, 30]"
    :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="32">
  </el-pagination>
</div>
</div>
</template>
<script>

  export default {
    name: "admin-page",
    data() {
      return {
        statistics: [
          {
            student_register: "132/231",
            teacher_register: "23/231",
            teacher_deal: "123/232",
            student_deal: "222/321",
            course_total: 300,
            shenhe: 12,
          }
        ],
        tableData: [{
          date: '2016-05-02',
          teacher: '王小虎2',
          student: "李军",
          title: "《基于微信的XXX》",
          status: "待审核"
        }, {
          date: '2016-05-04',
          teacher: '王小虎4',
          student: "邹智康",
          title: "《基于微信的XXX》",
          status: "已发布"
        }, {
          date: '2016-05-01',
          teacher: '王小虎1',
          student: "王兴建",
          title: "《基于微信的XXX》",
          status: "驳回"
        }, {
          date: '2016-05-03',
          teacher: '王小虎3',
          student: "王兴建",
          title: "《基于微信的XXX》",
          status: "已互选"
        }],
        currentPage4: 4
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
      }
    }
  }

</script>
<style>
.statistics {
  margin: 10px 0;
}
</style>

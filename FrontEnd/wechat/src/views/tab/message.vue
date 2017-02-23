<template>
  <div class="tab-page-container">
    <mt-cell v-for="n in msg.totalMsg" :title="msg.msgArr[n-1].title" :label="msg.msgArr[n-1].content.substr(0,20)" @click.native="getMsgDeatils(n-1)">
      <span :class="{hasRead: !msg.msgArr[n-1].status }">{{msg.msgArr[n-1].time}}</span>
    </mt-cell>
  </div>

</template>
<script>
  import { MessageBox } from 'mint-ui';

  export default {
    name: "message-tab",
    data() {
      return {
        userType: window.util.getUserType(),
        msg:{}
      }
    },
    created() {
      this.getMsg()
    },
    methods: {
      getMsg() {
        var message = {
          totalMsg: 5,
          msgArr: []
        }
        for (var i = 0; i < student.message.totalMsg; i++) {
          var classExample = {
            title: "退选通知",
            time: "2015-05-05",
            content: "你选择的《进击的巨人》课程已互选结束，很遗憾您并未成功互选，系统已自动帮您退选。",
            status: 0,
          }
          classExample.title = "退选通知" + i
          classExample.time = "2015-" + (Math.random() * 12).toFixed(0) + "-" + i
          classExample.status = Math.random() > 0.5;
          message.msgArr.push(classExample)
        }
        this.msg = message
      },
      getMsgDeatils(index) {
        MessageBox.alert(this.msg.msgArr[index].content, this.msg.msgArr[index].title);
        this.msg.msgArr[index].status = 1
      },

    },

  };

</script>
<style>
  .hasRead {
    color: red
  }
</style>

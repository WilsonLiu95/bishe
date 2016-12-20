<template>
  <div class="tab-page-container">
    <mt-cell v-for="n in msg.totalMsg" :title="msg.msgArr[n-1].title" :label="msg.msgArr[n-1].content.substr(0,20)" @click.native="getMsgDeatils(n-1)" >
      <span :class="{hasRead: !msg.msgArr[n-1].status }" >{{msg.msgArr[n-1].time}}</span>
    </mt-cell>
  </div>

</template>
<script>
  import { MessageBox } from 'mint-ui';

  export default {
    name: "message-tab",
    data() {
      return {
        userType: window._const.userType,
        msg: window.student.message
      }
    },
    created() {
    },
    methods: {
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

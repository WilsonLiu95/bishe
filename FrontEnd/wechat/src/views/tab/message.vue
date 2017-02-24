<template>
  <div class="tab-page-container">
    <div v-if="message.length">
      <mt-cell v-for="(item,index) in message" :title="String(item.from_id)" :label="item.content.substr(0,20)" @click.native="getMsgDeatils(item,index)">
        <span :class="{hasRead: !item.send_status }">{{item.created_at}}</span>
      </mt-cell>
    </div>
    <div v-else>
      <mt-cell title="提示">暂无消息</mt-cell>
    </div>

  </div>

</template>
<script>
  import { MessageBox } from 'mint-ui';

  export default {
    name: "message-tab",
    data() {
      return {
        userType: window.util.getUserType(),
        message: []
      }
    },
    created() {
      this.getMsg()
    },
    methods: {
      getMsg() {
        this.$http.get("/message").then((res) => {
          this.message = res.data.data
        })
      },
      getMsgDeatils(item,index) {
        MessageBox.alert(item.content, item.from_id);

        this.$http.get("/message/read-one-msg?id="+ item.id).then(res => {
          if(res.data.state){
            //  阅读成功 else 没有阅读成功，可能改信息并不属于该用户
            item.send_status=1
          }
        })

      },

    },

  };

</script>
<style>
  .hasRead {
    color: red
  }
</style>

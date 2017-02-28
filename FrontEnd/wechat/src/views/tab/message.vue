<template>
  <div class="tab-page-container">
    <div class="page-infinite-wrapper" ref="wrapper" :style="{ height: wrapperHeight + 'px' }">
      <div class="page-infinite-list" v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="50">
        <mt-cell v-for="(item,index) in message" :title="item.title" :label="item.content.substr(0,20)" @click.native="getMsgDeatils(item,index)">
          <span :class="{hasRead: !item.is_read }">{{item.created_at}}</span>
        </mt-cell>
        <a v-if="!allLoaded" class='msg-tips'>
          <div v-if="isloading">
            <mt-spinner class="spinner" type="fading-circle"></mt-spinner>
            <span>刷新中...</span>
          </div>
        </a>
        <a v-if="allLoaded" class='msg-tips'>
          <span v-if="message.length">已全部加载完成</span>
          <span v-if="!message.length">暂无消息</span>
        </a>
      </div>
    </div>
  </div>
  </div>
</template>
<script>
  export default {
    name: "message-tab",
    data() {
      return {
        message: [],
        seg: 1,
        allLoaded: false,
        isloading: false,
        wrapperHeight: 0
      }
    },
    mounted() {
      this.wrapperHeight = document.documentElement.clientHeight - this.$refs.wrapper.getBoundingClientRect().top;
      if (window._const.msg.length) {
        this.message = window._const.msg
        this.allLoaded = true
      } else {
        // 没有则请求
        this.getMsg(0)
      }
    },
    methods: {
      getMsg(seg) {
        if (this.isloading) {
          // 有请求正在加载，不要再发送
          return;
        }
        if (this.allLoaded) { // 已全部加载不再发起请求
          return;
        }
        this.isloading = true // 开始发送请求
        this.$http.get("/message?seg=" + seg).then((res) => {
          this.isloading = false
          if (!res.data.data.length) {
            // 已全部加载
            window._const.msg = this.message
            this.allLoaded = true
            return
          }
          this.seg = seg + 1
          // debugger
          res.data.data.forEach((item) => {
            this.message.push(item)
          })
        })
      },
      getMsgDeatils(item, index) {
        util.box.alert(item.content, item.title);
        this.$http.get("/message/read-one-msg?id=" + item.id).then(res => {
          if (res.data.state) {
            //  阅读成功 else 没有阅读成功，可能改信息并不属于该用户
            if (this.$parent.unreadMsgNum > 0) {
              this.$parent.unreadMsgNum--
            }
            item.is_read = 1
          }
        })
      },
      loadMore() {
        this.getMsg(this.seg)
      }
    },

  };

</script>
<style>
  .hasRead {
    color: red
  }

  .msg-tips {
    text-align: center;
    min-height: 120px;
    display: block;
  }

  .spinner div {
    display: block;
    margin: 0 auto;
  }
</style>

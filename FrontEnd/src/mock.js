var student = {
  account: {
    name: "wilson",
    student_no: "U201313759",
    school: "华中科技大学",
    major: "通信工程",
    institute: "电子信息与通信学院",
    direction: "互联网",
    phone: 18571639914,
    email: "wilsonliuxyz@gmail.com",
    intro: "致力于做一个有故事的人"
  },
  course: [],
  message: {
    totalMsg: 5,
    msgArr: []
  }
}

var teacher = {
  account: {
    name: "许炜",
    teacher_no: "T20001313",
    school: "华中科技大学",
    institute: "电子信息与通信学院",
    level: "教授",
    direction: "[互联网,图像处理,音频处理]",
    phone: 13788886666,
    email: "66031975@gmail.com",
    intro: "微助教创始人，连续创业者"
  }

}

for (var i = 0; i < 31; i++) {
  var classExample = {
    title: "《基于微信的选课系统》",
    person: "3/1",
    teacher: "许炜",
    id: "123456789"
  }
  classExample.title = "《基于微信的选课系统" + i
  classExample.id += i
  student.course.push(classExample)
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
  student.message.msgArr.push(classExample)
}

export { student, teacher }

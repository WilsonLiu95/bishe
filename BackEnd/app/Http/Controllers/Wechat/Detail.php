<?php

namespace App\Http\Controllers\Wechat;


use App\Model;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class Detail extends Controller
{
    use BaseTrait;

    public function getIndex()
    {
        $id = request()->input("id");

        $course = Model\Course::find($id);
        if (!$course) {
            return $this->toast(0, '课程不存在或已被删除');
        }
        $course->detail(); // 补充详情

        // 查询是否是发布课题的老师
        if ($this->isTeacher()) {
            $course->isowner = $this->isOwner($id);
            $course->isadmin = $this->isCourseAdmin($id);
        } else if ($course->status == 2) {
            // 学生,课程在互选中
            $course->isSelected = $course->schedule()
                ->where("student_id", $this->getSessionInfo("id"))
                ->where("is_finish_select", false)
                ->exists();
        }
        return $this->json(1, $course);
    }

    public function getSelectCourse()
    {
        if ($this->isTeacher()) {
            return $this->toast(0, "操作错误");
        }
        $student_id = $this->getSessionInfo("id");
        if ($this->isMaxSelectCourse()) {
            return $this->toast(0, "已达到最大课程数,不可再选定课程");
        }
        if ($this->isHasDone($student_id)) {
            return $this->toast(0, "您已完成互选,不可再选定其他课程");
        }
        $id = request()->input("id");
        $course = Model\Course::find($id);
        // 首先检验该课程是否可以被选定
        if ($course->status != 2) {
            return $this->toast(0, "课程未进入互选阶段");
        }
        // 可以被选定,再校验该用户是否已经选定
        Model\Schedule::create([
            "course_id" => $id,
            "student_id" => $student_id,
        ]);

        // 选定成功则发送消息
        Model\Message::create([
            'title' => "课程被选定",
            'send_type' => 1,
            'send_id' => $course->teacher_id,
            'content' => "您的《" . $course->title . "》已被" . $this->getUser()->name . '选定。'
        ]);

        return $this->toast(1, "选定成功,请主动联系老师,完成互选");

    }

    public function postModifyCourse()
    {
        if (!$this->isOwner(request()->id) || is_null(request()->id)) {
            return $this->toast(0, '不可修改不属于您的课程');
        }
        $course = Model\Course::find(request()->id);

        $course->update([
            "title" => request()->title,
            "details" => request()->details
        ]);
        if ($course->status == 1 && $course->check_status == 1) { // 未通过审核的课程修改后,即重新回到待审核
            $course->update(["check_status" => 0]);

            // 课程修改后,发送信息给管理员
            Model\Teacher::where("major_id", $course->major_id)
                ->where('is_admin', 1)
                ->get()->each(function ($item) use ($course) {
                    Model\Message::create([
                        'title' => "审核通知",
                        'send_type' => 1,
                        'send_id' => $course->teacher_id,
                        'content' => $this->getUser()->name . "老师的《" . $course->title . "》已经重新修改,请您再次审核。"
                    ]);
                });
        }
        return $this->toast(1, "修改成功");
    }

    public function getDeleteCourse()
    {
        $course = Model\Course::find(request()->id);
        if ($course->status == 3) {
            // 已完成互选,不能直接删除,需要先退选学生恢复到"互选中",才可以继续删除
            return $this->toast(0, "已完成互选,不能直接删除");
        }
        if ($course->status == 2) { // 互选中的课程
            // 接着退选选中该门课的学生
            $course->schedule()->get()->each(function ($item) use ($course) {
                // 将所有学生退选
                // message 向所有学生发送消息
                Model\Message::create([
                    'title' => "退选通知",
                    'send_type' => 2,
                    'send_id' => $item->student_id,
                    'content' => "《" . $course->title . "》课题已被" . $course->teacher->name . "老师删除,已为您自动退选。"
                ]);
                $item->delete();
            });
        }
        $course->delete();// 0代表删除该课程
        return $this->toast(1, "课程删除成功");
    }

    public function getStudentList()
    {
        $course = Model\Course::find(request()->id)
            ->schedule()->get();
        return $this->json(1, $course);
    }

    public function getSelectStudent()
    {
        // 选中学生
        // 先校验学生是否还可以被选中
        if ($this->isHasDone(request()->student_id)) {
            return $this->toast(0, "学生已与其他人完成互选,不可再与您达成互选,系统将自动退订该学生");
        }
        $course = Model\Course::find(request()->id);
        $course->update(['status' => 3]);

        $other_select_student = $course->schedule()
            ->where("student_id", "!=", request()->student_id); // 选定该门课的学生
        $select = $course->schedule()
            ->where("student_id", request()->student_id)->first();

        $other_select_student->get()->each(
            function ($item) use ($course, $select) {
                // 向其他互选中的学生发送信息
                Model\Message::create([
                    'title' => "退选通知",
                    'send_type' => 2,
                    'send_id' => $item->student_id,
                    'content' => "《" . $course->title . "》互选完成," . $course->teacher->name . "已与" . $select->student->name . '完成互选,已帮您自动退选。'
                ]);
            }
        );
        $other_select_student->delete(); // 首先将其他所有人置为0,再将选中的个体置为2

        Model\Message::create([
            'title' => "互选成功通知",
            'send_type' => 2,
            'send_id' => $select->student_id,
            'content' => "《" . $course->title . "》互选完成," . $course->teacher->name . "已与您完成互选"
        ]);
        $select->update(["is_finish_select" => true]);

        $select_other_sc = $select->student->schedule()->where('is_finish_select', false);

        $select_other_sc->get()->each(function ($sc) use ($course) {
            Model\Message::create([
                'title' => "退选通知",
                'send_type' => 1,
                'send_id' => $sc->course->teacher_id,
                'content' => $sc->student->name . "已与其他老师达成互选,系统已为其自动退选您的" . $sc->course->title
            ]);
        });
        $select_other_sc->delete(); // 退选其他课程,并且发送消息

        return $this->toast(1, "已完成互选，马上为您自动跳转");
    }

    public function getDeleteStudent()
    {
        $course = Model\Course::find(request()->id);
        $course->update(["status" => 2]);
        $sc = $course->schedule()
            ->where("is_finish_select", true)->first();
        if ($sc) {
            Model\Message::create([
                'title' => "退选通知",
                'send_type' => 2,
                'send_id' => $sc->student_id,
                'content' => $course->teacher->name . "老师主动将《" . $course->title . "》为您退选,请您尽快选择其他课程。"
            ]);
            $sc->delete(); // 老师方面退选学生,学生回到退选状态。
        }
        return $this->toast(1, "退选学生成功");
    }

    public function getCancelSelectCourse()
    {
        $student_id = $this->getSessionInfo("id");
        $sc = Model\Course::find(request()->id)
            ->schedule()
            ->where("student_id", $student_id)
            ->first();
        $sc->delete();
        Model\Message::create([
            'title' => "退选通知",
            'send_type' => 1,
            'send_id' => $sc->course->teacher_id,
            'content' => $sc->student->name . "已主动退选您的《" . $sc->course->title . "》课程。"
        ]);

        return $this->toast(1, "退选课程成功");
    }

    public function postCheckCourse()
    {
        $course = Model\Course::find(request()->id);
        if (request()->is_pass) {
            // 通过审核
            $course->update([
                    'check_status' => 2,
                    'check_advice' => request()->check_advice,
                    'status' => 2
                ]
            );
        } else {
            // 通过审核
            $course->update([
                    'check_status' => 1,
                    'check_advice' => request()->check_advice,
                    'status' => 1
                ]
            );
        }
        Model\Message::create([
            'title' => "审核通知",
            'send_type' => 2,
            'send_id' => $course->teacher_id,
            'content' => "您的《" . $course->title . "》课程" . (request()->is_pass ? "通过" : "未通过") . "审核。 审核意见如下: " .
                request()->check_advice
        ]);

        return $this->toast(1, "审核完成");
    }


    private function isMaxSelectCourse()
    {
        $num = $this->getUser()->schedule()->count();
        $max = $this->getGrade()->max_select_class;
        return $num < $max ? false : true;
    }

    // 判断是否为该课程的主人,以鉴定权限
    private function isOwner($course_id)
    {
        $course = Model\Course::find($course_id);
        return $course->teacher_id == $this->getSessionInfo("id");
    }

    private function isHasDone($student_id)
    {
        // 查看学生是否有在"完成互选"的进度
        return Model\Schedule::where("student_id", $student_id)
            ->where("is_finish_select", true)->exists(); // 2为完成互选
    }

    private function isCourseAdmin($course_id)
    {
        $course = Model\Course::find($course_id);
        if (!$this->isTeacher() || !$this->getUser()->isMajorAdmin() ||
            $course->major_id != $this->getUser()->major_id
        ) { // 不是老师 or 不是管理员 or 专业不同
            return false;
        }
        return true;
    }
}

package router

import (
	"dev/course/model"
)

var Routers map[string]interface{}

func init(){
	Routers = make(map[string]interface{})

	// 课程管理
	Routers["SelectCourse"] = model.SelectCourse
	Routers["StoreCourse"] = model.StoreCourse
	Routers["SelectCourseList"] = model.SelectCourseList

	// 课时管理
	Routers["SelectCourseLessonList"] = model.SelectCourseLessonList
}
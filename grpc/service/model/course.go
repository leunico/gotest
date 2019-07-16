package model

import (
  	"github.com/jinzhu/gorm"
	"dev/course/model/db"
	"dev/course/src"
	"errors"
	"strings"
	"dev/course/helper"

	"fmt"
	// "reflect"
	// "os"
)

type CourseType struct {
	src.Model
	TypeName string
}

type Course struct {
	src.Model
	Title string
	Price int32
	OriginalPrice int32
	Category int32 `gorm:"type:tinyint(1);unsigned"`
	CourseCount int32 `gorm:"type:smallint(3);unsigned"`
	CourseTypeId int32 `gorm:"index;unsigned"`
	CourseType CourseType
	Level int32 `gorm:"type:tinyint(1);unsigned"`
	CourseClass string
	CourseObject string
	CourseIntro string
	Status int32 `gorm:"type:tinyint(1);default:0;unsigned"`
	CourseDuration string `gorm:"type:varchar(50)"`
	IsDrainage int32 `gorm:"type:tinyint(1);default:0;unsigned"`
	IsMail int32 `gorm:"type:tinyint(1);default:0;unsigned"`
	Grounding int32 `gorm:"type:tinyint(1);default:0;unsigned"`
	Scheduling int32 `gorm:"type:tinyint(1);default:1;unsigned"`
	ClassTimeLength int `gorm:"type:bigint(20);unsigned"`
	Version int32 `gorm:"type:int(11);default:1;unsigned"`
	LessonPrice int32 `gorm:"type:int(11);default:0;unsigned"`

	// 添加返回字段
	TypeName string `gorm:"-"`
}

/*
* @fuc 获取课程列表
*/
func SelectCourseList(p map[string]string) ([]Course, error, map[string]int) {
	// select
	var courseRets []Course
	mydb := db.GetInstance().GetMysqlDB()
	
    // 使用id获取记录
	res := mydb.Debug().Select("courses.*, course_types.type_name").Joins("left join course_types on course_types.id = courses.course_type_id")

	if p["status"] != "" {
		res = res.Where("status = ?", p["status"])
	}

	if p["category"] != "" {
		res = res.Where("category = ?", p["category"])
	}

	if p["level"] != "" {
		res = res.Where("level = ?", p["level"])
	}

	if p["course_type_id"] != "" { // todo 判断是否多个： strings.Index(p["course_type_id"], ",") != -1
		res = res.Where("course_type_id in (?)", strings.Split(p["course_type_id"], ","))
	}

	if p["keyword"] != "" {
		res = res.Where("title LIKE ?", "%" + p["keyword"] + "%")
	}

	if p["price"] == "1" {
		res = res.Order("price desc")
	} else if p["price"] == "0" {
		res = res.Order("price")
	}

	pages, errCount := helper.Paginate(res.Model(&Course{}), p["page"], p["perPage"]);
	if errCount != nil {
		return nil, errCount, nil
	}

    if err := res.Offset(pages["start"]).Limit(pages["per_page"]).Find(&courseRets).Error; err != nil {
        return nil, errors.New("查询失败"), nil
	}
	
    return courseRets, nil, pages
}

/*
* @fuc 获取课程
*/
func SelectCourse(p map[string]string) (Course, error) {
	// select
	var course Course
	mydb := db.GetInstance().GetMysqlDB()
	
	err := mydb.Debug().Preload("CourseType", func(db *gorm.DB) *gorm.DB {
		return db.Select("id,type_name,created_at,updated_at").Unscoped()
	}).Where("id = ?", p["id"]).Find(&course).Error

    if err != nil {
        return Course{}, errors.New("查询失败")
	}

    return course, nil
}

/*
* @fuc 添加课程
*/
func StoreCourse(p map[string]string) (map[string]int, error) {
	// select
	// course := Course{
	// 	Title: p["title"],
	// 	Price: p["price"],
	// 	OriginalPrice: p["original_price"],
	// }
	// mydb := db.GetInstance().GetMysqlDB()
	
	fmt.Println(p)

    // if err != nil {
    //     return nil, errors.New("查询失败")composer 
	// }

    return nil, nil
}
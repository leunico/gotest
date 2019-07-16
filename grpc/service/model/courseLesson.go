package model

import (
	"github.com/jinzhu/gorm"
	"time"
	"dev/course/model/db"
	"errors"

	// "fmt"
	// "os"
)

type CourseLesson struct {
	gorm.Model
	Id int32 `gorm:"primary_key;auto_increment"`
	Title string
	Price int32
	OriginalPrice int32
	Category int32 `gorm:"type:tinyint(1);unsigned"`
	CourseCount int32 `gorm:"type:smallint(3);unsigned"`
	courseTypeId int32 `gorm:"index;unsigned"`
	Level int32 `gorm:"type:tinyint(1);unsigned"`
	CourseClass string
	CourseObject string
	CourseIntro string
	Status int32 `gorm:"type:tinyint(1);unsigned"`
	CourseDuration string `gorm:"type:varchar(50)"`
	CreatedAt time.Time
	UpdatedAt time.Time
	DeletedAt *time.Time
}

/*
* @fuc 获取课程列表
*/
func SelectCourseLessonList(p map[string]string) ([]CourseLesson, error) {
	// select
	var courseLessonRets []CourseLesson
	mydb := db.GetInstance().GetMysqlDB()
	
    // 使用id获取记录
	err := mydb.Offset(20).Limit(p["perPage"]).Find(&courseLessonRets).Error // .Debug()

    if err != nil {
        return []CourseLesson{}, errors.New("查询失败")
    }

    return courseLessonRets, nil
}
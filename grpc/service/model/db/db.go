package db

import (
    "fmt"
    "github.com/jinzhu/gorm"
    _ "github.com/jinzhu/gorm/dialects/mysql"
    "log"
    "sync"
)

/*
* MysqlConnectiPool
* 数据库连接操作库
 */
type MysqlConnectiPool struct {
    // ...
}

var instance *MysqlConnectiPool
var once sync.Once
var db *gorm.DB
var err_db error

func GetInstance() *MysqlConnectiPool {
    once.Do(func() {
        instance = &MysqlConnectiPool{}
    })
    return instance
}

/*
* @func 初始化数据库连接
*/
func (m *MysqlConnectiPool) InitDataPool() (issucc bool) {
    db, err_db = gorm.Open("mysql", "root:Dev@CodePKu@tcp(course.dev.codepku.com:3306)/codepku_course?charset=utf8&parseTime=True&loc=Local")
    fmt.Println(err_db)
    if err_db != nil {
        log.Fatal(err_db)
        return false
	}
	
    // todo 关闭数据库，db会被多个goroutine共享，可以不调用
	// defer db.Close()
	
    return true
}

/*
* @func 对外获取数据库连接对象db
*/
func (m *MysqlConnectiPool) GetMysqlDB() (db_con *gorm.DB) {
    return db
}
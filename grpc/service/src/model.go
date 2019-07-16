package src

import (
    "dev/course/src/utils"
)

type Model struct {
    ID        uint            `gorm:"primary_key" json:"id"`
    CreatedAt utils.JSONTime  `json:"createdAt"`
    UpdatedAt utils.JSONTime  `json:"updatedAt"`
    DeletedAt *utils.JSONTime `sql:"index" json:"-"`
}
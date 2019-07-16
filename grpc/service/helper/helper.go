package helper

import (
	"github.com/jinzhu/gorm"
	"strconv"
	// "errors"

  	// "fmt"
  	// "reflect"
  	// "os"
)

func Paginate(res *gorm.DB, page string, perPage string) (map[string]int, error) {
	intPage, err := strconv.Atoi(page)
	if err != nil {
		return nil, err
	}

	intperPage, err := strconv.Atoi(perPage)
	if err != nil {
		return nil, err
	}
	
	var total int
	if err := res.Count(&total).Error; err != nil {
        return nil, err
	}

	pages := make(map[string]int)
	pages["current_page"] = intPage
	pages["last_page"] = (total % intperPage) + 2
	pages["per_page"] = intperPage
	pages["total"] = total
	pages["start"] = (intPage - 1) * intperPage

	return pages, nil
}
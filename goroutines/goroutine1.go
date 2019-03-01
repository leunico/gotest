package main

import (
	"fmt"
	"time"
)

func running () {
	var times int

	for {
		times++
		fmt.Println("tick", times)

		// 延时1秒
        time.Sleep(time.Second)
	}
}

func main () {
	go running()
	var input string
	fmt.Scanln(&input)
}
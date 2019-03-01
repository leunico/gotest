package main

import (
	"fmt"
	"os"
)

func dd(a...interface{}) {
	fmt.Println(a...)
	os.Exit(1)
}

// 01.从排序数组中删除重复项
func removeDuplicates(nums []int) int {
    if len(nums) == 0 {
        return 0
    }
	
	i := 0
	// result := nums[0:1]
    for j := 1; j<len(nums); j++ {
        if nums[j] != nums[i] {
			i++
			nums[i] = nums[j]
			// result = append(result, nums[j])
        }
	}
	
	// fmt.Println(nums[0:i+1])
    return i + 1
}

// 122.买卖股票的最佳时机 II
func maxProfit(prices []int) int {
	// var i, maxprofit, valley, peak int
	// for i < len(prices) - 1 {
	// 	for i < len(prices) - 1 && prices[i] >= prices[i+1] {
	// 		i++
	// 	}
	// 	valley = prices[i]
	// 	for i < len(prices) - 1 && prices[i] <= prices[i+1] {
	// 		i++
	// 	}
	// 	peak = prices[i]
	// 	maxprofit += peak - valley
	// }

	maxprofit := 0
	for i := 1; i < len(prices); i++ {
		if prices[i] > prices[i-1] {
			maxprofit += prices[i] - prices[i-1]
		}
	}

	return maxprofit
}

// 189.旋转数组
func rotate(nums []int, k int)  {
	n := len(nums)
	k %= n
	reverse(nums, 0, n - 1)
    reverse(nums, 0, k - 1)
    reverse(nums, k, n - 1)

}
func reverse(nums []int, start int, end int) {
    for start < end {
		temp := nums[start]
		nums[start] = nums[end]
		start++
		nums[end] = temp
		end--
    }
}

// 217.存在重复元素
func containsDuplicate(nums []int) bool {
    // for i := 1; i < len(nums); i++ {
    //     for j := i-1; j >= 0; j-- {
	// 		fmt.Println(nums[i], nums[j])
    //         if nums[i] > nums[j] { // todo 条件是有序吧？
    //             break
    //         } else if nums[i] == nums[j] {
    //             return true
    //         }
    //     }
	// }
	
	hash := make(map[int]bool, 0)
	for _, v := range nums {
		if hash[v] == true {
			return true
		} else {
			hash[v] = true
		}
	}
    
    return false
}

// --main--

func main() {
	// 01
	// nums := []int{1,2,2,3,3}
	// fmt.Println(removeDuplicates(nums))

	// 122
	// nums := []int{7,1,5,3,6,4,8,9,2,6,3,4}
	// fmt.Println(maxProfit(nums))

	// 189
	// nums := []int{1,2,3,4,5,6,7}
	// rotate(nums, 3)
	// fmt.Println(nums)

	// 217
	nums := []int{8,7,3,2,1,8}
	fmt.Println(containsDuplicate(nums))
}